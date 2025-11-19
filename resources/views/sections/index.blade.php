<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Encabezado con búsqueda y botón Crear -->
                    <div class="flex justify-between items-center mb-4">
                        <!-- Form de búsqueda -->
                        <form method="GET" action="{{ route('sections.index') }}" class="flex gap-2">
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

                            <select name="academic_period_id"
                                class="block rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                                <option value="">Todos los períodos</option>
                                @foreach ($academicPeriods as $period)
                                    <option value="{{ $period->id }}"
                                        {{ request('academic_period_id') == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit"
                                class="bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700">Buscar</button>
                        </form>

                        <!-- Botón para abrir modal de creación -->
                        <button type="button" class="bg-green-600 text-white rounded-md px-4 py-2 hover:bg-green-700"
                            onclick="document.getElementById('sectionModal').classList.remove('hidden'); document.getElementById('sectionModal').classList.add('flex');">
                            Crear Sección
                        </button>
                    </div>

                    <h1 class="text-2xl font-bold mb-4 text-white dark:text-white">Lista de Secciones</h1>

                    <!-- Tabla de secciones -->
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Código</th>
                                    <th class="py-2 px-4 border-b text-left">Nombre</th>
                                    <th class="py-2 px-4 border-b text-left">Período Académico</th>
                                    <th class="py-2 px-4 border-b text-left">Capacidad</th>
                                    <th class="py-2 px-4 border-b text-left">Descripción</th>
                                    <th class="py-2 px-4 border-b text-left">Estado</th>
                                    <th class="py-2 px-4 border-b text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sections as $section)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-2 px-4 border-b">{{ $section->id }}</td>
                                        <td class="py-2 px-4 border-b">{{ $section->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $section->academicPeriod->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $section->capacity }}</td>
                                        <td class="py-2 px-4 border-b">{{ $section->description ?? '-' }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <form method="POST" action="{{ route('sections.toggle', $section) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="relative inline-flex h-6 w-11 items-center rounded-full cursor-pointer {{ $section->is_active ? 'bg-blue-600' : 'bg-gray-400' }}">
                                                    <span
                                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $section->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                                </button>
                                            </form>
                                            <span
                                                class="inline-block {{ $section->is_active ? 'bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100' : 'bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100' }} text-xs px-2 py-1 rounded mr-1">
                                                {{ $section->is_active ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b relative">
                                            <!-- Botón Acciones -->
                                            <button data-dropdown-section="{{ $section->id }}"
                                                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                                Acciones
                                            </button>

                                            <!-- Dropdown template -->
                                            <div id="dropdown-template-{{ $section->id }}" class="hidden">
                                                <ul class="py-1 text-sm text-gray-200">
                                                    <li>
                                                        <a href="{{ route('sections.show', $section) }}"
                                                            class="block px-4 py-2 hover:bg-gray-700">
                                                            Asignar materias
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button type="button" data-id="{{ $section->id }}"
                                                            data-academic-period-id="{{ $section->academic_period_id }}"
                                                            data-name="{{ e($section->name) }}"
                                                            data-description="{{ e($section->description) }}"
                                                            data-capacity="{{ $section->capacity }}"
                                                            class="block w-full text-left px-4 py-2 hover:bg-gray-700 edit-btn">
                                                            Editar
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form method="POST"
                                                            action="{{ route('sections.destroy', $section) }}">
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
                                        <td colspan="7" class="py-2 px-4 text-center text-gray-300">
                                            No se encontraron secciones
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if ($sections->count() > 0)
                        <div class="mt-4 text-white">
                            {{ $sections->appends(request()->except('page'))->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Modal CREAR (sin cambios) --}}
    <div id="sectionModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md shadow-lg my-8 relative">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white text-center">Nueva Sección</h2>

            <form action="{{ route('sections.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <x-input-label for="academic_period_id" :value="__('Período Académico *')" />
                    <select id="academic_period_id" name="academic_period_id" required
                        class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">
                        <option value="">Seleccione un período</option>
                        @foreach ($academicPeriods as $period)
                            <option value="{{ $period->id }}"
                                {{ old('academic_period_id') == $period->id ? 'selected' : '' }}>
                                {{ $period->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('academic_period_id')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="name" :value="__('Nombre *')" />
                    <x-text-input id="name" name="name" type="text" :value="old('name')"
                        class="block mt-1 w-full" required autocomplete="off" placeholder="Ej: 1ro A, 3ro C" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="capacity" :value="__('Capacidad *')" />
                    <x-text-input id="capacity" name="capacity" type="number" :value="old('capacity')"
                        class="block mt-1 w-full" required min="1" placeholder="Ej: 30" />
                    <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="description" :value="__('Descripción')" />
                    <textarea id="description" name="description"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2"
                        placeholder="Descripción opcional">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        onclick="document.getElementById('sectionModal').classList.add('hidden'); document.getElementById('sectionModal').classList.remove('flex');">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal EDITAR (sin cambios) --}}
    <div id="editSectionModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md shadow-lg my-8 relative">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white text-center">Editar Sección</h2>

            <form id="editSectionForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <x-input-label for="edit-academic_period_id" :value="__('Período Académico *')" />
                    <select id="edit-academic_period_id" name="academic_period_id" required
                        class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">
                        <option value="">Seleccione un período</option>
                        @foreach ($academicPeriods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('academic_period_id')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit-name" :value="__('Nombre *')" />
                    <x-text-input id="edit-name" name="name" type="text" class="block mt-1 w-full" required
                        autocomplete="off" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit-capacity" :value="__('Capacidad *')" />
                    <x-text-input id="edit-capacity" name="capacity" type="number" class="block mt-1 w-full"
                        required min="1" />
                    <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit-description" :value="__('Descripción')" />
                    <textarea id="edit-description" name="description"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        onclick="document.getElementById('editSectionModal').classList.add('hidden'); document.getElementById('editSectionModal').classList.remove('flex');">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script (SIN CAMBIOS, mantén tu mismo script) --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            function toggleBodyScroll(disable) {
                document.body.style.overflow = disable ? 'hidden' : '';
            }

            const modals = ['sectionModal', 'editSectionModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    const observer = new MutationObserver((mutations) => {
                        mutations.forEach((mutation) => {
                            if (mutation.attributeName === 'class') {
                                toggleBodyScroll(!modal.classList.contains('hidden'));
                            }
                        });
                    });
                    observer.observe(modal, {
                        attributes: true
                    });
                }
            });

            document.querySelectorAll("[data-dropdown-section]").forEach(btn => {
                btn.addEventListener("click", () => {
                    const sectionId = btn.dataset.dropdownSection;
                    let existing = document.getElementById("dropdownMenu-" + sectionId);

                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }

                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());

                    const tpl = document.getElementById("dropdown-template-" + sectionId);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + sectionId;
                    clone.classList.remove("hidden");
                    clone.classList.add("dropdown-clone", "fixed", "z-50", "w-40", "bg-gray-800",
                        "border", "border-gray-700", "rounded", "shadow-lg");

                    const rect = btn.getBoundingClientRect();
                    const menuHeight = 120;
                    const espacioAbajo = window.innerHeight - rect.bottom;

                    if (espacioAbajo >= menuHeight) {
                        clone.style.top = rect.bottom + "px";
                    } else {
                        clone.style.top = (rect.top - menuHeight) + "px";
                    }

                    clone.style.left = rect.left + "px";
                    document.body.appendChild(clone);
                });
            });

            document.addEventListener("click", e => {
                if (!e.target.closest("[data-dropdown-section]") && !e.target.closest(".dropdown-clone")) {
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                }
            });

            document.addEventListener("click", e => {
                if (e.target.classList.contains("edit-btn")) {
                    const btn = e.target;
                    const id = btn.getAttribute('data-id');
                    const academicPeriodId = btn.getAttribute('data-academic-period-id');
                    const name = btn.getAttribute('data-name') || '';
                    const description = btn.getAttribute('data-description') || '';
                    const capacity = btn.getAttribute('data-capacity') || '';

                    const dropdown = btn.closest(".dropdown-clone");
                    if (dropdown) dropdown.remove();

                    const form = document.getElementById("editSectionForm");
                    form.action = `/sections/${id}`;

                    document.getElementById("edit-academic_period_id").value = academicPeriodId;
                    document.getElementById("edit-name").value = name;
                    document.getElementById("edit-description").value = description;
                    document.getElementById("edit-capacity").value = capacity;

                    const editModal = document.getElementById("editSectionModal");
                    editModal.classList.remove("hidden");
                    editModal.classList.add("flex");
                }
            });

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
                const createModal = document.getElementById('sectionModal');
                if (createModal) {
                    createModal.classList.remove('hidden');
                    createModal.classList.add('flex');
                }
            @endif

            @if ($errors->any() && session('form') === 'edit')
                @php
                    $editId = session('edit_id');
                @endphp
                const editModal = document.getElementById('editSectionModal');
                const editForm = document.getElementById('editSectionForm');

                if (editModal && editForm && '{{ $editId }}') {
                    // setear la acción correcta SIEMPRE que haya error en edición
                    editForm.action = "/sections/{{ $editId }}";

                    // rellenar campos con los valores viejos
                    document.getElementById('edit-academic_period_id').value = @json(old('academic_period_id'));
                    document.getElementById('edit-name').value = @json(old('name'));
                    document.getElementById('edit-capacity').value = @json(old('capacity'));
                    document.getElementById('edit-description').value = @json(old('description'));

                    // abrir modal
                    editModal.classList.remove('hidden');
                    editModal.classList.add('flex');
                }
            @endif
        });
    </script>
</x-app-layout>
