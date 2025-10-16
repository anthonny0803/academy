<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
                        Editar Inscripción
                    </h1>

                    <!-- Advertencia sobre edición de inscripción -->
                    <div
                        class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    <strong>Atención:</strong> Edita esta inscripción solo si se cometió un error al
                                    registrarla.<br>
                                    <strong>Para cambios normales:</strong> Usa "Transferir Sección" o "Promover Grado"
                                    desde los detalles de la inscripción.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la inscripción actual -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Inscripción actual:</h2>
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Estudiante:</strong> {{ $enrollment->student->user->name }}
                            {{ $enrollment->student->user->last_name }}<br>
                            <strong>Código:</strong> {{ $enrollment->student->student_code }}<br>
                            <strong>Período:</strong> {{ $enrollment->section->academicPeriod->name }}<br>
                            <strong>Sección actual:</strong> {{ $enrollment->section->name }}<br>
                            <strong>Estado actual:</strong> <span
                                class="font-bold">{{ ucfirst($enrollment->status) }}</span>
                        </p>
                    </div>

                    <form method="POST" action="{{ route('enrollments.update', $enrollment) }}">
                        @csrf
                        @method('PATCH')

                        <div class="flex flex-wrap -mx-2 mb-4">
                            {{-- Cambiar Estado --}}
                            <div class="w-full sm:w-1/2 px-2 mb-4">
                                <x-input-label for="status" :value="__('Estado *')" />
                                <select id="status" name="status"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">Selecciona un estado</option>
                                    @foreach ($statuses as $statusValue)
                                        <option value="{{ $statusValue }}" @selected(old('status', $enrollment->status) === $statusValue)>
                                            {{ ucfirst($statusValue) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            {{-- Cambiar Sección (opcional, solo si fue error) --}}
                            <div class="w-full sm:w-1/2 px-2 mb-4">
                                <x-input-label for="section_id" :value="__('Sección (dejar vacío para no cambiar)')" />
                                <select id="section_id" name="section_id"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">No cambiar sección</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}" @selected(old('section_id') == $section->id)>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Solo cambia la sección si la inscripción se hizo en la sección equivocada por error
                                </p>
                                <x-input-error :messages="$errors->get('section_id')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Motivo del cambio (opcional pero recomendado) --}}
                        <div class="mb-4">
                            <x-input-label for="reason" :value="__('Motivo del cambio (opcional pero recomendado)')" />
                            <textarea id="reason" name="reason" rows="3"
                                class="block mt-1 w-full uppercase text-gray-900 dark:text-gray-300 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-700 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                placeholder="Ej: Se inscribió en la sección equivocada por error administrativo">{{ old('reason') }}</textarea>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                href="{{ route('enrollments.show', $enrollment) }}">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Actualizar Inscripción') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
