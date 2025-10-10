<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Encabezado con búsqueda y botón Crear -->
                    <div class="flex justify-between items-center mb-4">
                        <!-- Form de búsqueda -->
                        <form method="GET" action="{{ route('subjects.index') }}" class="flex gap-2">
                            <input type="text" name="search" placeholder="Buscar..." value="{{ request('search') }}"
                                class="block w-64 rounded-md border-gray-300 dark:border-gray-700 bg-gray-800 dark:bg-gray-900 text-white p-2"
                                autocomplete="off">

                            <select name="status"
                                class="block rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                                <option value="">Todos los estados</option>
                                <option value="Activo" {{ request('status') === 'Activo' ? 'selected' : '' }}>Activo
                                </option>
                                <option value="Inactivo" {{ request('status') === 'Inactivo' ? 'selected' : '' }}>
                                    Inactivo</option>
                            </select>

                            <button type="submit"
                                class="bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700">Buscar</button>
                        </form>

                        <!-- Botón para abrir modal de creación -->
                        <button type="button" class="bg-green-600 text-white rounded-md px-4 py-2 hover:bg-green-700"
                            onclick="document.getElementById('subjectModal').classList.remove('hidden'); document.getElementById('subjectModal').classList.add('flex');">
                            Crear Asignatura
                        </button>
                    </div>

                    <h1 class="text-2xl font-bold mb-4 text-white dark:text-white">Lista de Asignaturas</h1>

                    <!-- Tabla de asignaturas -->
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Código</th>
                                    <th class="py-2 px-4 border-b text-left">Nombre</th>
                                    <th class="py-2 px-4 border-b text-left">Descripción</th>
                                    <th class="py-2 px-4 border-b text-left">Estado</th>
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
                                            <form method="POST" action="{{ route('subjects.toggle', $subject) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="relative inline-flex h-6 w-11 items-center rounded-full cursor-pointer 
                       {{ $subject->is_active ? 'bg-blue-600' : 'bg-gray-400' }}">
                                                    <span
                                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition
                           {{ $subject->is_active ? 'translate-x-6' : 'translate-x-1' }}">
                                                    </span>
                                                </button>
                                            </form>
                                            <span
                                                class="inline-block {{ $subject->is_active ? 'bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100 text-xs px-2 py-1 rounded mr-1' : 'bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100 text-xs px-2 py-1 rounded mr-1' }}">
                                                {{ $subject->is_active ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b relative">
                                            <!-- Botón Acciones -->
                                            <button data-dropdown-subject="{{ $subject->id }}"
                                                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                                Acciones
                                            </button>

                                            <!-- Dropdown actions -->
                                            <div id="dropdown-template-{{ $subject->id }}" class="hidden">
                                                <ul class="py-1 text-sm text-gray-200">
                                                    <li>
                                                        <button type="button" data-id="{{ $subject->id }}"
                                                            data-name="{{ $subject->name }}"
                                                            data-description="{{ $subject->description }}"
                                                            class="block w-full text-left px-4 py-2 hover:bg-gray-700 edit-btn">
                                                            Editar
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form method="POST"
                                                            action="{{ route('subjects.destroy', $subject) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="w-full text-left px-4 py-2 hover:bg-gray-700 text-red-600">
                                                                Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
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

                    <!-- Paginación -->
                    @if ($subjects->count() > 0)
                        <div class="mt-4 text-white">
                            {{ $subjects->appends(request()->except('page'))->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Modal de creación -->
    <div id="subjectModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md shadow-lg">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white text-center">Nueva asignatura</h2>

            <form action="{{ route('subjects.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block mb-1 text-gray-700 dark:text-gray-200">Nombre</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block mb-1 text-gray-700 dark:text-gray-200">Descripción</label>
                    <textarea name="description" id="description"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        onclick="document.getElementById('subjectModal').classList.add('hidden');">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal global de edición -->
    <div id="editSubjectModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md shadow-lg">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white text-center">Editar Asignatura</h2>

            <form id="editSubjectForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="edit-name" class="block mb-1 text-gray-700 dark:text-gray-200">Nombre</label>
                    <input type="text" name="name" id="edit-name"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">
                </div>

                <div class="mb-4">
                    <label for="edit-description"
                        class="block mb-1 text-gray-700 dark:text-gray-200">Descripción</label>
                    <textarea name="description" id="edit-description"
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        onclick="document.getElementById('editSubjectModal').classList.add('hidden');">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script dropdown + modal -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Abrir dropdown
            document.querySelectorAll("[data-dropdown-subject]").forEach(btn => {
                btn.addEventListener("click", () => {
                    const subjectId = btn.dataset.dropdownSubject;
                    let existing = document.getElementById("dropdownMenu-" + subjectId);

                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }

                    // Eliminar otros dropdowns abiertos
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());

                    const tpl = document.getElementById("dropdown-template-" + subjectId);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + subjectId;
                    clone.classList.remove("hidden");
                    clone.classList.add(
                        "dropdown-clone", "fixed", "z-50", "w-40",
                        "bg-gray-800", "border", "border-gray-700",
                        "rounded", "shadow-lg"
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

            // Cerrar dropdown al hacer clic fuera
            document.addEventListener("click", e => {
                if (!e.target.closest("[data-dropdown-subject]") && !e.target.closest(".dropdown-clone")) {
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                }
            });

            // Abrir modal de edición desde dropdown y cerrar el dropdown
            document.addEventListener("click", e => {
                if (e.target.classList.contains("edit-btn")) {
                    const id = e.target.dataset.id;
                    const name = e.target.dataset.name;
                    const description = e.target.dataset.description;

                    // Cerrar el dropdown antes de abrir modal
                    const dropdown = e.target.closest(".dropdown-clone");
                    if (dropdown) dropdown.remove();

                    // Configurar y abrir modal global
                    const form = document.getElementById("editSubjectForm");
                    form.action = `/subjects/${id}`; // Ruta PUT

                    document.getElementById("edit-name").value = name;
                    document.getElementById("edit-description").value = description;

                    document.getElementById("editSubjectModal").classList.remove("hidden");
                }
            });
        });
    </script>


</x-app-layout>
