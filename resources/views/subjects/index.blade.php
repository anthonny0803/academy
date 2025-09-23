<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h1 class="text-2xl font-bold mb-4 text-white dark:text-white">Lista de Materias</h1>

                    <form method="GET" action="{{ route('subjects.index') }}" class="flex gap-2 mb-4">
                        <input type="text" name="search" placeholder="Buscar..." value="{{ request('search') }}"
                            class="block w-64 rounded-md border-gray-300 dark:border-gray-700 bg-gray-800 dark:bg-gray-900 text-white p-2"
                            autocomplete="off">

                        <button type="submit"
                            class="bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700">Buscar</button>
                        <a href="{{ route('dashboard') }}"
                            class="underline px-4 py-2 text-sm text-white hover:text-gray-300 rounded-md">
                            Volver al Panel
                        </a>
                    </form>

                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Código</th>
                                    <th class="py-2 px-4 border-b text-left">Nombre</th>
                                    <th class="py-2 px-4 border-b text-left">Descripción</th>
                                    <th class="py-2 px-4 border-b text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subjects as $subject)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-2 px-4 border-b">{{ $subject->id ?? '-' }}</td>
                                        <td class="py-2 px-4 border-b">{{ $subject->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $subject->description ?? '-' }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="#"
                                                class="px-2 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700">Ver</a>
                                            <a href="#"
                                                class="px-2 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Editar</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-2 px-4 text-center text-gray-300">
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

                    @if ($subjects->count() > 0)
                        <div class="mt-4 text-white">
                            {{ $subjects->appends(request()->except('page'))->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
