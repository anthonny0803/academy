<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Retirar Estudiante
            </h2>
            <a href="{{ route('enrollments.show', $enrollment) }}"
                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">
                ← Volver a inscripción
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Información del estudiante --}}
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                            Datos del Estudiante
                        </h3>
                        <dl class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Nombre</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $enrollment->student->user->full_name }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Código</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $enrollment->student->student_code }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Sección</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $enrollment->section->name }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Período</dt>
                                <dd class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $enrollment->section->academicPeriod->name }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Advertencia --}}
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-red-800 dark:text-red-200">
                                    Acción de Retiro
                                </h4>
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                                    El estudiante será retirado de esta inscripción. Esta acción se usa para casos de:
                                </p>
                                <ul class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside">
                                    <li>Abandono voluntario del estudiante</li>
                                    <li>Expulsión disciplinaria</li>
                                    <li>Estudiante que nunca asistió a clases</li>
                                    <li>Problemas de salud que impiden continuar</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Formulario --}}
                    <form action="{{ route('enrollments.withdraw', $enrollment) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-6">
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Motivo del retiro <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="reason"
                                name="reason"
                                rows="4"
                                required
                                maxlength="500"
                                placeholder="Describa el motivo por el cual el estudiante es retirado..."
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500"
                            >{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Máximo 500 caracteres. Este motivo quedará registrado en el historial.
                            </p>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('enrollments.show', $enrollment) }}"
                                class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                                onclick="return confirm('¿Está seguro de retirar a este estudiante? Esta acción no se puede deshacer.')">
                                Confirmar Retiro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>