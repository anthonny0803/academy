<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Encabezado con búsqueda y botón Crear -->
                    <div class="flex justify-between items-center mb-4">
                        <!-- Form de búsqueda -->
                        <form method="GET" action="{{ route('academic-periods.index') }}" class="flex gap-2">
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
                            onclick="document.getElementById('academicPeriodModal').classList.remove('hidden'); document.getElementById('academicPeriodModal').classList.add('flex');">
                            Crear Período Académico
                        </button>
                    </div>

                    <h1 class="text-2xl font-bold mb-4 text-white dark:text-white">Lista de Períodos Académicos</h1>

                    <!-- Tabla de períodos académicos -->
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Código</th>
                                    <th class="py-2 px-4 border-b text-left">Nombre</th>
                                    <th class="py-2 px-4 border-b text-left">Notas</th>
                                    <th class="py-2 px-4 border-b text-left">Estado</th>
                                    <th class="py-2 px-4 border-b text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($academicPeriods as $academicPeriod)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-2 px-4 border-b">{{ $academicPeriod->id ?? '-' }}</td>
                                        <td class="py-2 px-4 border-b">{{ $academicPeriod->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $academicPeriod->notes ?? '-' }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <span
                                                class="inline-block {{ $academicPeriod->is_active ? 'bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100' : 'bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100' }} text-xs px-2 py-1 rounded mr-1">
                                                {{ $academicPeriod->is_active ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b relative">
                                            <!-- Botón Acciones -->
                                            <button data-dropdown-academic-period="{{ $academicPeriod->id }}"
                                                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                                Acciones
                                            </button>

                                            <!-- Dropdown template (clonado por JS) -->
                                            <div id="dropdown-template-{{ $academicPeriod->id }}" class="hidden">
                                                <ul class="py-1 text-sm text-gray-200">
                                                    <li>
                                                        <a href="{{ route('academic-periods.show', $academicPeriod) }}"
                                                            class="block w-full text-left px-4 py-2 hover:bg-gray-700">
                                                            Ver detalles
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <!-- EDIT button: ahora incluye notes, start-date y end-date -->
                                                        <button type="button" data-id="{{ $academicPeriod->id }}"
                                                            data-name="{{ e($academicPeriod->name) }}"
                                                            data-notes="{{ e($academicPeriod->notes) }}"
                                                            data-start-date="{{ $academicPeriod->start_date ? \Carbon\Carbon::parse($academicPeriod->start_date)->format('Y-m-d') : '' }}"
                                                            data-end-date="{{ $academicPeriod->end_date ? \Carbon\Carbon::parse($academicPeriod->end_date)->format('Y-m-d') : '' }}"
                                                            class="block w-full text-left px-4 py-2 hover:bg-gray-700 edit-btn">
                                                            Editar
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form method="POST"
                                                            action="{{ route('academic-periods.destroy', $academicPeriod) }}">
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

                    <!-- Paginación -->
                    @if ($academicPeriods->count() > 0)
                        <div class="mt-4 text-white">
                            {{ $academicPeriods->appends(request()->except('page'))->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Modal CREAR -->
    <div id="academicPeriodModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md shadow-lg my-8 relative">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white text-center">Nuevo Período Académico</h2>

            <form action="{{ route('academic-periods.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <x-input-label for="name" :value="__('Nombre *')" />
                    <x-text-input id="name" name="name" type="text" :value="old('name')"
                        class="block mt-1 w-full" required autocomplete="off" placeholder="Nombre del período" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="notes" :value="__('Notas')" />
                    <textarea id="notes" name="notes"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2"
                        placeholder="Notas opcionales">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <!-- Fecha de inicio -->
                <div class="w-full mb-4">
                    <x-input-label for="start_date" :value="__('Fecha de inicio *')" />
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                            </svg>
                        </div>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}"
                            class="block mt-1 w-full ps-10 rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2 dark:[color-scheme:dark]"
                            required />
                        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                    </div>
                </div>

                <!-- Fecha de fin -->
                <div class="w-full mb-4">
                    <x-input-label for="end_date" :value="__('Fecha de fin *')" />
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                            </svg>
                        </div>
                        <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}"
                            class="block mt-1 w-full ps-10 rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2 dark:[color-scheme:dark]"
                            required />
                        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        onclick="document.getElementById('academicPeriodModal').classList.add('hidden'); document.getElementById('academicPeriodModal').classList.remove('flex');">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal EDITAR -->
    <div id="editAcademicPeriodModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md shadow-lg my-8 relative">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white text-center">Editar Período Académico</h2>

            <form id="editAcademicPeriodForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <x-input-label for="edit-name" :value="__('Nombre *')" />
                    <x-text-input id="edit-name" name="name" type="text" class="block mt-1 w-full" required
                        autocomplete="off" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit-notes" :value="__('Notas')" />
                    <textarea id="edit-notes" name="notes"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2"></textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <div class="w-full mb-4">
                    <x-input-label for="edit-start_date" :value="__('Fecha de inicio *')" />
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Z" />
                            </svg>
                        </div>
                        <input type="date" id="edit-start_date" name="start_date"
                            class="block mt-1 w-full ps-10 rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2 dark:[color-scheme:dark]"
                            required />
                        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                    </div>
                </div>

                <div class="w-full mb-4">
                    <x-input-label for="edit-end_date" :value="__('Fecha de fin *')" />
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Z" />
                            </svg>
                        </div>
                        <input type="date" id="edit-end_date" name="end_date"
                            class="block mt-1 w-full ps-10 rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2 dark:[color-scheme:dark]"
                            required />
                        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        onclick="document.getElementById('editAcademicPeriodModal').classList.add('hidden'); document.getElementById('editAcademicPeriodModal').classList.remove('flex');">
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
            // Prevenir scroll del body cuando el modal está abierto
            function toggleBodyScroll(disable) {
                if (disable) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }

            // Observar cambios en los modales
            const modals = ['academicPeriodModal', 'editAcademicPeriodModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    const observer = new MutationObserver((mutations) => {
                        mutations.forEach((mutation) => {
                            if (mutation.attributeName === 'class') {
                                const isHidden = modal.classList.contains('hidden');
                                toggleBodyScroll(!isHidden);
                            }
                        });
                    });
                    observer.observe(modal, {
                        attributes: true
                    });
                }
            });

            // Abrir dropdown
            document.querySelectorAll("[data-dropdown-academic-period]").forEach(btn => {
                btn.addEventListener("click", () => {
                    const academicPeriodId = btn.dataset.dropdownAcademicPeriod;
                    let existing = document.getElementById("dropdownMenu-" + academicPeriodId);

                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }

                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());

                    const tpl = document.getElementById("dropdown-template-" + academicPeriodId);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + academicPeriodId;
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
                if (!e.target.closest("[data-dropdown-academic-period]") && !e.target.closest(
                        ".dropdown-clone")) {
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                }
            });

            // Abrir modal de edición desde dropdown y cerrar el dropdown
            document.addEventListener("click", e => {
                if (e.target.classList.contains("edit-btn")) {
                    const btn = e.target;
                    const id = btn.getAttribute('data-id');
                    const name = btn.getAttribute('data-name') || '';
                    const notes = btn.getAttribute('data-notes') || '';
                    const startDate = btn.getAttribute('data-start-date') || '';
                    const endDate = btn.getAttribute('data-end-date') || '';

                    const dropdown = btn.closest(".dropdown-clone");
                    if (dropdown) dropdown.remove();

                    const form = document.getElementById("editAcademicPeriodForm");
                    form.action = `/academic-periods/${id}`;

                    document.getElementById("edit-name").value = name;
                    document.getElementById("edit-notes").value = notes;
                    document.getElementById("edit-start_date").value = startDate;
                    document.getElementById("edit-end_date").value = endDate;

                    const editModal = document.getElementById("editAcademicPeriodModal");
                    editModal.classList.remove("hidden");
                    editModal.classList.add("flex");
                }
            });

            // Cerrar modals con ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    modals.forEach(id => {
                        const el = document.getElementById(id);
                        if (el && !el.classList.contains('hidden')) {
                            el.classList.add('hidden');
                            el.classList.remove('flex');
                        }
                    });
                }
            });

            @if ($errors->any() && session('form') === 'create')
                const createModal = document.getElementById('academicPeriodModal');
                if (createModal) {
                    createModal.classList.remove('hidden');
                    createModal.classList.add('flex');
                }
            @endif

            @if ($errors->any() && session('form') === 'edit')
                @php
                    $editId = session('edit_id');
                @endphp
                const editModal = document.getElementById('editAcademicPeriodModal');
                const editForm = document.getElementById('editAcademicPeriodForm');

                if (editModal && editForm && '{{ $editId }}') {
                    // setear la acción correcta SIEMPRE que haya error en edición
                    editForm.action = "/academic-periods/{{ $editId }}";

                    // rellenar campos con los valores viejos
                    document.getElementById('edit-name').value = @json(old('name'));
                    document.getElementById('edit-notes').value = @json(old('notes'));
                    document.getElementById('edit-start_date').value = @json(old('start_date'));
                    document.getElementById('edit-end_date').value = @json(old('end_date'));

                    // abrir modal
                    editModal.classList.remove('hidden');
                    editModal.classList.add('flex');
                }
            @endif
        });
    </script>
</x-app-layout>
