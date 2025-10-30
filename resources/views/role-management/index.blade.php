<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h1 class="text-2xl font-bold mb-4 text-white dark:text-white">Gestión de Roles</h1>

                    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">

                        <form method="GET" action="{{ route('role-management.index') }}" class="flex gap-2 flex-wrap items-center">
                            <input type="text" name="search" placeholder="Buscar usuario..." value="{{ request('search') }}"
                                class="block w-80 rounded-md border-gray-300 dark:border-gray-700 bg-gray-800 dark:bg-gray-900 text-white p-2"
                                autocomplete="off" style="text-transform:uppercase;">

                            <button type="submit"
                                class="bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700">
                                Buscar
                            </button>

                            <a href="{{ route('dashboard') }}"
                                class="underline px-4 py-2 text-sm text-white hover:text-gray-300 rounded-md">
                                Volver al Panel
                            </a>
                        </form>

                    </div>

                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Nombre Completo</th>
                                    <th class="py-2 px-4 border-b text-left">Correo</th>
                                    <th class="py-2 px-4 border-b text-left">Roles Actuales</th>
                                    <th class="py-2 px-4 border-b text-left">Perfiles</th>
                                    <th class="py-2 px-4 border-b text-left">Asignar Rol</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-2 px-4 border-b">{{ $user->full_name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if ($user->roles->isNotEmpty())
                                                @foreach ($user->roles as $role)
                                                    <span
                                                        class="inline-block bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-100 text-xs px-2 py-1 rounded mr-1">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-gray-400 italic">Sin rol</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            @if ($user->teacher)
                                                <span class="inline-block bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-100 text-xs px-2 py-1 rounded mr-1">
                                                    Teacher
                                                </span>
                                            @endif
                                            @if ($user->representative)
                                                <span class="inline-block bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 text-xs px-2 py-1 rounded mr-1">
                                                    Representative
                                                </span>
                                            @endif
                                            @if ($user->student)
                                                <span class="inline-block bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 text-xs px-2 py-1 rounded mr-1">
                                                    Student
                                                </span>
                                            @endif
                                            @if (!$user->teacher && !$user->representative && !$user->student)
                                                <span class="text-gray-400 italic">Sin perfil</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('role-management.show-assign-options', $user) }}"
                                                class="bg-green-600 text-white rounded-md px-3 py-1 hover:bg-green-700 text-sm">
                                                Asignar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-2 px-4 text-center text-gray-300">
                                            @if (request()->filled('search'))
                                                No se encontraron resultados
                                            @else
                                                Ingresa un término de búsqueda para ver resultados
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($users->count() > 0)
                        <div class="mt-4 text-white">
                            {{ $users->appends(request()->except('page'))->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>