<?php

namespace App\Models;

use App\Contracts\HasEntityName;
use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model implements HasEntityName
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'section_id',
        'status',
        'passed',
    ];

    protected $casts = [
        'status' => 'string',
        'passed' => 'boolean',
    ];

    // Contracts Implementation

    public function getEntityName(): string
    {
        return 'Inscripción';
    }

    // Relationships

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // Query Scopes

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->whereHas('student.user', function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%");
        })->orWhereHas('student', function ($q) use ($term) {
            $q->where('student_code', 'like', "%{$term}%");
        });
    }

    public function scopeForStudent(Builder $query, int $studentId): Builder
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForSection(Builder $query, int $sectionId): Builder
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', EnrollmentStatus::Active->value);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', EnrollmentStatus::Completed->value);
    }

    public function scopeWithdrawn(Builder $query): Builder
    {
        return $query->where('status', EnrollmentStatus::Withdrawn->value);
    }

    public function scopePassed(Builder $query): Builder
    {
        return $query->where('passed', true);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('passed', false);
    }

    // Helper Methods - Estado

    public function isActive(): bool
    {
        return $this->status === EnrollmentStatus::Active->value;
    }

    public function isCompleted(): bool
    {
        return $this->status === EnrollmentStatus::Completed->value;
    }

    public function isWithdrawn(): bool
    {
        return $this->status === EnrollmentStatus::Withdrawn->value;
    }

    public function isTransferred(): bool
    {
        return $this->status === EnrollmentStatus::Transferred->value;
    }

    public function isPromoted(): bool
    {
        return $this->status === EnrollmentStatus::Promoted->value;
    }

    public function hasPassed(): ?bool
    {
        return $this->passed;
    }

    // Helper Methods - Calificaciones

    public function getGradesForSubject(int $subjectId)
    {
        return $this->grades()
            ->whereHas('gradeColumn.sectionSubjectTeacher', function ($q) use ($subjectId) {
                $q->where('subject_id', $subjectId);
            })
            ->get();
    }

    public function getAverageForSubject(int $subjectId): ?float
    {
        $average = $this->grades()
            ->whereHas('gradeColumn.sectionSubjectTeacher', function ($q) use ($subjectId) {
                $q->where('subject_id', $subjectId);
            })
            ->avg('value');

        return $average ? round($average, 2) : null;
    }

    public function getOverallAverage(): ?float
    {
        $average = $this->grades()->avg('value');
        return $average ? round($average, 2) : null;
    }

    public function getAvailableSubjects()
    {
        return $this->section->sectionSubjectTeachers()
            ->active()
            ->with('subject')
            ->get()
            ->pluck('subject');
    }

    /**
     * Calcula el promedio ponderado para una asignatura específica
     */
    public function getWeightedAverageForAssignment(int $sstId): ?float
    {
        $grades = $this->grades()
            ->whereHas('gradeColumn', function ($q) use ($sstId) {
                $q->where('section_subject_teacher_id', $sstId);
            })
            ->with('gradeColumn')
            ->get();

        if ($grades->isEmpty()) {
            return null;
        }

        $totalWeight = $grades->sum(fn($g) => $g->gradeColumn->weight);
        
        if ($totalWeight == 0) {
            return null;
        }

        $weightedSum = $grades->sum(fn($g) => $g->value * $g->gradeColumn->weight);

        return round($weightedSum / $totalWeight, 2);
    }

    /**
     * Verifica si el estudiante aprobó una asignatura específica
     */
    public function hasPassedAssignment(int $sstId): ?bool
    {
        $average = $this->getWeightedAverageForAssignment($sstId);
        
        if ($average === null) {
            return null; // No hay notas
        }

        $passingGrade = $this->section->academicPeriod->passing_grade ?? 60;
        
        return $average >= $passingGrade;
    }

    /**
     * Verifica si el estudiante tiene TODAS las notas completas
     * para TODAS las asignaturas de su sección
     */
    public function hasAllGradesComplete(): bool
    {
        $assignments = $this->section->sectionSubjectTeachers()
            ->active()
            ->with('gradeColumns')
            ->get();

        foreach ($assignments as $sst) {
            // Verificar que la configuración esté completa (100%)
            if (!$sst->isConfigurationComplete()) {
                return false;
            }

            // Verificar que tenga nota en cada columna
            foreach ($sst->gradeColumns as $column) {
                $hasGrade = $this->grades()
                    ->where('grade_column_id', $column->id)
                    ->exists();

                if (!$hasGrade) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Calcula si el estudiante aprobó TODAS las asignaturas
     */
    public function calculatePassed(): ?bool
    {
        $assignments = $this->section->sectionSubjectTeachers()
            ->active()
            ->get();

        if ($assignments->isEmpty()) {
            return null;
        }

        foreach ($assignments as $sst) {
            $passed = $this->hasPassedAssignment($sst->id);
            
            // Si alguna está sin notas o reprobada, no aprobó
            if ($passed === null || $passed === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtiene detalle de notas faltantes para esta inscripción
     */
    public function getMissingGradesReport(): array
    {
        $missing = [];

        $assignments = $this->section->sectionSubjectTeachers()
            ->active()
            ->with(['subject', 'gradeColumns'])
            ->get();

        foreach ($assignments as $sst) {
            $subjectMissing = [];

            // Verificar configuración incompleta
            if (!$sst->isConfigurationComplete()) {
                $subjectMissing[] = [
                    'type' => 'configuration',
                    'message' => "Configuración incompleta: {$sst->getTotalWeight()}% de 100%",
                ];
            }

            // Verificar notas faltantes
            foreach ($sst->gradeColumns as $column) {
                $hasGrade = $this->grades()
                    ->where('grade_column_id', $column->id)
                    ->exists();

                if (!$hasGrade) {
                    $subjectMissing[] = [
                        'type' => 'grade',
                        'column' => $column->name,
                    ];
                }
            }

            if (!empty($subjectMissing)) {
                $missing[$sst->subject->name] = $subjectMissing;
            }
        }

        return $missing;
    }
}