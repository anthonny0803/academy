<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div
                class="relative overflow-hidden bg-gradient-to-r from-amber-500 via-amber-500 to-orange-600 dark:from-amber-700 dark:via-amber-700 dark:to-orange-800 rounded-2xl shadow-xl mb-8">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-8">
                    {{-- Back link --}}
                    <a href="{{ route('grade-columns.index', $sectionSubjectTeacher) }}"
                        class="inline-flex items-center gap-2 text-amber-100 hover:text-white transition-colors mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Configurar Evaluaciones
                    </a>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl border border-white/30">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">Registro de Calificaciones</h1>
                                <div class="flex flex-wrap items-center gap-2 mt-2 text-amber-100">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-white/20">
                                        {{ $sectionSubjectTeacher->subject->name }}
                                    </span>
                                    <span class="text-amber-200">•</span>
                                    <span>{{ $sectionSubjectTeacher->section->name }}</span>
                                    <span class="text-amber-200">•</span>
                                    <span>{{ $sectionSubjectTeacher->section->academicPeriod->name }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Save Status --}}
                        <div id="saveStatus" class="hidden">
                            <span
                                class="saving hidden inline-flex items-center gap-2 px-4 py-2 bg-yellow-500/20 backdrop-blur-sm text-white rounded-xl border border-yellow-400/30">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Guardando...
                            </span>
                            <span
                                class="saved hidden inline-flex items-center gap-2 px-4 py-2 bg-green-500/20 backdrop-blur-sm text-white rounded-xl border border-green-400/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Guardado
                            </span>
                            <span
                                class="error hidden inline-flex items-center gap-2 px-4 py-2 bg-red-500/20 backdrop-blur-sm text-white rounded-xl border border-red-400/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Error
                            </span>
                        </div>
                    </div>

                    {{-- Teacher info --}}
                    <div class="mt-4 flex items-center gap-2 text-amber-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Prof: {{ $sectionSubjectTeacher->teacher->user->full_name }}</span>
                    </div>
                </div>
            </div>

            {{-- Info Card --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="p-4 sm:p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-6 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 dark:text-gray-400">Rango de notas:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $minGrade }} -
                                    {{ $maxGrade }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 dark:text-gray-400">Aprobación:</span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                    {{ $passingGrade }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            <span>Enter o Tab para avanzar</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Card --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-900/50 z-10">
                                    Código
                                </th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider sticky left-[80px] bg-gray-50 dark:bg-gray-900/50 z-10">
                                    Estudiante
                                </th>
                                @foreach ($gradeColumns as $column)
                                    <th class="px-3 py-4 text-center min-w-[110px]" title="{{ $column->observation }}">
                                        <div class="text-xs text-amber-600 dark:text-amber-400 font-bold">
                                            {{ number_format($column->weight, 0) }}%</div>
                                        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 mt-1">
                                            {{ $column->name }}</div>
                                    </th>
                                @endforeach
                                <th
                                    class="px-4 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider bg-amber-50 dark:bg-amber-900/20">
                                    Promedio
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($enrollments as $enrollment)
                                @php
                                    $studentGrades = $gradesByEnrollment[$enrollment->id] ?? [];
                                    $average = $sectionSubjectTeacher->calculateStudentAverage($enrollment->id);
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                    data-enrollment="{{ $enrollment->id }}">
                                    <td
                                        class="px-4 py-3 whitespace-nowrap sticky left-0 bg-white dark:bg-gray-800 z-10">
                                        <span class="text-sm text-gray-500 dark:text-gray-400 font-mono">
                                            {{ $enrollment->student->student_code }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-3 whitespace-nowrap sticky left-[80px] bg-white dark:bg-gray-800 z-10">
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            {{ $enrollment->student->user->full_name }}
                                        </span>
                                    </td>
                                    @foreach ($gradeColumns as $column)
                                        @php
                                            $grade = $studentGrades[$column->id] ?? null;
                                            $gradeValue = $grade?->value ?? '';
                                            $gradeId = $grade?->id ?? '';
                                        @endphp
                                        <td class="px-2 py-2 text-center">
                                            <input type="number"
                                                class="grade-input w-full px-2 py-2 text-center text-sm font-medium bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200"
                                                data-enrollment-id="{{ $enrollment->id }}"
                                                data-column-id="{{ $column->id }}"
                                                data-grade-id="{{ $gradeId }}"
                                                data-original="{{ $gradeValue }}" value="{{ $gradeValue }}"
                                                min="{{ $minGrade }}" max="{{ $maxGrade }}" step="0.01"
                                                autocomplete="off">
                                        </td>
                                    @endforeach
                                    <td class="px-4 py-3 whitespace-nowrap text-center bg-amber-50 dark:bg-amber-900/20 average-cell"
                                        data-enrollment="{{ $enrollment->id }}">
                                        @if ($average !== null)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $average >= $passingGrade ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                                {{ number_format($average, 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($gradeColumns) + 3 }}" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">No hay estudiantes
                                                inscritos en esta sección</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($enrollments->count() > 0)
                            <tfoot>
                                <tr class="bg-gray-50 dark:bg-gray-900/50">
                                    <td class="px-4 py-3 font-bold text-gray-900 dark:text-white sticky left-0 bg-gray-50 dark:bg-gray-900/50"
                                        colspan="2">
                                        Ponderación
                                    </td>
                                    @foreach ($gradeColumns as $column)
                                        <td class="px-3 py-3 text-center">
                                            <span class="text-sm font-semibold text-amber-600 dark:text-amber-400">
                                                {{ number_format($column->weight, 0) }}%
                                            </span>
                                        </td>
                                    @endforeach
                                    <td class="px-4 py-3 text-center bg-amber-50 dark:bg-amber-900/20">
                                        <span class="text-sm font-bold text-amber-700 dark:text-amber-400">100%</span>
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                {{-- Legend --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <div class="flex flex-wrap items-center gap-6 text-sm">
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            <span class="text-gray-600 dark:text-gray-400">Aprobado (≥ {{ $passingGrade }})</span>
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                            <span class="text-gray-600 dark:text-gray-400">Reprobado (&lt; {{ $passingGrade }})</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="mt-8 flex justify-center gap-6">
                <a href="{{ route('grade-columns.index', $sectionSubjectTeacher) }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Configurar Evaluaciones
                </a>
                <a href="{{ route('sections.show', $sectionSubjectTeacher->section_id) }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver a Sección
                </a>
            </div>

        </div>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sstId = {{ $sectionSubjectTeacher->id }};
            const minGrade = {{ $minGrade }};
            const maxGrade = {{ $maxGrade }};
            const passingGrade = {{ $passingGrade }};
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            const inputs = document.querySelectorAll('.grade-input');
            const inputsArray = Array.from(inputs);

            inputsArray.forEach((input, index) => {
                let savedByKeydown = false;

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === 'Tab') {
                        e.preventDefault();

                        const nextInput = inputsArray[index + 1];
                        const value = this.value.trim();
                        const original = this.dataset.original;

                        if (value !== original && value !== '') {
                            savedByKeydown = true;
                            saveGrade(this);
                        }

                        if (nextInput) {
                            nextInput.focus();
                            nextInput.select();
                        } else {
                            this.blur();
                        }
                    }
                });

                input.addEventListener('focus', function() {
                    savedByKeydown = false;
                    this.select();
                });

                input.addEventListener('blur', function() {
                    if (savedByKeydown) {
                        savedByKeydown = false;
                        return;
                    }

                    const value = this.value.trim();
                    const original = this.dataset.original;

                    if (value !== original && value !== '') {
                        saveGrade(this);
                    }
                });

                input.addEventListener('change', function() {
                    const value = parseFloat(this.value);
                    if (value < minGrade || value > maxGrade) {
                        this.classList.add('border-red-500', 'ring-red-500');
                        showStatus('error');
                    } else {
                        this.classList.remove('border-red-500', 'ring-red-500');
                    }
                });
            });

            function saveGrade(input) {
                const enrollmentId = input.dataset.enrollmentId;
                const columnId = input.dataset.columnId;
                const gradeId = input.dataset.gradeId;
                const value = parseFloat(input.value);

                if (isNaN(value) || value < minGrade || value > maxGrade) {
                    input.classList.add('border-red-500', 'ring-red-500');
                    showStatus('error');
                    return;
                }

                showStatus('saving');
                input.classList.remove('border-red-500', 'ring-red-500', 'border-green-500', 'ring-green-500');
                input.classList.add('border-yellow-500', 'ring-yellow-500');

                const url = gradeId ?
                    `/grades/${gradeId}` :
                    `/grade-columns/${columnId}/grades`;

                const method = gradeId ? 'PUT' : 'POST';

                const body = gradeId ?
                    {
                        value: value
                    } :
                    {
                        enrollment_id: enrollmentId,
                        value: value
                    };

                fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(body)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            input.classList.remove('border-yellow-500', 'ring-yellow-500');
                            input.classList.add('border-green-500', 'ring-green-500');
                            input.dataset.original = value;

                            if (data.grade && data.grade.id) {
                                input.dataset.gradeId = data.grade.id;
                            }

                            showStatus('saved');
                            updateAverage(enrollmentId);

                            setTimeout(() => {
                                input.classList.remove('border-green-500', 'ring-green-500');
                            }, 1500);
                        } else {
                            input.classList.remove('border-yellow-500', 'ring-yellow-500');
                            input.classList.add('border-red-500', 'ring-red-500');
                            showStatus('error');
                            alert(data.message || 'Error al guardar');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        input.classList.remove('border-yellow-500', 'ring-yellow-500');
                        input.classList.add('border-red-500', 'ring-red-500');
                        showStatus('error');
                    });
            }

            function updateAverage(enrollmentId) {
                const row = document.querySelector(`tr[data-enrollment="${enrollmentId}"]`);
                const gradeInputs = row.querySelectorAll('.grade-input');
                const averageCell = document.querySelector(`.average-cell[data-enrollment="${enrollmentId}"]`);

                let weightedSum = 0;
                let totalWeight = 0;

                gradeInputs.forEach(input => {
                    const value = parseFloat(input.value);
                    if (!isNaN(value)) {
                        const columnId = input.dataset.columnId;
                        const weight = getColumnWeight(columnId);
                        weightedSum += value * weight;
                        totalWeight += weight;
                    }
                });

                if (totalWeight > 0) {
                    const average = weightedSum / totalWeight;
                    const isApproved = average >= passingGrade;
                    const colorClasses = isApproved ?
                        'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                        'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400';
                    averageCell.innerHTML =
                        `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold ${colorClasses}">${average.toFixed(2)}</span>`;
                } else {
                    averageCell.innerHTML = '<span class="text-gray-400 dark:text-gray-500">—</span>';
                }
            }

            function getColumnWeight(columnId) {
                const weights = {
                    @foreach ($gradeColumns as $column)
                        {{ $column->id }}: {{ $column->weight }},
                    @endforeach
                };
                return weights[columnId] || 0;
            }

            function showStatus(status) {
                const container = document.getElementById('saveStatus');
                container.classList.remove('hidden');
                container.querySelector('.saving').classList.add('hidden');
                container.querySelector('.saved').classList.add('hidden');
                container.querySelector('.error').classList.add('hidden');
                container.querySelector('.' + status).classList.remove('hidden');

                if (status === 'saved') {
                    setTimeout(() => {
                        container.classList.add('hidden');
                    }, 2000);
                }
            }
        });
    </script>
</x-app-layout>
