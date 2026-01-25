<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-violet-600 via-violet-600 to-purple-700 dark:from-violet-800 dark:via-violet-800 dark:to-purple-900 rounded-2xl shadow-xl mb-8">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-8">
                    {{-- Back link --}}
                    <a href="{{ route('students.show', $student) }}" class="inline-flex items-center gap-2 text-violet-200 hover:text-white transition-colors mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver al estudiante
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Asignar Nueva Inscripción</h1>
                    <p class="mt-2 text-violet-100">Inscribe al estudiante en una sección académica</p>
                </div>
            </div>

            {{-- Student Info Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Estudiante
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            {{ strtoupper(substr($student->user->name, 0, 1) . substr($student->user->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $student->user->name }} {{ $student->user->last_name }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Código: <span class="font-mono">{{ $student->student_code }}</span>
                                @if($student->user->document_id)
                                    · Doc: {{ $student->user->document_id }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active Enrollments Alert --}}
            @if ($student->enrollments->where('status', 'activo')->count() > 0)
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                Inscripciones Activas Actuales:
                            </h4>
                            <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                                @foreach ($student->enrollments->where('status', 'activo') as $activeEnrollment)
                                    <li class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                        {{ $activeEnrollment->section->name }}
                                        <span class="text-blue-500 dark:text-blue-400">({{ $activeEnrollment->section->academicPeriod->name }})</span>
                                    </li>
                                @endforeach
                            </ul>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-3">
                                El estudiante puede estar inscrito en múltiples períodos simultáneamente (ej: curso regular + curso adicional).
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-sm font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Datos de Inscripción
                    </h3>
                </div>

                <form method="POST" action="{{ route('students.enrollments.store', $student) }}">
                    @csrf
                    <div class="p-6 space-y-6">
                        {{-- Academic Period --}}
                        <div class="space-y-1">
                            <label for="academic_period_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Período Académico <span class="text-red-500">*</span>
                            </label>
                            <select id="academic_period_id"
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200"
                                    required>
                                <option value="">Selecciona un período</option>
                                @foreach ($academicPeriods as $period)
                                    <option value="{{ $period->id }}" @selected(old('academic_period_id') == $period->id)>
                                        {{ $period->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Puede ser un curso regular o un curso adicional (francés, alemán, etc.)
                            </p>
                        </div>

                        {{-- Section --}}
                        <div class="space-y-1">
                            <label for="section_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Sección <span class="text-red-500">*</span>
                            </label>
                            <select id="section_id" name="section_id"
                                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
                                    required disabled>
                                <option value="">Primero selecciona un período</option>
                            </select>
                            @error('section_id')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                        <a href="{{ route('students.show', $student) }}"
                           class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-violet-500/25 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Inscribir Estudiante
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const academicPeriodsData = {!! json_encode(
                $academicPeriods->map(function ($period) {
                    return [
                        'id' => $period->id,
                        'sections' => $period->sections->map(fn($s) => ['id' => $s->id, 'name' => $s->name]),
                    ];
                }),
            ) !!};

            const periodSelect = document.getElementById('academic_period_id');
            const sectionSelect = document.getElementById('section_id');
            const oldSectionId = '{{ old('section_id') }}';

            periodSelect.addEventListener('change', function() {
                const periodId = parseInt(this.value);
                sectionSelect.innerHTML = '<option value="">Selecciona una sección</option>';
                sectionSelect.disabled = !periodId;

                if (periodId) {
                    const period = academicPeriodsData.find(p => p.id === periodId);
                    if (period && period.sections) {
                        period.sections.forEach(section => {
                            const option = document.createElement('option');
                            option.value = section.id;
                            option.textContent = section.name;
                            if (section.id == oldSectionId) {
                                option.selected = true;
                            }
                            sectionSelect.appendChild(option);
                        });
                    }
                }
            });

            // Trigger initial if there's a value
            if (periodSelect.value) {
                periodSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>