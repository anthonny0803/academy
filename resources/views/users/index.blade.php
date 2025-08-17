<x-app-layout> {{-- Asume que estás usando el layout de Breeze --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lista de Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Crear Nuevo Usuario
                        </a>
                    </div>

                    @if($users->isEmpty())
                        <p class="text-center py-4">No hay usuarios registrados.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Roles</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Activo</th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100">{{ $user->id }}</td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100">{{ $user->name }} {{ $user->last_name }}</td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100">
                                            @forelse($user->getRoleNames() as $role)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">{{ $role }}</span>
                                            @empty
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">Sin Rol</span>
                                            @endforelse
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100">
                                            @if($user->is_active)
                                                <span class="text-green-500">Sí</span>
                                            @else
                                                <span class="text-red-500">No</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900 mr-3">{{ __('Ver') }}</a>
                                            <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">{{ __('Editar') }}</a>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('¿Estás seguro de que quieres eliminar este usuario?') }}')">{{ __('Eliminar') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $users->links() }} {{-- Para los enlaces de paginación --}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>