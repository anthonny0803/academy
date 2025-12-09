<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Lista de Estudiantes</h1>

                    <!-- Contenedor del formulario y botón -->
                    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">

                        <!-- Formulario de búsqueda -->
                        <form method="GET" action="{{ route('students.index') }}"
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
                                    Inactivo</option>
                            </select>

                            <select id="academic_period_filter" name="academic_period_id"
                                class="block rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                                <option value="">Todos los períodos</option>
                                @foreach ($academicPeriods as $period)
                                    <option value="{{ $period->id }}"
                                        {{ request('academic_period_id') == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select id="section_filter" name="section_id"
                                class="block rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2"
                                {{ request('academic_period_id') ? '' : 'disabled' }}>
                                <option value="">Todas las secciones</option>
                                {{-- Las opciones se cargan dinámicamente con JavaScript --}}
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

                    <!-- Tabla de estudiantes -->
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Código</th>
                                    <th class="py-2 px-4 border-b text-left">Nombre</th>
                                    <th class="py-2 px-4 border-b text-left">Apellido</th>
                                    <th class="py-2 px-4 border-b text-left">Correo</th>
                                    <th class="py-2 px-4 border-b text-left">Representante</th>
                                    <th class="py-2 px-4 border-b text-left">Estado</th>
                                    <th class="py-2 px-4 border-b text-left">Situación</th>
                                    <th class="py-2 px-4 border-b text-left">Gestionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $situationColors = [
                                        'Cursando' => 'bg-green-100 text-green-800 dark:bg-green-600 dark:text-green-100',
                                        'Pausado' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100',
                                        'Baja médica' => 'bg-blue-100 text-blue-800 dark:bg-blue-600 dark:text-blue-100',
                                        'Suspendido' => 'bg-red-100 text-red-800 dark:bg-red-600 dark:text-red-100',
                                        'Situación familiar' => 'bg-purple-100 text-purple-800 dark:bg-purple-600 dark:text-purple-100',
                                    ];
                                    $situations = \App\Enums\StudentSituation::toArray();
                                @endphp

                                @forelse ($students as $student)
                                    @php
                                        $situation = $student->situation?->value ?? 'Cursando';
                                        $colorClass = $situationColors[$situation] ?? 'bg-gray-100 text-gray-800';
                                        $hasActiveEnrollments = $student->enrollments->where('status', 'activo')->count() > 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-2 px-4 border-b">{{ $student->student_code }}</td>
                                        <td class="py-2 px-4 border-b">{{ $student->user->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $student->user->last_name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $student->user->email ?? 'Sin correo' }}</td>
                                        <td class="py-2 px-4 border-b">
                                            {{ $student->representative?->user?->name ?? 'Sin representante' }}
                                            {{ $student->representative?->user?->last_name ?? '' }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <span
                                                class="inline-block {{ $student->is_active ? 'bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100' : 'bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100' }} text-xs px-2 py-1 rounded">
                                                {{ $student->is_active ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="inline-block text-xs px-2 py-1 rounded {{ $colorClass }}">
                                                {{ $situation }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <button data-dropdown-student="{{ $student->id }}"
                                                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                                Acciones
                                            </button>

                                            <!-- Dropdown actions -->
                                            <div id="dropdown-template-{{ $student->id }}" class="hidden">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                                    <li>
                                                        <a href="{{ route('students.show', $student) }}"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            Ver
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('students.enrollments.create', $student) }}"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            Inscribir en Sección
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('students.edit', $student) }}"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button type="button"
                                                            onclick="openSituationModal('{{ $student->id }}')"
                                                            class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            Cambiar Situación
                                                        </button>
                                                    </li>
                                                    @if ($student->is_active && $hasActiveEnrollments)
                                                        <li class="border-t border-gray-200 dark:border-gray-600">
                                                            <a href="{{ route('students.withdraw.form', $student) }}"
                                                                class="block px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                                                                Retirar Estudiante
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Modal para cambiar situación (uno por estudiante) --}}
                                    <div id="situationModal-{{ $student->id }}"
                                        class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                        <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl w-96">
                                            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">
                                                Cambiar Situación
                                            </h3>
                                            
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                                Estudiante: <span class="font-medium">{{ $student->full_name }}</span>
                                            </p>

                                            <form action="{{ route('students.situation', $student) }}" method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <div class="mb-4">
                                                    <label for="situation-{{ $student->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                        Situación
                                                    </label>
                                                    <select name="situation" 
                                                            id="situation-{{ $student->id }}"
                                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                                            required>
                                                        @foreach ($situations as $situationOption)
                                                            <option value="{{ $situationOption }}" 
                                                                    {{ $student->situation?->value === $situationOption ? 'selected' : '' }}>
                                                                {{ $situationOption }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="flex justify-end gap-3">
                                                    <button type="button" 
                                                            onclick="closeSituationModal('{{ $student->id }}')"
                                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                                                        Cancelar
                                                    </button>
                                                    <button type="submit"
                                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                                        Guardar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-4 px-4 text-center text-gray-500 dark:text-gray-400">
                                            {{ request('search') ? 'No se encontraron estudiantes con los criterios de búsqueda.' : 'Ingrese un término de búsqueda para ver resultados.' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if ($students instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="mt-4">
                            {{ $students->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Contenedor del dropdown flotante --}}
    <div id="floating-dropdown"
        class="hidden absolute bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 min-w-[180px]">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const floatingDropdown = document.getElementById('floating-dropdown');
            let currentOpenButton = null;

            // Escuchar clicks en botones de dropdown
            document.querySelectorAll('[data-dropdown-student]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const studentId = this.dataset.dropdownStudent;
                    const template = document.getElementById(
                        `dropdown-template-${studentId}`);

                    // Si el mismo botón, cerrar
                    if (currentOpenButton === this && !floatingDropdown.classList.contains(
                            'hidden')) {
                        floatingDropdown.classList.add('hidden');
                        currentOpenButton = null;
                        return;
                    }

                    // Copiar contenido del template
                    floatingDropdown.innerHTML = template.innerHTML;

                    // Posicionar el dropdown
                    const rect = this.getBoundingClientRect();
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

                    // Calcular posición
                    let top = rect.bottom + scrollTop + 5;
                    let left = rect.left + scrollLeft;

                    // Verificar si el dropdown se sale de la pantalla por la derecha
                    const dropdownWidth = 180;
                    if (left + dropdownWidth > window.innerWidth) {
                        left = rect.right + scrollLeft - dropdownWidth;
                    }

                    // Verificar si el dropdown se sale por abajo
                    const menuHeight = 220; // altura aproximada del menú
                    if (rect.bottom + menuHeight > window.innerHeight) {
                        top = rect.top + scrollTop - menuHeight - 5;
                    }

                    floatingDropdown.style.top = `${top}px`;
                    floatingDropdown.style.left = `${left}px`;
                    floatingDropdown.classList.remove('hidden');
                    currentOpenButton = this;
                });
            });

            // Cerrar dropdown al hacer click fuera
            document.addEventListener('click', function(e) {
                if (!floatingDropdown.contains(e.target) && !e.target.hasAttribute(
                        'data-dropdown-student')) {
                    floatingDropdown.classList.add('hidden');
                    currentOpenButton = null;
                }
            });

            // Cerrar dropdown al scroll
            window.addEventListener('scroll', function() {
                floatingDropdown.classList.add('hidden');
                currentOpenButton = null;
            });

            // FILTRO DINÁMICO DE SECCIONES
            const periodosData = @json($academicPeriods);
            const periodSelect = document.getElementById('academic_period_filter');
            const sectionSelect = document.getElementById('section_filter');
            const selectedSectionId = '{{ request('section_id') }}';

            function updateSections() {
                const selectedPeriodId = periodSelect.value;
                sectionSelect.innerHTML = '<option value="">Todas las secciones</option>';

                if (!selectedPeriodId) {
                    sectionSelect.disabled = true;
                    return;
                }

                const periodo = periodosData.find(p => p.id == selectedPeriodId);
                if (periodo && periodo.sections) {
                    periodo.sections.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.id;
                        option.textContent = section.name;
                        if (section.id == selectedSectionId) {
                            option.selected = true;
                        }
                        sectionSelect.appendChild(option);
                    });
                    sectionSelect.disabled = false;
                } else {
                    sectionSelect.disabled = true;
                }
            }

            periodSelect.addEventListener('change', updateSections);

            // Cargar secciones iniciales si hay período seleccionado
            if (periodSelect.value) {
                updateSections();
            }
        });

        // Funciones para el modal de situación
        function openSituationModal(studentId) {
            document.getElementById('situationModal-' + studentId).classList.remove('hidden');
            // Cerrar el dropdown flotante
            document.getElementById('floating-dropdown').classList.add('hidden');
        }

        function closeSituationModal(studentId) {
            document.getElementById('situationModal-' + studentId).classList.add('hidden');
        }

        // Cerrar modal al hacer click fuera
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>