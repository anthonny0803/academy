<?php

namespace App\Domains\Academics\Services\SubjectTeacher;

use App\Domains\Academics\Exceptions\SubjectTeacherInUseException;
use App\Domains\Academics\Models\Teacher;
use Illuminate\Support\Facades\DB;

class StoreSubjectTeacherService
{
    public function handle(Teacher $teacher, array $data): void
    {
        $keptSubjectIds = $data['subjects'] ?? [];

        $this->assertRemovedSubjectsAreUnassigned($teacher, $keptSubjectIds);

        DB::transaction(function () use ($teacher, $keptSubjectIds) {
            $teacher->subjects()->sync($keptSubjectIds);
        });
    }

    /**
     * The sync detaches whatever is unchecked, so it needs the same guard the
     * targeted delete has: a subject the teacher still holds assignments in
     * cannot be removed, or those assignments outlive the qualification.
     *
     * @param  array<int, string>  $keptSubjectIds
     */
    private function assertRemovedSubjectsAreUnassigned(Teacher $teacher, array $keptSubjectIds): void
    {
        $subjectsInUse = $teacher->subjects()
            ->whereNotIn('subjects.id', $keptSubjectIds)
            ->whereHas('sectionSubjectTeachers', fn ($query) => $query->where('teacher_id', $teacher->id))
            ->pluck('subjects.name')
            ->all();

        if ($subjectsInUse === []) {
            return;
        }

        throw SubjectTeacherInUseException::forSubjects($subjectsInUse);
    }
}
