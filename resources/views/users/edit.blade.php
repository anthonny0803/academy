<x-app-layout>
    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- Form --}}
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PATCH')

                        {{-- Personal information --}}
                        <div>
                            <x-input-label for="name" :value="__('Nombre')" />
                            <x-text-input id="name" class="block mt-1 w-full uppercase cursor-not-allowed opacity-60"
                                type="text" name="name" :value="$user->name" disabled />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="last_name" :value="__('Apellido')" />
                            <x-text-input id="last_name" class="block mt-1 w-full uppercase cursor-not-allowed opacity-60"
                                type="text" name="last_name" :value="$user->last_name" disabled />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Correo')" />
                            <x-text-input id="email" class="block mt-1 w-full lowercase" type="email"
                                name="email" :value="$user->email" required autocomplete="off" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="sex" :value="__('Sexo')" />
                            <select id="sex" name="sex"
                                class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm
                                cursor-not-allowed opacity-60"
                                disabled>
                                @foreach ($sexes as $sex)
                                    <option value="{{ $sex }}"
                                        {{ old('sex', $user->sex) === $sex ? 'selected' : '' }}>
                                        {{ ucfirst($sex) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('sex')" class="mt-2" />
                        </div>

                        {{-- Assignable roles --}}
                        <div class="mt-4 text-white">
                            <x-input-label :value="__('Roles')" />

                            {{-- Hidden input to allow unchecking all roles --}}
                            <input type="hidden" name="roles[]" value="">

                            <div class="mt-2 space-y-1">
                                @foreach ($roles as $role)
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="radio" name="role" value="{{ $role->name }}"
                                            {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                            class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer">

                                        <span>{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                        </div>

                        {{-- Action buttons --}}
                        <div class="flex items-center justify-end mt-6">
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                href="{{ route('users.index') }}">
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
                    {{-- End form --}}

                    {{-- Notificación push --}}
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)" {{-- Se oculta automáticamente a los 8 segundos --}}
                        x-transition
                        class="fixed top-5 right-5 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg text-sm z-50 w-80"
                        role="alert">
                        <div class="flex justify-between items-start">
                            <div>
                                <strong class="font-bold">Nota:</strong>
                                <span class="block sm:inline">Los campos de empleados se modifican en su propio
                                    perfil.</span><br>
                                <span class="block sm:inline">El correo solo debe ser modificado en caso de ser necesario.</span>
                            </div>
                            <button type="button"
                                class="text-red-700 hover:text-red-900 font-bold text-lg leading-none ml-2"
                                @click="show = false">
                                &times;
                            </button>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>
</x-app-layout>
