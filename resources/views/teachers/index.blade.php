<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Lista de Profesores</h1>

                    <!-- Contenedor del formulario y botón -->
                    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">

                        <!-- Formulario de búsqueda -->
                        <form method="GET" action="{{ route('teachers.index') }}"
                            class="flex gap-2 flex-wrap items-center">
                            <input type="text" name="search" placeholder="Buscar..." value="{{ request('search') }}"
                                class="block w-80 rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-800 p-2"
                                autocomplete="off" style="text-transform:uppercase;">

                            <select name="status"
                                class="block rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                                <option value="">Todos los estados</option>
                                <option value="Activo" {{ request('status') === 'Activo' ? 'selected' : '' }}>Activo
                                </option>
                                <option value="Inactivo" {{ request('status') === 'Inactivo' ? 'selected' : '' }}>
                                    Inactivo
                                </option>
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

                        <!-- Botón para registrar teacher alineado a la derecha -->
                        <a href="{{ route('teachers.create') }}"
                            class="bg-green-600 text-white rounded-md px-4 py-2 hover:bg-green-700">
                            Registrar profesor
                        </a>

                    </div>

                    <!-- Tabla de teachers -->
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Nombre</th>
                                    <th class="py-2 px-4 border-b text-left">Apellido</th>
                                    <th class="py-2 px-4 border-b text-left">Correo</th>
                                    <th class="py-2 px-4 border-b text-left">Sexo</th>
                                    <th class="py-2 px-4 border-b text-left">Rol</th>
                                    <th class="py-2 px-4 border-b text-left">Gestionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($teachers as $teacher)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-2 px-4 border-b">{{ $teacher->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $teacher->last_name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $teacher->email }}</td>
                                        <td class="py-2 px-4 border-b">{{ $teacher->sex }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if ($teacher->user && $teacher->user->roles->isNotEmpty())
                                                @foreach ($teacher->user->roles as $role)
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
                                            <button data-dropdown-teacher="{{ $teacher->id }}"
                                                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                                Acciones
                                            </button>

                                            <!-- Dropdown actions -->
                                            <div id="dropdown-template-{{ $teacher->id }}" class="hidden">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                                    <li><a href="{{ route('teachers.show', $teacher) }}"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Ver</a>
                                                    </li>
                                                    <li><a href="{{ route('users.edit', $teacher->user) }}"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Editar</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-2 px-4 text-center text-gray-300">
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

                    <!-- Paginación -->
                    @if ($teachers->count() > 0)
                        <div class="mt-4">
                            {{ $teachers->appends(request()->except('page'))->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Script para dropdowns -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll("[data-dropdown-teacher]").forEach(btn => {
                btn.addEventListener("click", () => {
                    const teacherId = btn.dataset.dropdownTeacher;
                    let existing = document.getElementById("dropdownMenu-" + teacherId);

                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }

                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());

                    const tpl = document.getElementById("dropdown-template-" + teacherId);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + teacherId;
                    clone.classList.remove("hidden");
                    clone.classList.add(
                        "dropdown-clone", "fixed", "z-50", "w-40",
                        "bg-white", "dark:bg-gray-800", "border", "border-gray-200",
                        "dark:border-gray-700", "rounded", "shadow-lg"
                    );

                    const rect = btn.getBoundingClientRect();
                    const menuHeight = 160;
                    const espacioAbajo = window.innerHeight - rect.bottom;
                    const espacioArriba = rect.top;

                    if (espacioAbajo >= menuHeight) {
                        clone.style.top = rect.bottom + "px";
                    } else if (espacioArriba >= menuHeight) {
                        clone.style.top = (rect.top - menuHeight) + "px";
                    } else {
                        clone.style.top = rect.bottom + "px";
                        clone.style.maxHeight = espacioAbajo + "px";
                        clone.style.overflowY = "auto";
                    }

                    clone.style.left = rect.left + "px";
                    document.body.appendChild(clone);
                });
            });

            document.addEventListener("click", e => {
                if (!e.target.closest("[data-dropdown-teacher]") &&
                    !e.target.closest(".dropdown-clone")) {
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                }
            });
        });
    </script>

</x-app-layout>
