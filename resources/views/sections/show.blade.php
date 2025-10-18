<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            Sección: {{ $section->name }}
                        </h1>
                        <a href="{{ route('sections.index') }}"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                            Volver a Secciones
                        </a>
                    </div>

                    {{-- Información de la sección --}}
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Período Académico</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $section->academicPeriod->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Capacidad</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $section->capacity }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Estado</label>
                                <span class="inline-block {{ $section->is_active ? 'bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100' : 'bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100' }} text-xs px-2 py-1 rounded">
                                    {{ $section->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Materias y Profesores --}}
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Materias y Profesores Asignados</h2>
                        <button type="button" class="bg-green-600 text-white rounded-md px-4 py-2 hover:bg-green-700"
                            onclick="document.getElementById('assignModal').classList.remove('hidden'); document.getElementById('assignModal').classList.add('flex');">
                            + Agregar Materia/Profesor
                        </button>
                    </div>

                    {{-- Tabla de asignaciones --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Materia</th>
                                    <th class="py-2 px-4 border-b text-left">Profesor</th>
                                    <th class="py-2 px-4 border-b text-center">Principal</th>
                                    <th class="py-2 px-4 border-b text-center">Estado</th>
                                    <th class="py-2 px-4 border-b text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($section->sectionSubjectTeachers as $sst)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-2 px-4 border-b">{{ $sst->subject->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $sst->teacher->user->full_name }}</td>
                                        <td class="py-2 px-4 border-b text-center">
                                            @if($sst->is_primary)
                                                <span class="inline-block bg-blue-100 dark:bg-blue-600 text-blue-800 dark:text-blue-100 text-xs px-2 py-1 rounded">
                                                    Sí
                                                </span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">No</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <span class="inline-block 
                                                @if($sst->status === 'activo') bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100
                                                @elseif($sst->status === 'suplente') bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100
                                                @else bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-100
                                                @endif
                                                text-xs px-2 py-1 rounded">
                                                {{ ucfirst($sst->status) }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <button data-dropdown-sst="{{ $sst->id }}"
                                                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                                Acciones
                                            </button>

                                            <div id="dropdown-template-{{ $sst->id }}" class="hidden">
                                                <ul class="py-1 text-sm text-gray-200">
                                                    <li>
                                                        <button type="button" 
                                                            data-id="{{ $sst->id }}"
                                                            data-subject-id="{{ $sst->subject_id }}"
                                                            data-teacher-id="{{ $sst->teacher_id }}"
                                                            data-is-primary="{{ $sst->is_primary ? '1' : '0' }}"
                                                            data-status="{{ $sst->status }}"
                                                            class="block w-full text-left px-4 py-2 hover:bg-gray-700 edit-sst-btn">
                                                            Editar
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('section-subject-teacher.destroy', $sst) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                onclick="return confirm('¿Seguro que deseas eliminar esta asignación?');"
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
                                        <td colspan="5" class="py-4 px-4 text-center text-gray-300">
                                            No hay materias/profesores asignados a esta sección
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal CREAR Asignación --}}
    <div id="assignModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md shadow-lg my-8 relative">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white text-center">Asignar Materia/Profesor</h2>

            <form action="{{ route('section-subject-teacher.store') }}" method="POST">
                @csrf
                <input type="hidden" name="section_id" value="{{ $section->id }}">

                <div class="mb-4">
                    <x-input-label for="subject_id" :value="__('Materia *')" />
                    <select id="subject_id" name="subject_id" required
                        class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">
                        <option value="">Seleccione una materia</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('subject_id')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="teacher_id" :value="__('Profesor *')" />
                    <select id="teacher_id" name="teacher_id" required disabled
                        class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">
                        <option value="">Primero selecciona una materia</option>
                    </select>
                    <x-input-error :messages="$errors->get('teacher_id')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_primary" value="1" {{ old('is_primary', true) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">¿Es profesor principal?</span>
                    </label>
                    <x-input-error :messages="$errors->get('is_primary')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="status" :value="__('Estado *')" />
                    <select id="status" name="status" required
                        class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">
                        <option value="activo" {{ old('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="suplente" {{ old('status') == 'suplente' ? 'selected' : '' }}>Suplente</option>
                        <option value="inactivo" {{ old('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        onclick="document.getElementById('assignModal').classList.add('hidden'); document.getElementById('assignModal').classList.remove('flex');">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal EDITAR Asignación --}}
    <div id="editSSTModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-md shadow-lg my-8 relative">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white text-center">Editar Asignación</h2>

            <form id="editSSTForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <x-input-label for="edit-subject_id" :value="__('Materia')" />
                    <input type="text" id="edit-subject-name" disabled
                        class="block mt-1 w-full rounded-md bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 p-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">No se puede cambiar la materia. Elimina y crea nueva asignación.</p>
                </div>

                <div class="mb-4">
                    <x-input-label for="edit-teacher_id" :value="__('Profesor')" />
                    <input type="text" id="edit-teacher-name" disabled
                        class="block mt-1 w-full rounded-md bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 p-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">No se puede cambiar el profesor. Elimina y crea nueva asignación.</p>
                </div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="edit-is_primary" name="is_primary" value="1"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">¿Es profesor principal?</span>
                    </label>
                </div>

                <div class="mb-4">
                    <x-input-label for="edit-status" :value="__('Estado *')" />
                    <select id="edit-status" name="status" required
                        class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-black dark:text-white p-2">
                        <option value="activo">Activo</option>
                        <option value="suplente">Suplente</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                        onclick="document.getElementById('editSSTModal').classList.add('hidden'); document.getElementById('editSSTModal').classList.remove('flex');">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Datos de profesores por materia (desde backend)
            const teachersBySubject = @json($teachersBySubject);

            // Select de materia
            const subjectSelect = document.getElementById('subject_id');
            const teacherSelect = document.getElementById('teacher_id');

            // Cargar profesores según materia seleccionada
            subjectSelect.addEventListener('change', function() {
                const subjectId = parseInt(this.value);
                teacherSelect.innerHTML = '<option value="">Seleccione un profesor</option>';
                teacherSelect.disabled = !subjectId;

                if (subjectId && teachersBySubject[subjectId]) {
                    teachersBySubject[subjectId].forEach(teacher => {
                        const option = document.createElement('option');
                        option.value = teacher.id;
                        option.textContent = teacher.name;
                        teacherSelect.appendChild(option);
                    });
                }
            });

            // Si hay old values, recargar profesores
            @if(old('subject_id'))
                subjectSelect.dispatchEvent(new Event('change'));
                teacherSelect.value = '{{ old('teacher_id') }}';
            @endif

            // Reabrir modal si hay errores
            @if($errors->any())
                document.getElementById('assignModal').classList.remove('hidden');
                document.getElementById('assignModal').classList.add('flex');
            @endif

            // Dropdown logic
            document.querySelectorAll("[data-dropdown-sst]").forEach(btn => {
                btn.addEventListener("click", () => {
                    const sstId = btn.dataset.dropdownSst;
                    let existing = document.getElementById("dropdownMenu-" + sstId);

                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }

                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());

                    const tpl = document.getElementById("dropdown-template-" + sstId);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + sstId;
                    clone.classList.remove("hidden");
                    clone.classList.add("dropdown-clone", "fixed", "z-50", "w-40", "bg-gray-800",
                        "border", "border-gray-700", "rounded", "shadow-lg");

                    const rect = btn.getBoundingClientRect();
                    const menuHeight = 100;
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
                if (!e.target.closest("[data-dropdown-sst]") && !e.target.closest(".dropdown-clone")) {
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                }
            });

            // Abrir modal de edición
            document.addEventListener("click", e => {
                if (e.target.classList.contains("edit-sst-btn")) {
                    const btn = e.target;
                    const id = btn.getAttribute('data-id');
                    const subjectId = btn.getAttribute('data-subject-id');
                    const teacherId = btn.getAttribute('data-teacher-id');
                    const isPrimary = btn.getAttribute('data-is-primary') === '1';
                    const status = btn.getAttribute('data-status');

                    const dropdown = btn.closest(".dropdown-clone");
                    if (dropdown) dropdown.remove();

                    const form = document.getElementById("editSSTForm");
                    form.action = `/section-subject-teacher/${id}`;

                    // Mostrar nombres (readonly)
                    const subjectName = subjectSelect.querySelector(`option[value="${subjectId}"]`)?.textContent || '';
                    document.getElementById("edit-subject-name").value = subjectName;

                    // Cargar profesores para obtener el nombre
                    if (teachersBySubject[subjectId]) {
                        const teacher = teachersBySubject[subjectId].find(t => t.id == teacherId);
                        document.getElementById("edit-teacher-name").value = teacher ? teacher.name : '';
                    }

                    document.getElementById("edit-is_primary").checked = isPrimary;
                    document.getElementById("edit-status").value = status;

                    const editModal = document.getElementById("editSSTModal");
                    editModal.classList.remove("hidden");
                    editModal.classList.add("flex");
                }
            });
        });
    </script>
</x-app-layout>