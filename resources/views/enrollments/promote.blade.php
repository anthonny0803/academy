<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
                        Promover Estudiante a Siguiente Nivel
                    </h1>

                    <!-- Información actual -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Inscripción actual:</h2>
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Estudiante:</strong> {{ $enrollment->student->user->name }}
                            {{ $enrollment->student->user->last_name }}<br>
                            <strong>Código:</strong> {{ $enrollment->student->student_code }}<br>
                            <strong>Sección actual:</strong> {{ $enrollment->section->name }}<br>
                            <strong>Período académico:</strong> {{ $academicPeriod->name }}
                        </p>
                    </div>

                    <!-- Información sobre promoción -->
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 dark:border-blue-600">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    <strong>Promoción:</strong> El estudiante avanzará a otra sección dentro del mismo 
                                    período académico ({{ $academicPeriod->name }}). La inscripción actual se marcará 
                                    como "promovido" y se creará una nueva inscripción activa en la sección destino.
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($sections->count() > 0)
                        <form method="POST" action="{{ route('enrollments.promote', $enrollment) }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-4">
                                <x-input-label for="section_id" :value="__('Nueva Sección *')" />
                                <select id="section_id" name="section_id"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">Selecciona la sección destino</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}" @selected(old('section_id') == $section->id)>
                                            {{ $section->name }}
                                            @if($section->description)
                                                - {{ $section->description }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('section_id')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Secciones disponibles en el período "{{ $academicPeriod->name }}".
                                </p>
                            </div>

                            {{-- Botones --}}
                            <div class="flex items-center justify-end mt-6">
                                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                    href="{{ route('enrollments.show', $enrollment) }}">
                                    {{ __('Cancelar') }}
                                </a>
                                <x-primary-button class="ms-4 bg-green-600 hover:bg-green-700">
                                    {{ __('Promover Estudiante') }}
                                </x-primary-button>
                            </div>
                        </form>
                    @else
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600">
                            <p class="text-yellow-800 dark:text-yellow-200">
                                No hay otras secciones disponibles en el período académico "{{ $academicPeriod->name }}" 
                                para realizar la promoción.
                            </p>
                            <a href="{{ route('enrollments.show', $enrollment) }}"
                                class="underline text-sm mt-2 inline-block text-yellow-900 dark:text-yellow-100">
                                Volver a detalles
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>