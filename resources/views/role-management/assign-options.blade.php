<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Asignar Rol</h1>

                    <div class="mb-6 p-4 bg-gray-100 dark:bg-gray-700 rounded">
                        <p class="text-sm text-gray-700 dark:text-gray-400 mb-2">Usuario seleccionado:</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user->full_name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                        
                        <div class="mt-3">
                            <p class="text-sm text-gray-700 dark:text-gray-400 mb-1">Roles actuales:</p>
                            @if ($user->roles->isNotEmpty())
                                @foreach ($user->roles as $role)
                                    <span class="inline-block bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-100 text-xs px-2 py-1 rounded mr-1">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-gray-400 italic text-sm">Sin rol</span>
                            @endif
                        </div>
                    </div>

                    <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Roles Disponibles</h2>

                    @if (empty($availableRoles))
                        <div class="p-4 bg-yellow-100 dark:bg-yellow-900 rounded mb-4">
                            <p class="text-yellow-800 dark:text-yellow-100">
                                No hay roles disponibles para asignar a este usuario.
                            </p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($availableRoles as $roleData)
                                <div class="p-4 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $roleData['label'] }}
                                            </h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $roleData['description'] }}
                                            </p>
                                            @if ($roleData['needs_form'])
                                                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                                    ⚠️ Requiere completar campos adicionales
                                                </p>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('role-management.show-form', ['user' => $user, 'role' => $roleData['role']->value]) }}"
                                                class="bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700">
                                                Seleccionar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('role-management.index') }}"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md">
                            Volver al buscador
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>