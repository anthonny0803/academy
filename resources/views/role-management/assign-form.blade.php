<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                        Asignar Rol: {{ $roleEnum->value }}
                    </h1>

                    <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-700 rounded">
                        <p class="text-sm text-gray-700 dark:text-gray-400 mb-2">Usuario:</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->full_name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                    </div>

                    @if (!empty($missingFields))
                        <div class="mb-6 p-4 bg-blue-100 dark:bg-blue-900 rounded">
                            <p class="text-blue-800 dark:text-blue-100 text-sm">
                                ℹ️ Se requieren los siguientes campos para completar la asignación del rol.
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('role-management.assign', ['user' => $user, 'role' => $roleEnum->value]) }}">
                        @csrf

                        @if (in_array('password', $missingFields))
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Contraseña *
                                </label>
                                <input type="password" name="password" id="password" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2 
                                    @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Confirmar Contraseña *
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                            </div>
                        @endif

                        @if (in_array('document_id', $missingFields))
                            <div class="mb-4">
                                <label for="document_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Documento de Identidad *
                                </label>
                                <input type="text" name="document_id" id="document_id" required
                                    value="{{ old('document_id') }}"
                                    placeholder="12345678Z"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2 
                                    @error('document_id') border-red-500 @enderror"
                                    style="text-transform:uppercase;">
                                @error('document_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        @if (in_array('birth_date', $missingFields))
                            <div class="mb-4">
                                <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Fecha de Nacimiento *
                                </label>
                                <input type="date" name="birth_date" id="birth_date" required
                                    value="{{ old('birth_date') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2 
                                    @error('birth_date') border-red-500 @enderror">
                                @error('birth_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        @if (in_array('phone', $missingFields))
                            <div class="mb-4">
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Teléfono *
                                </label>
                                <input type="text" name="phone" id="phone" required
                                    value="{{ old('phone') }}"
                                    placeholder="912345678"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2 
                                    @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        @if (in_array('address', $missingFields))
                            <div class="mb-4">
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Dirección *
                                </label>
                                <input type="text" name="address" id="address" required
                                    value="{{ old('address') }}"
                                    placeholder="Calle Principal 123"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2 
                                    @error('address') border-red-500 @enderror"
                                    style="text-transform:uppercase;">
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        @if ($roleEnum->value === 'representative')
                            <div class="mb-4">
                                <label for="occupation" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Ocupación (Opcional)
                                </label>
                                <input type="text" name="occupation" id="occupation"
                                    value="{{ old('occupation') }}"
                                    placeholder="Ingeniero"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2 
                                    @error('occupation') border-red-500 @enderror"
                                    style="text-transform:uppercase;">
                                @error('occupation')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div class="flex justify-end gap-2 mt-6">
                            <a href="{{ route('role-management.show-assign-options', $user) }}"
                                class="bg-gray-600 text-white rounded-md px-4 py-2 hover:bg-gray-700">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700">
                                Asignar Rol
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>