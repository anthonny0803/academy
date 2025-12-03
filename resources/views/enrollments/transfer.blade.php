<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
                        Transferir Estudiante a Otra Institución
                    </h1>

                    <!-- Información actual -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Inscripción actual:</h2>
                        <p class="text-gray-700 dark:text-gray-300">
                            <strong>Estudiante:</strong> {{ $enrollment->student->user->name }}
                            {{ $enrollment->student->user->last_name }}<br>
                            <strong>Código:</strong> {{ $enrollment->student->student_code }}<br>
                            <strong>Sección:</strong> {{ $enrollment->section->name }}<br>
                            <strong>Período:</strong> {{ $enrollment->section->academicPeriod->name }}
                        </p>
                    </div>

                    <!-- Advertencia -->
                    <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    <strong>Importante:</strong> Esta acción registra que el estudiante se transfiere 
                                    a otra institución educativa. La inscripción actual se marcará como "transferido" 
                                    y el estudiante dejará de estar activo en el sistema.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('enrollments.transfer', $enrollment) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <x-input-label for="reason" :value="__('Motivo de la Transferencia *')" />
                            <textarea id="reason" name="reason" rows="4"
                                class="block mt-1 w-full uppercase text-gray-900 dark:text-gray-300 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-700 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                placeholder="Ej: Cambio de domicilio, traslado a otra ciudad, cambio de institución..."
                                required>{{ old('reason') }}</textarea>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Describe el motivo por el cual el estudiante se transfiere a otra institución.
                            </p>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end mt-6">
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                href="{{ route('enrollments.show', $enrollment) }}">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button class="ms-4 bg-orange-600 hover:bg-orange-700">
                                {{ __('Confirmar Transferencia') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>