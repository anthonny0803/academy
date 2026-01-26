<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-indigo-600 to-purple-700 dark:from-indigo-800 dark:via-indigo-800 dark:to-purple-900 rounded-2xl shadow-xl mb-8">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-8">
                    {{-- Back link --}}
                    <a href="{{ route('role-management.show-assign-options', $user) }}" class="inline-flex items-center gap-2 text-indigo-200 hover:text-white transition-colors mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver a opciones
                    </a>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl border border-white/30">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Asignar Rol: {{ $roleEnum->value }}</h1>
                            <p class="mt-1 text-indigo-100">Completa los campos requeridos</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- User Info Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Usuario
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                            {{ strtoupper(substr($user->name, 0, 1) . substr($user->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $user->full_name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Alert --}}
            @if (!empty($missingFields))
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Se requieren los siguientes campos para completar la asignación del rol.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Información Requerida
                    </h3>
                </div>

                <form method="POST" action="{{ route('role-management.assign', ['user' => $user, 'role' => $roleEnum->value]) }}">
                    @csrf
                    <div class="p-6 space-y-5">
                        {{-- Password --}}
                        @if (in_array('password', $missingFields))
                            <div class="space-y-1">
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password" id="password" required
                                       class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Confirmar Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            </div>
                        @endif

                        {{-- Document ID --}}
                        @if (in_array('document_id', $missingFields))
                            <div class="space-y-1">
                                <label for="document_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Documento de Identidad <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="document_id" id="document_id" required
                                       value="{{ old('document_id') }}"
                                       placeholder="12345678Z"
                                       class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 placeholder:text-gray-400/50 transition-all duration-200 uppercase @error('document_id') border-red-500 @enderror"
                                       style="text-transform:uppercase;">
                                @error('document_id')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Birth Date --}}
                        @if (in_array('birth_date', $missingFields))
                            <div class="space-y-1">
                                <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha de Nacimiento <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="birth_date" id="birth_date" required
                                       value="{{ old('birth_date') }}"
                                       class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 dark:[color-scheme:dark] @error('birth_date') border-red-500 @enderror">
                                @error('birth_date')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Phone --}}
                        @if (in_array('phone', $missingFields))
                            <div class="space-y-1">
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Teléfono <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="phone" id="phone" required
                                       value="{{ old('phone') }}"
                                       placeholder="912345678"
                                       class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 placeholder:text-gray-400/50 transition-all duration-200 @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Address --}}
                        @if (in_array('address', $missingFields))
                            <div class="space-y-1">
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Dirección <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="address" id="address" required
                                       value="{{ old('address') }}"
                                       placeholder="Calle Principal 123"
                                       class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 placeholder:text-gray-400/50 transition-all duration-200 uppercase @error('address') border-red-500 @enderror"
                                       style="text-transform:uppercase;">
                                @error('address')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Occupation (only for representative) --}}
                        @if ($roleEnum->value === 'representative')
                            <div class="space-y-1">
                                <label for="occupation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ocupación <span class="text-gray-400 text-xs">(Opcional)</span>
                                </label>
                                <input type="text" name="occupation" id="occupation"
                                       value="{{ old('occupation') }}"
                                       placeholder="Ingeniero, Médico, Comerciante..."
                                       class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 placeholder:text-gray-400/50 transition-all duration-200 uppercase @error('occupation') border-red-500 @enderror"
                                       style="text-transform:uppercase;">
                                @error('occupation')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Show message if no missing fields --}}
                        @if (empty($missingFields) && $roleEnum->value !== 'representative')
                            <div class="text-center py-4">
                                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">
                                    El usuario ya tiene todos los campos requeridos. Puedes proceder a asignar el rol.
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                        <a href="{{ route('role-management.show-assign-options', $user) }}"
                           class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/25 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Asignar Rol
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>