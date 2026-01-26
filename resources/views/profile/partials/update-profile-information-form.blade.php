<section>
    <header class="mb-6">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('Información del Perfil') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __("Actualiza la información de tu cuenta y correo electrónico.") }}
                </p>
            </div>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div class="grid gap-5 sm:grid-cols-2">
            {{-- Nombre --}}
            <div class="space-y-1">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Nombre') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required autofocus autocomplete="name"
                       value="{{ old('name', $user->name) }}"
                       class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Apellido --}}
            <div class="space-y-1">
                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Apellido') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="last_name" name="last_name" required autocomplete="family-name"
                       value="{{ old('last_name', $user->last_name) }}"
                       class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('last_name') border-red-500 @enderror">
                @error('last_name')
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Email --}}
        <div class="space-y-1">
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Correo Electrónico') }} <span class="text-red-500">*</span>
            </label>
            <input type="email" id="email" name="email" required autocomplete="username"
                   value="{{ old('email', $user->email) }}"
                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('email') border-red-500 @enderror">
            @error('email')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                    <p class="text-sm text-amber-800 dark:text-amber-200">
                        {{ __('Tu correo electrónico no está verificado.') }}
                        <button form="send-verification" class="underline text-amber-700 dark:text-amber-300 hover:text-amber-900 dark:hover:text-amber-100 font-medium ml-1">
                            {{ __('Haz clic aquí para reenviar el correo de verificación.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu correo electrónico.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Sexo --}}
        <div class="space-y-1">
            <label for="sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Sexo') }} <span class="text-red-500">*</span>
            </label>
            <select id="sex" name="sex" required
                    class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 @error('sex') border-red-500 @enderror">
                <option value="">Selecciona tu sexo</option>
                <option value="Masculino" {{ old('sex', $user->sex) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                <option value="Femenino" {{ old('sex', $user->sex) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                <option value="Otro" {{ old('sex', $user->sex) == 'Otro' ? 'selected' : '' }}>Otro</option>
            </select>
            @error('sex')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/25 transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('Guardar Cambios') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 3000)"
                   class="inline-flex items-center gap-1.5 text-sm text-green-600 dark:text-green-400 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('Guardado correctamente.') }}
                </p>
            @endif
        </div>
    </form>
</section>