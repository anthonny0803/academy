<?php

namespace App\Domains\Academics\Services\Subjects;

use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Repositories\SubjectRepository;
use Illuminate\Support\Facades\DB;

class DeleteSubjectService
{
    public function __construct(
        private SubjectRepository $subjectRepository
    ) {}

    public function handle(Subject $subject): void
    {
        DB::transaction(function () use ($subject) {
            if ($subject->sectionSubjectTeachers()->exists()) {
                throw new \Exception('No se puede eliminar una asignatura con asignaciones en secciones.');
            }

            if ($subject->teachers()->exists()) {
                throw new \Exception('No se puede eliminar una asignatura con profesores asignados.');
            }

            $this->subjectRepository->delete($subject);
        });
    }
}
