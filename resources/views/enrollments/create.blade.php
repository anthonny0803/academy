<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
                        Asignar Nueva Inscripción
                    </h1>

                    <!-- Información del estudiante (readonly) -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Estudiante:</h2>
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>{{ $student->user->name }} {{ $student->user->last_name }}</strong><br>
                            Código: {{ $student->student_code }}<br>
                            Documento: {{ $student->user->document_id ?? 'Sin documento' }}
                        </p>
                    </div>

                    <!-- Inscripciones actuales del estudiante -->
                    @if ($student->enrollments->where('status', 'activo')->count() > 0)
                        <div
                            class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-600">
                            <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">
                                Inscripciones Activas Actuales:
                            </h3>
                            <ul class="text-sm text-blue-700 dark:text-blue-300 list-disc list-inside">
                                @foreach ($student->enrollments->where('status', 'activo') as $activeEnrollment)
                                    <li>
                                        {{ $activeEnrollment->section->name }}
                                        ({{ $activeEnrollment->section->academicPeriod->name }})
                                    </li>
                                @endforeach
                            </ul>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                                El estudiante puede estar inscrito en múltiples períodos simultáneamente (ej: curso
                                regular + curso adicional).
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('students.enrollments.store', $student) }}">
                        @csrf

                        <div class="flex flex-wrap -mx-2 mb-4">
                            {{-- Período Académico --}}
                            <div class="w-full sm:w-1/2 px-2 mb-4">
                                <x-input-label for="academic_period_id" :value="__('Período Académico *')" />
                                <select id="academic_period_id"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
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

                            {{-- Sección --}}
                            <div class="w-full sm:w-1/2 px-2 mb-4">
                                <x-input-label for="section_id" :value="__('Sección *')" />
                                <select id="section_id" name="section_id"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required disabled>
                                    <option value="">Primero selecciona un período</option>
                                </select>
                                <x-input-error :messages="$errors->get('section_id')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                href="{{ route('students.show', $student) }}">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Inscribir Estudiante') }}
                            </x-primary-button>
                        </div>
                    </form>

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

                            if (periodSelect.value) {
                                periodSelect.dispatchEvent(new Event('change'));
                            }
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
