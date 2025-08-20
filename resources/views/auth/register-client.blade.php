<x-app-layout> {{-- <-- Primer cambio aquí --}}
    <div class="py-12"> {{-- Padding vertical como en el dashboard --}}
        <div class="max-w-md mx-auto sm:px-6 lg:px-8"> {{-- Centra y limita el ancho --}}
            {{-- EL CONTENEDOR DEL CARD: Mismos colores que tu dashboard --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6"> {{-- Padding y colores de texto del card --}}

                    <form method="POST" action="{{ route('register-client') }}">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Nombres')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="off" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="last_name" :value="__('Apellidos')" />
                            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="off" />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="document_id" :value="__('DNI/NIE')" />
                            <x-text-input id="document_id" class="block mt-1 w-full uppercase" type="text" name="document_id" :value="old('document_id')" pattern="[A-Z]{0,1}[0-9]{7,9}[A-Z]{1}" required autocomplete="off" />
                            <x-input-error :messages="$errors->get('document_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Correo (Opcional)')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="off" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="sex" :value="__('Sexo')" />
                            {{-- MODIFICADO: Asegúrate de que el select tenga los colores correctos para modo oscuro --}}
                            <select id="sex" name="sex" class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="">Selecciona una opción</option>
                                <option value="Masculino" {{ old('sex') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="Femenino" {{ old('sex') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                <option value="Otro" {{ old('sex') == 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            <x-input-error :messages="$errors->get('sex')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Contraseña')" />
                            <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            {{-- Este enlace a dashboard probablemente no lo necesites si es un registro admin --}}
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('dashboard') }}">
                                {{ __('Volver al Panel') }}
                            </a>

                            <x-primary-button class="ms-4">
                                {{ __('Crear Usuario') }} {{-- Cambiado de 'Registrar' a 'Crear Usuario' para claridad --}}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>