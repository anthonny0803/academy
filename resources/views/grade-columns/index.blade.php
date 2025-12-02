<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h1 class="text-2xl font-bold mb-2 text-white dark:text-white">Configuración de Evaluaciones</h1>
                    <p class="text-gray-400 mb-4">
                        {{ $sectionSubjectTeacher->subject->name }} - 
                        {{ $sectionSubjectTeacher->section->name }} 
                        ({{ $sectionSubjectTeacher->section->academicPeriod->name }})
                        <span class="text-gray-500">| Prof: {{ $sectionSubjectTeacher->teacher->user->full_name }}</span>
                    </p>

                    {{-- Barra de progreso --}}
                    <div class="mb-6 p-4 bg-gray-900 rounded-lg">
                        <div class="flex justify-between text-sm text-gray-400 mb-1">
                            <span>Peso total configurado</span>
                            <span class="{{ $isConfigurationComplete ? 'text-green-500' : 'text-yellow-500' }}">
                                {{ number_format($totalWeight, 2) }}% / 100%
                            </span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-300 
                                {{ $isConfigurationComplete ? 'bg-green-500' : ($totalWeight > 100 ? 'bg-red-500' : 'bg-yellow-500') }}"
                                style="width: {{ min($totalWeight, 100) }}%">
                            </div>
                        </div>
                        @if($isConfigurationComplete)
                            <p class="text-green-500 text-sm mt-2">✓ Configuración completa. Puede comenzar a calificar.</p>
                        @else
                            <p class="text-yellow-500 text-sm mt-2">
                                Falta {{ number_format($remainingWeight, 2) }}% para completar la configuración.
                            </p>
                        @endif
                    </div>

                    {{-- Contenedor de acciones --}}
                    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">

                        <div class="flex gap-2 flex-wrap items-center">
                            <a href="{{ route('sections.show', $sectionSubjectTeacher->section_id) }}"
                                class="underline px-4 py-2 text-sm text-white hover:text-gray-300 rounded-md">
                                Volver a Sección
                            </a>

                            @if($isConfigurationComplete)
                                <a href="{{ route('grades.index', $sectionSubjectTeacher) }}"
                                    class="bg-blue-600 text-white rounded-md px-4 py-2 hover:bg-blue-700">
                                    Ir a Calificaciones
                                </a>
                            @endif
                        </div>

                        @if($remainingWeight > 0)
                            <button type="button" onclick="openModal('createModal')"
                                class="bg-green-600 text-white rounded-md px-4 py-2 hover:bg-green-700">
                                Agregar Evaluación
                            </button>
                        @endif

                    </div>

                    {{-- Tabla de evaluaciones --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Orden</th>
                                    <th class="py-2 px-4 border-b text-left">Nombre</th>
                                    <th class="py-2 px-4 border-b text-left">Ponderación</th>
                                    <th class="py-2 px-4 border-b text-left">Observación</th>
                                    <th class="py-2 px-4 border-b text-left">Estado</th>
                                    <th class="py-2 px-4 border-b text-left">Gestionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sectionSubjectTeacher->gradeColumns as $column)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-2 px-4 border-b">{{ $column->display_order }}</td>
                                        <td class="py-2 px-4 border-b font-medium">{{ $column->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ number_format($column->weight, 2) }}%</td>
                                        <td class="py-2 px-4 border-b text-gray-400">
                                            {{ $column->observation ?? '-' }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            @if($column->hasGrades())
                                                <span class="inline-block bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 text-xs px-2 py-1 rounded">
                                                    Con notas
                                                </span>
                                            @else
                                                <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 text-xs px-2 py-1 rounded">
                                                    Sin notas
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            @if(!$column->hasGrades())
                                                <button data-dropdown-column="{{ $column->id }}"
                                                    class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                                    Acciones
                                                </button>

                                                <div id="dropdown-template-{{ $column->id }}" class="hidden">
                                                    <ul class="py-1 text-sm text-gray-200">
                                                        <li>
                                                            <button type="button"
                                                                onclick="openEditModal({{ $column->id }}, '{{ $column->name }}', {{ $column->weight }}, {{ $column->display_order }}, '{{ addslashes($column->observation ?? '') }}')"
                                                                class="w-full text-left block px-4 py-2 hover:bg-gray-700">
                                                                Editar
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button type="button"
                                                                onclick="confirmDelete({{ $column->id }}, '{{ $column->name }}')"
                                                                class="w-full text-left block px-4 py-2 hover:bg-gray-700 text-red-500">
                                                                Eliminar
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @else
                                                <span class="text-gray-500 text-sm italic">Bloqueado</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 px-4 text-center text-gray-300">
                                            No hay evaluaciones configuradas. Agregue al menos una.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($sectionSubjectTeacher->gradeColumns->count() > 0)
                                <tfoot>
                                    <tr class="bg-gray-900">
                                        <td class="py-2 px-4 border-t font-bold" colspan="2">Total</td>
                                        <td class="py-2 px-4 border-t font-bold {{ $isConfigurationComplete ? 'text-green-500' : 'text-yellow-500' }}">
                                            {{ number_format($totalWeight, 2) }}%
                                        </td>
                                        <td colspan="3" class="py-2 px-4 border-t"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal Crear --}}
    <div id="createModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md mx-4">
            <h3 class="text-xl font-bold text-white mb-4">Nueva Evaluación</h3>
            
            <form method="POST" action="{{ route('grade-columns.store', $sectionSubjectTeacher) }}">
                @csrf

                <div class="mb-4">
                    <x-input-label for="name" :value="__('Nombre')" />
                    <x-text-input id="name" class="block mt-1 w-full uppercase" type="text" name="name"
                        :value="old('name')" required autofocus autocomplete="off" placeholder="Ej: PARCIAL 1" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="weight" :value="__('Ponderación (%)')" />
                    <x-text-input id="weight" class="block mt-1 w-full" type="number" name="weight"
                        :value="old('weight')" required min="0.01" max="{{ $remainingWeight }}" step="0.01" />
                    <p class="text-gray-500 text-sm mt-1">Disponible: {{ number_format($remainingWeight, 2) }}%</p>
                    <x-input-error :messages="$errors->get('weight')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="display_order" :value="__('Orden')" />
                    <x-text-input id="display_order" class="block mt-1 w-full" type="number" name="display_order"
                        :value="old('display_order', $sectionSubjectTeacher->gradeColumns->count() + 1)" min="1" />
                    <x-input-error :messages="$errors->get('display_order')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="observation" :value="__('Observación (opcional)')" />
                    <textarea id="observation" name="observation" rows="2"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm uppercase">{{ old('observation') }}</textarea>
                    <x-input-error :messages="$errors->get('observation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button type="button" onclick="closeModal('createModal')"
                        class="underline text-sm text-gray-400 hover:text-white rounded-md">
                        Cancelar
                    </button>
                    <x-primary-button class="ms-4">
                        {{ __('Guardar') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Editar --}}
    <div id="editModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md mx-4">
            <h3 class="text-xl font-bold text-white mb-4">Editar Evaluación</h3>
            
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <x-input-label for="edit_name" :value="__('Nombre')" />
                    <x-text-input id="edit_name" class="block mt-1 w-full uppercase" type="text" name="name"
                        required autocomplete="off" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_weight" :value="__('Ponderación (%)')" />
                    <x-text-input id="edit_weight" class="block mt-1 w-full" type="number" name="weight"
                        required min="0.01" max="100" step="0.01" />
                    <x-input-error :messages="$errors->get('weight')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_display_order" :value="__('Orden')" />
                    <x-text-input id="edit_display_order" class="block mt-1 w-full" type="number" name="display_order" min="1" />
                    <x-input-error :messages="$errors->get('display_order')" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_observation" :value="__('Observación (opcional)')" />
                    <textarea id="edit_observation" name="observation" rows="2"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm uppercase"></textarea>
                    <x-input-error :messages="$errors->get('observation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button type="button" onclick="closeModal('editModal')"
                        class="underline text-sm text-gray-400 hover:text-white rounded-md">
                        Cancelar
                    </button>
                    <x-primary-button class="ms-4">
                        {{ __('Actualizar') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Eliminar --}}
    <div id="deleteModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-sm mx-4">
            <h3 class="text-xl font-bold text-white mb-4">Confirmar Eliminación</h3>
            <p class="text-gray-400 mb-4">
                ¿Está seguro de eliminar la evaluación "<span id="deleteColumnName" class="text-white font-medium"></span>"?
            </p>
            
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')

                <div class="flex items-center justify-end mt-4">
                    <button type="button" onclick="closeModal('deleteModal')"
                        class="underline text-sm text-gray-400 hover:text-white rounded-md">
                        Cancelar
                    </button>
                    <button type="submit" class="ms-4 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        // Modales
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function openEditModal(id, name, weight, order, observation) {
            document.getElementById('editForm').action = `/grade-columns/${id}`;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_weight').value = weight;
            document.getElementById('edit_display_order').value = order;
            document.getElementById('edit_observation').value = observation;
            openModal('editModal');
        }

        function confirmDelete(id, name) {
            document.getElementById('deleteForm').action = `/grade-columns/${id}`;
            document.getElementById('deleteColumnName').textContent = name;
            openModal('deleteModal');
        }

        // Cerrar modal al hacer click fuera
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });

        // Dropdowns
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll("[data-dropdown-column]").forEach(btn => {
                btn.addEventListener("click", () => {
                    const columnId = btn.dataset.dropdownColumn;
                    let existing = document.getElementById("dropdownMenu-" + columnId);

                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }

                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());

                    const tpl = document.getElementById("dropdown-template-" + columnId);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + columnId;
                    clone.classList.remove("hidden");
                    clone.classList.add(
                        "dropdown-clone", "fixed", "z-50", "w-40",
                        "bg-gray-800", "border", "border-gray-700",
                        "rounded", "shadow-lg"
                    );

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
                if (!e.target.closest("[data-dropdown-column]") && !e.target.closest(".dropdown-clone")) {
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                }
            });
        });
    </script>
</x-app-layout>