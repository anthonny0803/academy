<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header Card --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-violet-600 via-violet-600 to-purple-700 dark:from-violet-800 dark:via-violet-800 dark:to-purple-900 rounded-2xl shadow-xl">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-8">
                    {{-- Back link --}}
                    <a href="{{ route('enrollments.index') }}" class="inline-flex items-center gap-2 text-violet-200 hover:text-white transition-colors mb-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Ir a Inscripciones
                    </a>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl border border-white/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">Detalles de Inscripción</h1>
                                @if ($enrollment->status === 'activo')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-100 border border-green-400/30">
                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                                        Activo
                                    </span>
                                @elseif ($enrollment->status === 'completado')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-500/20 text-blue-100 border border-blue-400/30">
                                        Completado
                                    </span>
                                @elseif ($enrollment->status === 'retirado')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-500/20 text-red-100 border border-red-400/30">
                                        Retirado
                                    </span>
                                @elseif ($enrollment->status === 'transferido')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-500/20 text-amber-100 border border-amber-400/30">
                                        Transferido
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-500/20 text-purple-100 border border-purple-400/30">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-violet-100">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    {{ $enrollment->section->name }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $enrollment->section->academicPeriod->name }}
                                </span>
                            </div>
                        </div>

                        {{-- Actions button --}}
                        @if ($enrollment->status === 'activo')
                            <div class="relative">
                                <button data-dropdown-enrollment="{{ $enrollment->id }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white font-semibold rounded-xl border border-white/20 transition-all duration-300">
                                    <span>Acciones</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div id="dropdown-template-{{ $enrollment->id }}" class="hidden">
                                    <div class="py-2">
                                        <a href="{{ route('enrollments.transfer.form', $enrollment) }}"
                                           class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                            </svg>
                                            Transferir Sección
                                        </a>
                                        <a href="{{ route('enrollments.promote.form', $enrollment) }}"
                                           class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-700 dark:hover:text-green-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                            </svg>
                                            Promover Grado
                                        </a>
                                        <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                                        <button type="button" onclick="openDeleteModal()"
                                                class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Eliminar Inscripción
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid gap-6 lg:grid-cols-2">
                {{-- Student Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Estudiante
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                {{ strtoupper(substr($enrollment->student->user->name, 0, 1) . substr($enrollment->student->user->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $enrollment->student->user->name }} {{ $enrollment->student->user->last_name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $enrollment->student->student_code }}</p>
                            </div>
                        </div>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Documento</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $enrollment->student->user->document_id ?? 'Sin documento' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Representante</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">
                                    {{ $enrollment->student->representative?->user?->name ?? 'Sin representante' }}
                                    {{ $enrollment->student->representative?->user?->last_name ?? '' }}
                                </dd>
                            </div>
                        </dl>
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('students.show', $enrollment->student) }}"
                               class="inline-flex items-center gap-2 text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Ver perfil del estudiante
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Enrollment Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Datos de Inscripción
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Período Académico</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-300">
                                        {{ $enrollment->section->academicPeriod->name }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sección</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white font-medium">{{ $enrollment->section->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Inscripción</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $enrollment->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Última Actualización</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $enrollment->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Grades by Subject --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-sm font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Calificaciones por Asignatura
                    </h3>
                </div>

                @if ($subjectsData->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Asignatura</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Profesor</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Promedio</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Detalles</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($subjectsData as $index => $subject)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-violet-500 rounded-lg flex items-center justify-center text-white font-semibold text-xs shadow-md">
                                                    {{ strtoupper(substr($subject['subject_name'], 0, 2)) }}
                                                </div>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $subject['subject_name'] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ $subject['teacher_name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($subject['average'] !== null)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $subject['average'] >= $passingGrade ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                                    {{ number_format($subject['average'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500 text-sm">Sin notas</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button type="button" onclick="openGradeModal({{ $index }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/20 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Ver detalles
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Legend --}}
                    <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                            <span class="flex items-center gap-1.5">
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                Aprobado (≥ {{ $passingGrade }})
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                                Reprobado (&lt; {{ $passingGrade }})
                            </span>
                        </div>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">No hay asignaturas asignadas a esta sección.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Quick Links --}}
            <div class="flex justify-center gap-6">
                <a href="{{ route('enrollments.index') }}"
                   class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Ir a la Lista
                </a>
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Volver al Panel
                </a>
            </div>

        </div>
    </div>

    {{-- Grade Detail Modals --}}
    @foreach ($subjectsData as $index => $subject)
        <div id="gradeModal-{{ $index }}" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full max-h-[85vh] overflow-hidden border-t-4 border-amber-500">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-violet-500 rounded-lg flex items-center justify-center text-white font-semibold text-sm shadow-md">
                            {{ strtoupper(substr($subject['subject_name'], 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $subject['subject_name'] }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $subject['teacher_name'] }}</p>
                        </div>
                    </div>
                    <button onclick="closeGradeModal({{ $index }})"
                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    @if ($subject['grades_detail']->count() > 0)
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-3 text-left font-semibold">Evaluación</th>
                                    <th class="py-3 text-center font-semibold">Ponderación</th>
                                    <th class="py-3 text-center font-semibold">Calificación</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-900 dark:text-gray-100">
                                @foreach ($subject['grades_detail'] as $grade)
                                    <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="py-3">{{ $grade['column_name'] }}</td>
                                        <td class="py-3 text-center text-gray-500 dark:text-gray-400">{{ number_format($grade['weight'], 0) }}%</td>
                                        <td class="py-3 text-center font-medium">
                                            @if ($grade['value'] !== null)
                                                <span class="{{ $grade['value'] >= $passingGrade ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ number_format($grade['value'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                    <td class="py-4 text-gray-700 dark:text-gray-300 font-bold text-base">Promedio Ponderado</td>
                                    <td class="py-4 text-center text-gray-500 dark:text-gray-400">100%</td>
                                    <td class="py-4 text-center font-bold text-lg">
                                        @if ($subject['average'] !== null)
                                            <span class="{{ $subject['average'] >= $passingGrade ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ number_format($subject['average'], 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">No hay evaluaciones configuradas.</p>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-right">
                    <button onclick="closeGradeModal({{ $index }})"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-xl transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal ELIMINAR --}}
    @if ($enrollment->status === 'activo')
        <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md my-8 border-t-4 border-red-500 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Eliminar Inscripción</h2>
                    </div>
                </div>

                <div class="p-6">
                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                        ¿Estás seguro de que deseas eliminar la inscripción de:
                    </p>
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-xl mb-4">
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ $enrollment->student->user->name }} {{ $enrollment->student->user->last_name }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Sección: {{ $enrollment->section->name }}</p>
                    </div>
                    <p class="text-sm text-red-600 dark:text-red-400">
                        <strong>Advertencia:</strong> Esta acción no se puede deshacer y se perderán todas las calificaciones asociadas.
                    </p>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                    <button type="button" onclick="closeDeleteModal()"
                            class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </button>
                    <form action="{{ route('enrollments.destroy', $enrollment) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-xl shadow-lg shadow-red-500/25 transition-all duration-300">
                            Sí, Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        function openGradeModal(index) {
            document.getElementById('gradeModal-' + index).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeGradeModal(index) {
            document.getElementById('gradeModal-' + index).classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openDeleteModal() {
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
                document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
            }
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id^="gradeModal-"]').forEach(modal => modal.classList.add('hidden'));
                closeDeleteModal();
                document.body.style.overflow = '';
            }
        });

        document.querySelectorAll('[id^="gradeModal-"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        });

        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('click', function(e) {
                if (e.target === this) closeDeleteModal();
            });
        }

        @if ($enrollment->status === 'activo')
            document.addEventListener("DOMContentLoaded", () => {
                const closeAllDropdowns = () => document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());

                document.querySelectorAll("[data-dropdown-enrollment]").forEach(btn => {
                    btn.addEventListener("click", (e) => {
                        e.stopPropagation();
                        const id = btn.dataset.dropdownEnrollment;
                        let existing = document.getElementById("dropdownMenu-" + id);
                        if (existing && !existing.classList.contains("hidden")) { existing.remove(); return; }
                        closeAllDropdowns();

                        const tpl = document.getElementById("dropdown-template-" + id);
                        const clone = tpl.cloneNode(true);
                        clone.id = "dropdownMenu-" + id;
                        clone.classList.remove("hidden");
                        clone.classList.add("dropdown-clone", "fixed", "z-50", "w-52", "bg-white", "dark:bg-gray-800", "border", "border-gray-200", "dark:border-gray-700", "rounded-xl", "shadow-xl");

                        const rect = btn.getBoundingClientRect();
                        clone.style.top = (rect.bottom + 4) + "px";
                        clone.style.left = Math.min(rect.left, window.innerWidth - 220) + "px";
                        document.body.appendChild(clone);
                    });
                });

                document.addEventListener("click", e => {
                    if (!e.target.closest("[data-dropdown-enrollment]") && !e.target.closest(".dropdown-clone")) closeAllDropdowns();
                });
                window.addEventListener("scroll", closeAllDropdowns, true);
                window.addEventListener("resize", closeAllDropdowns);
            });
        @endif
    </script>
</x-app-layout>