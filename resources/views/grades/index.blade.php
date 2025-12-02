<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h1 class="text-2xl font-bold mb-2 text-white dark:text-white">Registro de Calificaciones</h1>
                    <p class="text-gray-400 mb-4">
                        {{ $sectionSubjectTeacher->subject->name }} -
                        {{ $sectionSubjectTeacher->section->name }}
                        ({{ $sectionSubjectTeacher->section->academicPeriod->name }})
                        <span class="text-gray-500">| Prof: {{ $sectionSubjectTeacher->teacher->user->full_name }}</span>
                    </p>

                    {{-- Info de rango --}}
                    <div class="mb-4 p-3 bg-gray-900 rounded-lg text-sm text-gray-400">
                        <span class="text-white font-medium">Rango de notas:</span>
                        {{ $minGrade }} - {{ $maxGrade }}
                        <span class="mx-2">|</span>
                        <span class="text-white font-medium">Aprobación:</span> {{ $passingGrade }}
                        <span class="mx-2">|</span>
                        <span class="text-green-500">↵ Enter o Tab para avanzar</span>
                    </div>

                    {{-- Contenedor de acciones --}}
                    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">

                        <div class="flex gap-2 flex-wrap items-center">
                            <a href="{{ route('grade-columns.index', $sectionSubjectTeacher) }}"
                                class="underline px-4 py-2 text-sm text-white hover:text-gray-300 rounded-md">
                                Configurar Evaluaciones
                            </a>
                            <a href="{{ route('sections.show', $sectionSubjectTeacher->section_id) }}"
                                class="underline px-4 py-2 text-sm text-white hover:text-gray-300 rounded-md">
                                Volver a Sección
                            </a>
                        </div>

                        <div id="saveStatus" class="text-sm text-gray-400 hidden">
                            <span class="saving hidden">Guardando...</span>
                            <span class="saved hidden text-green-500">✓ Guardado</span>
                            <span class="error hidden text-red-500">✗ Error</span>
                        </div>

                    </div>

                    {{-- Tabla de calificaciones --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left sticky left-0 bg-gray-900 z-10">Estudiante</th>
                                    <th class="py-2 px-4 border-b text-left sticky left-0 bg-gray-900 z-10">Código</th>
                                    @foreach ($gradeColumns as $column)
                                        <th class="py-2 px-3 border-b text-center min-w-[100px]" title="{{ $column->observation }}">
                                            <div class="text-xs text-gray-400">{{ number_format($column->weight, 0) }}%</div>
                                            {{ $column->name }}
                                        </th>
                                    @endforeach
                                    <th class="py-2 px-4 border-b text-center bg-gray-800">Promedio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($enrollments as $enrollment)
                                    @php
                                        $studentGrades = $gradesByEnrollment[$enrollment->id] ?? [];
                                        $average = $sectionSubjectTeacher->calculateStudentAverage($enrollment->id);
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800" data-enrollment="{{ $enrollment->id }}">
                                        <td class="py-2 px-4 border-b sticky left-0 bg-gray-900 font-medium">
                                            {{ $enrollment->student->user->full_name }}
                                        </td>
                                        <td class="py-2 px-4 border-b sticky left-0 bg-gray-900 text-gray-400 text-sm">
                                            {{ $enrollment->student->student_code }}
                                        </td>
                                        @foreach ($gradeColumns as $column)
                                            @php
                                                $grade = $studentGrades[$column->id] ?? null;
                                                $gradeValue = $grade?->value ?? '';
                                                $gradeId = $grade?->id ?? '';
                                            @endphp
                                            <td class="py-1 px-1 border-b text-center">
                                                <input type="number"
                                                    class="grade-input w-full px-2 py-1 text-center bg-gray-800 border border-gray-700 rounded text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                                    data-enrollment-id="{{ $enrollment->id }}"
                                                    data-column-id="{{ $column->id }}"
                                                    data-grade-id="{{ $gradeId }}"
                                                    data-original="{{ $gradeValue }}"
                                                    value="{{ $gradeValue }}"
                                                    min="{{ $minGrade }}"
                                                    max="{{ $maxGrade }}"
                                                    step="0.01"
                                                    autocomplete="off">
                                            </td>
                                        @endforeach
                                        <td class="py-2 px-4 border-b text-center bg-gray-800 font-bold average-cell"
                                            data-enrollment="{{ $enrollment->id }}">
                                            @if ($average !== null)
                                                <span class="{{ $average >= $passingGrade ? 'text-green-500' : 'text-red-500' }}">
                                                    {{ number_format($average, 2) }}
                                                </span>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($gradeColumns) + 3 }}" class="py-4 px-4 text-center text-gray-300">
                                            No hay estudiantes inscritos en esta sección.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($enrollments->count() > 0)
                                <tfoot>
                                    <tr class="bg-gray-900">
                                        <td class="py-2 px-4 border-t font-bold" colspan="2">Ponderación</td>
                                        @foreach ($gradeColumns as $column)
                                            <td class="py-2 px-3 border-t text-center text-gray-400">
                                                {{ number_format($column->weight, 0) }}%
                                            </td>
                                        @endforeach
                                        <td class="py-2 px-4 border-t text-center font-bold bg-gray-800">100%</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    {{-- Leyenda --}}
                    <div class="mt-4 text-sm text-gray-500">
                        <span class="text-green-500">■</span> Aprobado (≥ {{ $passingGrade }})
                        <span class="mx-2">|</span>
                        <span class="text-red-500">■</span> Reprobado (&lt; {{ $passingGrade }})
                    </div>

                </div>
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
            let saveTimeout = null;

            inputs.forEach((input, index) => {
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === 'Tab') {
                        e.preventDefault();
                        const nextInput = inputs[index + 1];
                        if (nextInput) {
                            nextInput.focus();
                            nextInput.select();
                        }
                    }
                });

                input.addEventListener('focus', function() {
                    this.select();
                });

                input.addEventListener('blur', function() {
                    const value = this.value.trim();
                    const original = this.dataset.original;

                    if (value !== original && value !== '') {
                        saveGrade(this);
                    }
                });

                input.addEventListener('change', function() {
                    const value = parseFloat(this.value);
                    if (value < minGrade || value > maxGrade) {
                        this.classList.add('border-red-500');
                        showStatus('error');
                    } else {
                        this.classList.remove('border-red-500');
                    }
                });
            });

            function saveGrade(input) {
                const enrollmentId = input.dataset.enrollmentId;
                const columnId = input.dataset.columnId;
                const gradeId = input.dataset.gradeId;
                const value = parseFloat(input.value);

                if (isNaN(value) || value < minGrade || value > maxGrade) {
                    input.classList.add('border-red-500');
                    showStatus('error');
                    return;
                }

                showStatus('saving');
                input.classList.remove('border-red-500');
                input.classList.add('border-yellow-500');

                const url = gradeId 
                    ? `/grades/${gradeId}` 
                    : `/grade-columns/${columnId}/grades`;

                const method = gradeId ? 'PUT' : 'POST';

                const body = gradeId 
                    ? { value: value } 
                    : { enrollment_id: enrollmentId, value: value };

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
                        input.classList.remove('border-yellow-500');
                        input.classList.add('border-green-500');
                        input.dataset.original = value;

                        if (data.grade && data.grade.id) {
                            input.dataset.gradeId = data.grade.id;
                        }

                        showStatus('saved');
                        updateAverage(enrollmentId);

                        setTimeout(() => {
                            input.classList.remove('border-green-500');
                        }, 1000);
                    } else {
                        input.classList.remove('border-yellow-500');
                        input.classList.add('border-red-500');
                        showStatus('error');
                        alert(data.message || 'Error al guardar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    input.classList.remove('border-yellow-500');
                    input.classList.add('border-red-500');
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
                    const colorClass = average >= passingGrade ? 'text-green-500' : 'text-red-500';
                    averageCell.innerHTML = `<span class="${colorClass}">${average.toFixed(2)}</span>`;
                } else {
                    averageCell.innerHTML = '<span class="text-gray-500">-</span>';
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