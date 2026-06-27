<?php

namespace App\Domains\AI\Services;

use App\Domains\AI\Contracts\AiTextGenerator;
use App\Domains\Grades\Services\Grades\StudentPerformanceSummaryService;
use App\Domains\Students\Models\Student;

class GenerateStudentPerformanceObservationService
{
    private const SYSTEM_PROMPT = 'Eres un orientador académico de una institución escolar. '
        .'Redactas observaciones de desempeño breves, claras y constructivas en español, '
        .'dirigidas al equipo docente. Básate únicamente en los datos proporcionados: no '
        .'inventes calificaciones ni hechos. Mantén un tono profesional y objetivo.';

    public function __construct(
        private AiTextGenerator $generator,
        private StudentPerformanceSummaryService $summaryService,
    ) {}

    public function forStudent(Student $student): string
    {
        $summary = $this->summaryService->forStudent($student);

        return $this->generator->generate($this->buildPrompt($summary), self::SYSTEM_PROMPT);
    }

    private function buildPrompt(array $summary): string
    {
        $data = json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return 'A partir del siguiente resumen de desempeño académico del estudiante (en formato '
            .'JSON), redacta una observación de desempeño en un párrafo breve. Destaca las materias '
            .'aprobadas y reprobadas, el promedio ponderado por materia y las áreas que requieren '
            ."atención.\n\n".$data;
    }
}
