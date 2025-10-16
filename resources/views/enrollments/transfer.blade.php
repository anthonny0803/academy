<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
                        Transferir Estudiante a Otra Sección
                    </h1>

                    <!-- Información actual -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Inscripción actual:</h2>
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Estudiante:</strong> {{ $enrollment->student->user->name }}
                            {{ $enrollment->student->user->last_name }}<br>
                            <strong>Sección actual:</strong> {{ $enrollment->section->name }}<br>
                            <strong>Período:</strong> {{ $enrollment->section->academicPeriod->name }}
                        </p>
                    </div>

                    @if ($sections->count() > 0)
                        <form method="POST" action="{{ route('enrollments.transfer', $enrollment) }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-4">
                                <x-input-label for="section_id" :value="__('Nueva Sección *')" />
                                <select id="section_id" name="section_id"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">Selecciona una sección</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}" @selected(old('section_id') == $section->id)>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('section_id')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="reason" :value="__('Motivo de la Transferencia *')" />
                                <textarea id="reason" name="reason" rows="4"
                                    class="block mt-1 w-full uppercase text-gray-900 dark:text-gray-300 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-700 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>{{ old('reason') }}</textarea>
                                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            </div>

                            {{-- Botones --}}
                            <div class="flex items-center justify-end mt-4">
                                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                    href="{{ route('enrollments.show', $enrollment) }}">
                                    {{ __('Cancelar') }}
                                </a>
                                <x-primary-button class="ms-4">
                                    {{ __('Transferir Estudiante') }}
                                </x-primary-button>
                            </div>
                        </form>
                    @else
                        <div
                            class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600">
                            <p class="text-yellow-800 dark:text-yellow-200">
                                No hay otras secciones disponibles en este período académico para realizar la
                                transferencia.
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
