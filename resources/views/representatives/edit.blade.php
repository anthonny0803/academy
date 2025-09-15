<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('representatives.update', $representative) }}">
                        @csrf
                        @method('PATCH')

                        <div class="flex flex-wrap -mx-2 mb-4">
                            {{-- Nombre --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="name" :value="__('Nombres')" />
                                <x-text-input id="name" class="block mt-1 w-full uppercase" type="text"
                                    name="name" value="{{ old('name', $representative->user->name) }}"
                                    autocomplete="off" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            {{-- Apellidos --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="last_name" :value="__('Apellidos')" />
                                <x-text-input id="last_name" class="block mt-1 w-full uppercase" type="text"
                                    name="last_name" value="{{ old('last_name', $representative->user->last_name) }}"
                                    autocomplete="off" required />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>

                            {{-- DNI/NIE --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="document_id" :value="__('DNI/NIE')" />
                                <x-text-input id="document_id" class="block mt-1 w-full uppercase" type="text"
                                    name="document_id" value="{{ old('document_id', $representative->document_id) }}"
                                    autocomplete="off" pattern="[A-Za-z]{0,1}[0-9]{7,9}[A-Za-z]{1}" required />
                                <x-input-error :messages="$errors->get('document_id')" class="mt-2" />
                            </div>

                            {{-- Correo --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="email" :value="__('Correo')" />
                                <x-text-input id="email" class="block mt-1 w-full lowercase" type="email"
                                    name="email" value="{{ old('email', $representative->user->email) }}"
                                    autocomplete="off" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            {{-- Teléfono --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="phone" :value="__('Teléfono')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone"
                                    value="{{ old('phone', $representative->phone) }}" pattern="[0-9]{9,13}"
                                    autocomplete="off" required />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            {{-- Ocupación --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="occupation" :value="__('Ocupación (Opcional)')" />
                                <x-text-input id="occupation" class="block mt-1 w-full uppercase" type="text"
                                    name="occupation" value="{{ old('occupation', $representative->occupation) }}"
                                    autocomplete="off" />
                                <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                            </div>

                            {{-- Sexo --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="sex" :value="__('Sexo')" />
                                <select id="sex" name="sex"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                                    required>
                                    <option value="">Selecciona una opción</option>
                                    @foreach (['Masculino', 'Femenino', 'Otro'] as $sex)
                                        <option value="{{ $sex }}"
                                            {{ old('sex', $representative->user->sex) === $sex ? 'selected' : '' }}>
                                            {{ $sex }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('sex')" class="mt-2" />
                            </div>

                            {{-- Fecha de nacimiento --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="birth_date" :value="__('Fecha de Nacimiento')" />
                                <x-text-input id="birth_date" datepicker datepicker-autohide
                                    datepicker-format="dd/mm/yyyy" type="text" name="birth_date"
                                    value="{{ old('birth_date', $representative->birth_date?->format('d/m/Y')) }}"
                                    class="block mt-1 w-full" placeholder="Elige una fecha" required />
                                <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                            </div>

                            {{-- Domicilio --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="address" :value="__('Domicilio')" />
                                <textarea id="address" name="address" rows="4"
                                    class="block mt-1 w-full uppercase text-gray-900 dark:text-gray-300 bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-700 rounded-md shadow-sm"
                                    autocomplete="off" required>{{ old('address', $representative->address) }}</textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                href="{{ route('representatives.index') }}">
                                {{ __('Ir a la Lista') }}
                            </a>
                            <a class="underline text-sm px-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                href="{{ route('dashboard') }}">
                                {{ __('Volver al Panel') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Actualizar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
