<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Materias y Profesores Autorizados</h1>

                    {{-- Formulario de búsqueda y filtros --}}
                    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
                        <form method="GET" action="{{ route('subject-teacher.index') }}"
                            class="flex gap-2 flex-wrap items-center">
                            
                            <input type="text" name="search" placeholder="Buscar materia..." 
                                value="{{ request('search') }}"
                                class="block w-80 rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-800 p-2"
                                autocomplete="off" style="text-transform:uppercase;">

                            <select name="subject_id"
                                class="block rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                                <option value="">Todas las materias</option>
                                @foreach($allSubjects as $sub)
                                    <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>
                                        {{ $sub->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit"
                                class="bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700">
                                Buscar
                            </button>

                            <a href="{{ route('dashboard') }}"
                                class="underline px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Volver al Panel
                            </a>
                        </form>
                    </div>

                    {{-- Tabla de materias y profesores --}}
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Materia</th>
                                    <th class="py-2 px-4 border-b text-left">Profesores Autorizados</th>
                                    <th class="py-2 px-4 border-b text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subjects as $subject)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-3 px-4 border-b">
                                            <span class="font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $subject->name }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            @if($subject->teachers->count() > 0)
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($subject->teachers as $teacher)
                                                        <a href="{{ route('teachers.show', $teacher) }}"
                                                           class="inline-block bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-100 text-xs px-3 py-1 rounded hover:bg-indigo-200 dark:hover:bg-indigo-700">
                                                            {{ $teacher->user->full_name }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500 italic text-sm">
                                                    Sin profesores asignados
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 border-b text-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-bold text-white 
                                                {{ $subject->teachers->count() > 0 ? 'bg-green-600' : 'bg-gray-400' }} 
                                                rounded-full">
                                                {{ $subject->teachers->count() }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 px-4 text-center text-gray-300">
                                            No se encontraron materias
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    @if ($subjects->count() > 0)
                        <div class="mt-4">
                            {{ $subjects->appends(request()->except('page'))->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>