<?php

namespace App\Domains\Academics\Services\SectionSubjectTeacher;

use App\Domains\Academics\Exceptions\TeacherNotQualifiedForSubjectException;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\SubjectTeacher;
use Illuminate\Support\Facades\DB;

class StoreSectionSubjectTeacherService
{
    public function handle(array $data): SectionSubjectTeacher
    {
        return DB::transaction(function () use ($data) {
            $canTeach = SubjectTeacher::where('teacher_id', $data['teacher_id'])
                ->where('subject_id', $data['subject_id'])
                ->exists();

            if (! $canTeach) {
                throw TeacherNotQualifiedForSubjectException::make();
            }

            if (isset($data['is_primary']) && $data['is_primary']) {
                SectionSubjectTeacher::primaryFor($data['section_id'], $data['subject_id'])
                    ->update(['is_primary' => false]);
            }

            $sst = SectionSubjectTeacher::create([
                'section_id' => $data['section_id'],
                'subject_id' => $data['subject_id'],
                'teacher_id' => $data['teacher_id'],
                'is_primary' => $data['is_primary'] ?? false,
                'status' => $data['status'],
            ]);

            return $sst->fresh(['section', 'subject', 'teacher']);
        });
    }
}
