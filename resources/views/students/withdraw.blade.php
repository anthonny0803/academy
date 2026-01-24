<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('students.show', $student) }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al estudiante
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Retirar Estudiante</h1>
            </div>

            {{-- Main Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

                {{-- Danger Alert Header --}}
                <div class="px-6 py-4 bg-red-50 dark:bg-red-900/20 border-b border-red-200 dark:border-red-800">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-100 dark:bg-red-900/50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-red-800 dark:text-red-200">
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

                <div class="p-6 sm:p-8 space-y-6">

                    {{-- Student Info Card --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                        <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                            Información del Estudiante
                        </h2>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                                {{ strtoupper(substr($student->user->name ?? 'E', 0, 1)) }}{{ strtoupper(substr($student->user->last_name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $student->full_name }}</p>
                                <p class="text-sm font-mono text-gray-500 dark:text-gray-400">{{ $student->student_code }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Active Enrollments to be affected --}}
                    @if($activeEnrollments->isNotEmpty())
                        <div>
                            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Inscripciones que serán marcadas como "Retirado":
                            </h2>
                            <div class="space-y-2">
                                @foreach($activeEnrollments as $enrollment)
                                    <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/50 rounded-xl">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">
                                                {{ $enrollment->section->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $enrollment->section->academicPeriod->name }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs font-medium">
                                            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg">
                                                Activo
                                            </span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                            <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">
                                                Retirado
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('students.withdraw', $student) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        {{-- Reason --}}
                        <div class="space-y-2">
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Motivo del Retiro <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="reason" 
                                id="reason" 
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 resize-none @error('reason') border-red-500 ring-red-500 @enderror"
                                placeholder="Ej: Abandono voluntario, Expulsión disciplinaria, Cambio de institución, Motivos personales..."
                                required
                            >{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Este motivo quedará registrado en el historial del estudiante.
                            </p>
                        </div>

                        {{-- Confirmation Checkbox --}}
                        <div class="p-4 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/50 rounded-xl">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" 
                                       id="confirm_withdraw"
                                       class="mt-1 w-5 h-5 rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700"
                                       required>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    Confirmo que deseo retirar a <strong class="text-gray-900 dark:text-white">{{ $student->full_name }}</strong> de todas sus inscripciones activas. Entiendo que esta acción es irreversible.
                                </span>
                            </label>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('students.show', $student) }}"
                               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-lg shadow-red-500/25 hover:shadow-red-500/40 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Confirmar Retiro
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>