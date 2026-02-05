<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header Card --}}
            <div
                class="relative overflow-hidden bg-gradient-to-r from-violet-600 via-violet-600 to-purple-700 dark:from-violet-800 dark:via-violet-800 dark:to-purple-900 rounded-2xl shadow-xl">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-8">
                    {{-- Back link --}}
                    <a href="{{ route('sections.index') }}"
                        class="inline-flex items-center gap-2 text-violet-200 hover:text-white transition-colors mb-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Ir a Secciones
                    </a>

                    {{-- Section info --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                        <div
                            class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl border border-white/30">
                            <span
                                class="text-2xl font-bold text-white">{{ strtoupper(substr($section->name, 0, 2)) }}</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $section->name }}</h1>
                                @if ($section->is_active)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-100 border border-green-400/30">
                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                                        Activo
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-500/20 text-gray-200 border border-gray-400/30">
                                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-violet-100">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $section->academicPeriod->name }}
                                </span>
                            </div>
                        </div>

                        {{-- Quick action --}}
                        <a href="{{ route('sections.assignments', $section) }}"
                            class="inline-flex items-center gap-2 px-5 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white font-semibold rounded-xl border border-white/20 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Gestionar Asignaturas
                        </a>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            @if ($section->description)
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        Descripción</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $section->description }}</p>
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Capacity Card --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Estudiantes Inscritos</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                                {{ $enrolledCount }} <span class="text-lg text-gray-400">/
                                    {{ $section->capacity }}</span>
                            </p>
                        </div>
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    {{-- Progress bar --}}
                    @php
                        $percentage =
                            $section->capacity > 0 ? min(100, ($enrolledCount / $section->capacity) * 100) : 0;
                        $barColor =
                            $percentage >= 90 ? 'bg-red-500' : ($percentage >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                    @endphp
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="{{ $barColor }} h-2 rounded-full transition-all duration-500"
                                style="width: {{ $percentage }}%"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ number_format($percentage, 0) }}%
                            de capacidad</p>
                    </div>
                </div>

                {{-- Subjects Card --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Asignaturas Asignadas</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                                {{ $section->sectionSubjectTeachers->count() }}</p>
                        </div>
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-violet-500 to-purple-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Students List --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3
                        class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Estudiantes Inscritos
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Estudiante</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Código</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Estado Inscripción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($section->enrollments as $enrollment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full flex items-center justify-center text-white font-semibold text-xs shadow-md">
                                                {{ strtoupper(substr($enrollment->student->user->name, 0, 1) . substr($enrollment->student->user->last_name, 0, 1)) }}
                                            </div>
                                            <a href="{{ route('students.show', $enrollment->student) }}"
                                                class="font-medium text-gray-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                                {{ $enrollment->student->user->full_name }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="text-sm font-mono text-gray-600 dark:text-gray-300">{{ $enrollment->student->student_code }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">No hay estudiantes
                                                inscritos</p>
                                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Los estudiantes
                                                aparecerán aquí cuando sean inscritos en esta sección</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Subjects List --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3
                        class="text-sm font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Asignaturas y Profesores
                    </h3>
                    <a href="{{ route('sections.assignments', $section) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/20 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Gestionar
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Asignatura</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Profesor</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Principal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($section->sectionSubjectTeachers as $sst)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 bg-gradient-to-br from-purple-500 to-violet-500 rounded-lg flex items-center justify-center text-white font-semibold text-xs shadow-md">
                                                {{ strtoupper(substr($sst->subject->name, 0, 2)) }}
                                            </div>
                                            <span
                                                class="font-medium text-gray-900 dark:text-white">{{ $sst->subject->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full flex items-center justify-center text-white font-semibold text-xs shadow-md">
                                                {{ strtoupper(substr($sst->teacher->user->name, 0, 1) . substr($sst->teacher->user->last_name, 0, 1)) }}
                                            </div>
                                            <a href="{{ route('teachers.show', $sst->teacher) }}"
                                                class="text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                                {{ $sst->teacher->user->full_name }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($sst->is_primary)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Sí
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">No hay asignaturas
                                                asignadas</p>
                                            <a href="{{ route('sections.assignments', $section) }}"
                                                class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-lg transition-colors text-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Agregar Asignaturas
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
