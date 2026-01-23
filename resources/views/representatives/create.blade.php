<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('representatives.index') }}" class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Ir al listado
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Registrar Representante</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Ingresa los datos del nuevo representante o tutor</p>
            </div>

            {{-- Form Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <form method="POST" action="{{ route('representatives.store') }}">
                    @csrf

                    <div class="p-6 sm:p-8">

                        {{-- Section: Información Personal --}}
                        <div class="mb-8">
                            <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Información Personal
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                {{-- Nombre --}}
                                <div class="space-y-1">
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nombres <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="off"
                                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 placeholder:text-gray-400/50 uppercase"
                                           placeholder="Nombres">
                                    @error('name')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Apellido --}}
                                <div class="space-y-1">
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Apellidos <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required autocomplete="off"
                                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 placeholder:text-gray-400/50 uppercase"
                                           placeholder="Apellidos">
                                    @error('last_name')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- DNI/NIE --}}
                                <div class="space-y-1">
                                    <label for="document_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        DNI/NIE <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="document_id" name="document_id" value="{{ old('document_id') }}" required autocomplete="off"
                                           pattern="[A-Za-z]{0,1}[0-9]{7,9}[A-Za-z]{1}"
                                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 placeholder:text-gray-400/50 uppercase"
                                           placeholder="12345678A">
                                    @error('document_id')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Sexo --}}
                                <div class="space-y-1">
                                    <label for="sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Sexo <span class="text-red-500">*</span>
                                    </label>
                                    <select id="sex" name="sex" required
                                            class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200">
                                        <option value="">Selecciona una opción</option>
                                        @foreach ($sexes as $sex)
                                            <option value="{{ $sex }}" {{ old('sex') == $sex ? 'selected' : '' }}>
                                                {{ ucfirst($sex) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sex')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Fecha de nacimiento --}}
                                <div class="space-y-1">
                                    <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Fecha de Nacimiento <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required autocomplete="off"
                                               class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 dark:[color-scheme:dark]">
                                    </div>
                                    @error('birth_date')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Ocupación --}}
                                <div class="space-y-1">
                                    <label for="occupation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Ocupación <span class="text-gray-400 text-xs">(Opcional)</span>
                                    </label>
                                    <input type="text" id="occupation" name="occupation" value="{{ old('occupation') }}" autocomplete="off"
                                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 placeholder:text-gray-400/50 uppercase"
                                           placeholder="Ej: Ingeniero, Médico...">
                                    @error('occupation')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700 my-6">

                        {{-- Section: Información de Contacto --}}
                        <div>
                            <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Información de Contacto
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                {{-- Email --}}
                                <div class="space-y-1">
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Correo electrónico <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                            </svg>
                                        </div>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="off"
                                               class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 placeholder:text-gray-400/50 lowercase"
                                               placeholder="correo@ejemplo.com">
                                    </div>
                                    @error('email')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Teléfono --}}
                                <div class="space-y-1">
                                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Teléfono <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </div>
                                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required autocomplete="off"
                                               pattern="[0-9]{9,13}"
                                               class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 placeholder:text-gray-400/50"
                                               placeholder="612345678">
                                    </div>
                                    @error('phone')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Domicilio --}}
                                <div class="space-y-1">
                                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Domicilio <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="address" name="address" rows="3" required
                                              class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 placeholder:text-gray-400/50 uppercase resize-none"
                                              placeholder="Calle, número, piso, ciudad...">{{ old('address') }}</textarea>
                                    @error('address')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Form Actions --}}
                    <div class="px-6 sm:px-8 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3 sm:justify-end">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Registrar Representante
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>