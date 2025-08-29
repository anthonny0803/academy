<x-app-layout>
    {{-- Pasa el mensaje de error de la sesión a una variable de JavaScript --}}
    <script>
        // Función para crear y mostrar el modal
        function showErrorModal(message) {
            // Crea el HTML del modal en un string
            const modalHtml = `
                <div id="errorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
                        <div class="flex items-center justify-between pb-3">
                            <h3 class="text-xl font-semibold text-red-600">¡Error!</h3>
                            <button onclick="document.getElementById('errorModal').style.display='none';" class="text-gray-500 hover:text-gray-700">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-2 text-gray-800">
                            <p>${message}</p>
                        </div>
                    </div>
                </div>
            `;

            // Inserta el modal en el cuerpo del documento
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }
    </script>

    {{-- Pasa el mensaje de error de la sesión a una variable de JavaScript --}}
    @if(session('error'))
    <script>
        // Muestra el modal si existe un mensaje de error
        showErrorModal("{{ session('error') }}");
    </script>
    @endif
    <div class="py-12">
        {{-- Aquí se usa la clase que hace el card más ancho. Tailwind ignora "max-w-9xl", pero el resultado es el que quieres. --}}
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('clients.store') }}">
                        @csrf

                        {{-- Contenedor de Flexbox para las tres columnas --}}
                        <div class="flex flex-wrap -mx-2 mb-4">
                            {{-- Los campos ocupan 1/3 del ancho en pantallas sm y mayores --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="name" :value="__('Nombres')" />
                                <x-text-input id="name" class="block mt-1 w-full uppercase" type="text" name="name" :value="old('name')" required autofocus autocomplete="off" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="last_name" :value="__('Apellidos')" />
                                <x-text-input id="last_name" class="block mt-1 w-full uppercase" type="text" name="last_name" :value="old('last_name')" required autocomplete="off" />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="document_id" :value="__('DNI/NIE')" />
                                <x-text-input id="document_id" class="block mt-1 w-full uppercase" type="text" name="document_id" :value="old('document_id')" pattern="[A-Za-z]{0,1}[0-9]{7,9}[A-Za-z]{1}" required autocomplete="off" />
                                <x-input-error :messages="$errors->get('document_id')" class="mt-2" />
                            </div>



                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="email" :value="__('Correo')" />
                                <x-text-input id="email" class="block mt-1 w-full lowercase" type="email" name="email" :value="old('email')" required autocomplete="off" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="phone" :value="__('Teléfono')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" pattern="[0-9]{9,13}" required autocomplete="off" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="occupation" :value="__('Ocupación (Opcional)')" />
                                <x-text-input id="occupation" class="block mt-1 w-full uppercase" type="text" name="occupation" :value="old('occupation')" autocomplete="off" />
                                <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="sex" :value="__('Sexo')" />
                                <select id="sex" name="sex" class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value="">Selecciona una opción</option>
                                    <option value="Masculino" {{ old('sex') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ old('sex') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Otro" {{ old('sex') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                <x-input-error :messages="$errors->get('sex')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="birth_date" :value="__('Fecha de Nacimiento')" />
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                        </svg>
                                    </div>
                                    <x-text-input
                                        id="birth_date"
                                        datepicker
                                        datepicker-autohide
                                        datepicker-format="dd/mm/yyyy"
                                        type="text"
                                        name="birth_date"
                                        :value="old('birth_date')"
                                        class="block mt-1 w-full ps-10"
                                        required
                                        autocomplete="off"
                                        placeholder="Elige una fecha" />
                                    <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                                </div>
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="address" :value="__('Domicilio')" />
                                <textarea
                                    id="address"
                                    name="address"
                                    rows="4"
                                    class="block mt-1 w-full uppercase text-gray-900 dark:text-gray-300 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-700 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>{{ old('address') }}</textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('dashboard') }}">
                                {{ __('Volver al Panel') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Registrar Cliente') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>