<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
                        Retirar Estudiante
                    </h1>

                    {{-- Alerta de advertencia --}}
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                    Acción Irreversible
                                </h3>
                                <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                                    Esta acción retirará al estudiante de <strong>TODAS</strong> sus inscripciones activas 
                                    y desactivará su perfil. El estudiante permanecerá en el sistema pero ya no podrá 
                                    ser inscrito hasta que se reactive manualmente.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Información del Estudiante --}}
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
                            Información del Estudiante
                        </h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Código</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $student->student_code }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Nombre</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $student->full_name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Inscripciones Activas que serán afectadas --}}
                    @if($activeEnrollments->isNotEmpty())
                        <div class="mb-6">
                            <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Inscripciones que serán marcadas como "Retirado":
                            </h2>
                            <div class="space-y-2">
                                @foreach($activeEnrollments as $enrollment)
                                    <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $enrollment->section->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $enrollment->section->academicPeriod->name }}
                                            </p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded">
                                            Activo → Retirado
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Formulario --}}
                    <form action="{{ route('students.withdraw', $student) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-6">
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Motivo del Retiro <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="reason" 
                                id="reason" 
                                rows="4"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 @error('reason') border-red-500 @enderror"
                                placeholder="Ej: Abandono voluntario, Expulsión disciplinaria, Cambio de institución, Motivos personales..."
                                required
                            >{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Este motivo quedará registrado en el historial del estudiante.
                            </p>
                        </div>

                        {{-- Confirmación --}}
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       id="confirm_withdraw"
                                       class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700"
                                       required>
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Confirmo que deseo retirar a este estudiante de todas sus inscripciones activas
                                </span>
                            </label>
                        </div>

                        {{-- Botones --}}
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('students.show', $student) }}"
                               class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                Confirmar Retiro
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>