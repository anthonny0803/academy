<section>
    <header class="mb-6">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('Actualizar Contraseña') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Asegúrate de usar una contraseña segura y única.') }}
                </p>
            </div>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        {{-- Contraseña actual --}}
        <div class="space-y-1">
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Contraseña Actual') }} <span class="text-red-500">*</span>
            </label>
            <input type="password" id="update_password_current_password" name="current_password" autocomplete="current-password"
                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 @error('current_password', 'updatePassword') border-red-500 @enderror">
            @error('current_password', 'updatePassword')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            {{-- Nueva contraseña --}}
            <div class="space-y-1">
                <label for="update_password_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Nueva Contraseña') }} <span class="text-red-500">*</span>
                </label>
                <input type="password" id="update_password_password" name="password" autocomplete="new-password"
                       class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 @error('password', 'updatePassword') border-red-500 @enderror">
                @error('password', 'updatePassword')
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirmar contraseña --}}
            <div class="space-y-1">
                <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Confirmar Contraseña') }} <span class="text-red-500">*</span>
                </label>
                <input type="password" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password"
                       class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200">
            </div>
        </div>

        {{-- Password Tips --}}
        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Consejos para una contraseña segura:</p>
            <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                <li class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Mínimo 8 caracteres
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Combina mayúsculas y minúsculas
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Incluye números y símbolos
                </li>
            </ul>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium rounded-xl shadow-lg shadow-amber-500/25 transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                {{ __('Actualizar Contraseña') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 3000)"
                   class="inline-flex items-center gap-1.5 text-sm text-green-600 dark:text-green-400 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('Contraseña actualizada.') }}
                </p>
            @endif
        </div>
    </form>
</section>