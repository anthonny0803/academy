<x-app-layout>
    <div class="py-12">
        {{-- Aquí se usa la clase que hace el card más ancho. Tailwind ignora "max-w-9xl", pero el resultado es el que quieres. --}}
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('representatives.students.store', $representative) }}">
                        @csrf

                        {{-- Contenedor de Flexbox para las tres columnas --}}
                        <div class="flex flex-wrap -mx-2 mb-4">
                            {{-- Los campos ocupan 1/3 del ancho en pantallas sm y mayores --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="representative_name" :value="__('Nombres del representante')" />
                                <x-text-input id="representative_name"
                                    class="block mt-1 w-full uppercase cursor-not-allowed" :value="$representative->user->name" disabled />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="representative_last_name" :value="__('Apellidos del representante')" />
                                <x-text-input id="representative_last_name"
                                    class="block mt-1 w-full uppercase cursor-not-allowed" :value="$representative->user->last_name" disabled />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="representative_document_id" :value="__('DNI/NIE del representante')" />
                                <x-text-input id="representative_document_id"
                                    class="block mt-1 w-full uppercase cursor-not-allowed" :value="$representative->document_id" disabled />
                            </div>


                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="name" :value="__('Nombres')" />
                                <x-text-input id="name" class="block mt-1 w-full uppercase" type="text"
                                    name="name" :value="old('name')" required autofocus autocomplete="off" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="last_name" :value="__('Apellidos')" />
                                <x-text-input id="last_name" class="block mt-1 w-full uppercase" type="text"
                                    name="last_name" :value="old('last_name')" required autocomplete="off" />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="document_id" :value="__('DNI/NIE')" />
                                <x-text-input id="document_id" class="block mt-1 w-full uppercase" type="text"
                                    name="document_id" :value="old('document_id')" pattern="[A-Za-z]{0,1}[0-9]{7,9}[A-Za-z]{1}"
                                    required autocomplete="off" />
                                <x-input-error :messages="$errors->get('document_id')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="email" :value="__('Correo (Opcional)')" />
                                <x-text-input id="email" class="block mt-1 w-full lowercase" type="email"
                                    name="email" :value="old('email')" autocomplete="off" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="phone" :value="__('Teléfono')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone"
                                    :value="old('phone')" pattern="[0-9]{9,13}" required autocomplete="off" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="sex" :value="__('Sexo')" />
                                <select id="sex" name="sex"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">Selecciona una opción</option>
                                    <option value="Masculino" {{ old('sex') == 'Masculino' ? 'selected' : '' }}>
                                        Masculino</option>
                                    <option value="Femenino" {{ old('sex') == 'Femenino' ? 'selected' : '' }}>Femenino
                                    </option>
                                    <option value="Otro" {{ old('sex') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                <x-input-error :messages="$errors->get('sex')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="birth_date" :value="__('Fecha de Nacimiento')" />
                                <div class="relative">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                        </svg>
                                    </div>
                                    <input type="date" id="birth_date" name="birth_date"
                                        value="{{ old('birth_date') }}"
                                        class="block mt-1 w-full ps-10 rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2 dark:[color-scheme:dark]"
                                        required autocomplete="off" placeholder="Elige una fecha" />
                                </div>
                                <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="address" :value="__('Domicilio')" />
                                <textarea id="address" name="address" rows="4"
                                    class="block mt-1 w-full uppercase text-gray-900 dark:text-gray-300 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-700 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>{{ old('address') }}</textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                        </div>


                        <div class="flex items-center justify-end mt-4 space-x-4">

                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="self_represented_toggle" value=""
                                    class="sr-only peer cursor-pointer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                </div>
                                <span
                                    class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Autorepresentado</span>
                            </label>

                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                href="{{ route('dashboard') }}">
                                {{ __('Volver al Panel') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Registrar Estudiante') }}
                            </x-primary-button>
                        </div>
                    </form>
                    <script>
                        const representativeData = {
                            name: '{{ $representative->user->name }}',
                            last_name: '{{ $representative->user->last_name }}',
                            email: '{{ $representative->user->email }}',
                            document_id: '{{ $representative->document_id }}',
                            phone: '{{ $representative->phone }}',
                            sex: '{{ $representative->user->sex }}',
                            birth_date: '{{ $representative->birth_date ? $representative->birth_date->format('Y-m-d') : '' }}',
                            address: '{{ $representative->address }}',
                        };
                    </script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const toggle = document.getElementById('self_represented_toggle');
                            const studentNameInput = document.getElementById('name');
                            const studentLastNameInput = document.getElementById('last_name');
                            const studentEmailInput = document.getElementById('email');
                            const studentDocumentIdInput = document.getElementById('document_id');
                            const studentPhoneInput = document.getElementById('phone');
                            const studentSexInput = document.getElementById('sex');
                            const studentBirthDateInput = document.getElementById('birth_date');
                            const studentAddressInput = document.getElementById('address');

                            toggle.addEventListener('change', function() {
                                if (this.checked) {
                                    studentNameInput.value = representativeData.name;
                                    studentLastNameInput.value = representativeData.last_name;
                                    studentEmailInput.value = representativeData.email;
                                    studentDocumentIdInput.value = representativeData.document_id;
                                    studentPhoneInput.value = representativeData.phone;
                                    studentSexInput.value = representativeData.sex;
                                    studentBirthDateInput.value = representativeData.birth_date;
                                    studentAddressInput.value = representativeData.address;

                                    // Deshabilita los campos para que no puedan ser editados
                                    studentNameInput.disabled = true;
                                    studentLastNameInput.disabled = true;
                                    studentEmailInput.disabled = true;
                                    studentDocumentIdInput.disabled = true;
                                    studentPhoneInput.disabled = true;
                                    studentSexInput.disabled = true;
                                    studentBirthDateInput.disabled = true;
                                    studentAddressInput.disabled = true;

                                    // Deshabilita el cursor para los campos
                                    studentNameInput.classList.add('cursor-not-allowed');
                                    studentLastNameInput.classList.add('cursor-not-allowed');
                                    studentEmailInput.classList.add('cursor-not-allowed');
                                    studentDocumentIdInput.classList.add('cursor-not-allowed');
                                    studentPhoneInput.classList.add('cursor-not-allowed');
                                    studentSexInput.classList.add('cursor-not-allowed');
                                    studentBirthDateInput.classList.add('cursor-not-allowed');
                                    studentAddressInput.classList.add('cursor-not-allowed');
                                } else {
                                    studentNameInput.value = '';
                                    studentLastNameInput.value = '';
                                    studentEmailInput.value = '';
                                    studentDocumentIdInput.value = '';
                                    studentPhoneInput.value = '';
                                    studentSexInput.value = '';
                                    studentBirthDateInput.value = '';
                                    studentAddressInput.value = '';

                                    // Habilita los campos para que puedan ser editados
                                    studentNameInput.disabled = false;
                                    studentLastNameInput.disabled = false
                                    studentEmailInput.disabled = false;
                                    studentDocumentIdInput.disabled = false;
                                    studentPhoneInput.disabled = false;
                                    studentSexInput.disabled = false;
                                    studentBirthDateInput.disabled = false;
                                    studentAddressInput.disabled = false;

                                    // Habilita el cursor para los campos
                                    studentNameInput.classList.remove('cursor-not-allowed');
                                    studentLastNameInput.classList.remove('cursor-not-allowed');
                                    studentEmailInput.classList.remove('cursor-not-allowed');
                                    studentDocumentIdInput.classList.remove('cursor-not-allowed');
                                    studentPhoneInput.classList.remove('cursor-not-allowed');
                                    studentSexInput.classList.remove('cursor-not-allowed');
                                    studentBirthDateInput.classList.remove('cursor-not-allowed');
                                    studentAddressInput.classList.remove('cursor-not-allowed');
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
