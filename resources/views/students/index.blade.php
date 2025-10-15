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
                                    <th class="py-2 px-4 border-b text-left">Gestionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students as $student)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-2 px-4 border-b">{{ $student->student_code }}</td>
                                        <td class="py-2 px-4 border-b">{{ $student->user?->name ?? '-' }}</td>
                                        <td class="py-2 px-4 border-b">{{ $student->user?->last_name ?? '-' }}</td>
                                        <td class="py-2 px-4 border-b">{{ $student->user?->email ?? '-' }}</td>
                                        <td class="py-2 px-4 border-b">
                                            {{ $student->representative?->user?->name ?? '-' }}
                                            {{ $student->representative?->user?->last_name ?? '' }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <span
                                                class="inline-block {{ $student->is_active ? 'bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100' : 'bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100' }} text-xs px-2 py-1 rounded">
                                                {{ $student->is_active ? 'Activo' : 'Inactivo' }}
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
                                                    <li><a href="{{ route('students.show', $student) }}"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Ver</a>
                                                    </li>
                                                    <li><a href="{{ route('students.enrollments.create', $student) }}"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Inscribir
                                                            en Sección</a>
                                                    </li>
                                                    <li><a href="{{ route('students.edit', $student) }}"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Editar</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-2 px-4 text-center text-gray-300">
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
                    @if ($students->count() > 0)
                        <div class="mt-4">
                            {{ $students->appends(request()->except('page'))->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Script para filtro cascada de períodos → secciones -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const academicPeriodsData = {!! json_encode(
                $academicPeriods->map(function ($period) {
                    return [
                        'id' => $period->id,
                        'sections' => $period->sections->map(fn($s) => ['id' => $s->id, 'name' => $s->name]),
                    ];
                }),
            ) !!};

            const periodSelect = document.getElementById('academic_period_filter');
            const sectionSelect = document.getElementById('section_filter');
            const oldSectionId = '{{ request('section_id') }}';

            periodSelect.addEventListener('change', function() {
                const periodId = parseInt(this.value);
                sectionSelect.innerHTML = '<option value="">Todas las secciones</option>';
                sectionSelect.disabled = !periodId;

                if (periodId) {
                    const period = academicPeriodsData.find(p => p.id === periodId);
                    if (period && period.sections) {
                        period.sections.forEach(section => {
                            const option = document.createElement('option');
                            option.value = section.id;
                            option.textContent = section.name;
                            if (section.id == oldSectionId) {
                                option.selected = true;
                            }
                            sectionSelect.appendChild(option);
                        });
                    }
                }
            });

            // Trigger inicial si hay old value
            if (periodSelect.value) {
                periodSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>

    <!-- Script para dropdowns -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll("[data-dropdown-student]").forEach(btn => {
                btn.addEventListener("click", () => {
                    const studentId = btn.dataset.dropdownStudent;
                    let existing = document.getElementById("dropdownMenu-" + studentId);

                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }

                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());

                    const tpl = document.getElementById("dropdown-template-" + studentId);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + studentId;
                    clone.classList.remove("hidden");
                    clone.classList.add(
                        "dropdown-clone", "fixed", "z-50", "w-40",
                        "bg-white", "dark:bg-gray-800", "border", "border-gray-200",
                        "dark:border-gray-700", "rounded", "shadow-lg"
                    );

                    const rect = btn.getBoundingClientRect();
                    const menuHeight = 160;
                    const espacioAbajo = window.innerHeight - rect.bottom;

                    // Decidir si va arriba o abajo
                    if (espacioAbajo >= menuHeight + 10) {
                        // Hay espacio abajo
                        clone.style.top = (rect.bottom + 5) + "px";
                    } else {
                        // No hay espacio, va arriba
                        clone.style.top = (rect.top - menuHeight - 5) + "px";
                    }

                    clone.style.left = rect.left + "px";
                    document.body.appendChild(clone);

                    // Cerrar dropdown al hacer scroll (para evitar desincronización)
                    const scrollHandler = () => {
                        clone.remove();
                        window.removeEventListener('scroll', scrollHandler, true);
                    };
                    window.addEventListener('scroll', scrollHandler, true);
                });
            });

            document.addEventListener("click", e => {
                if (!e.target.closest("[data-dropdown-student]") &&
                    !e.target.closest(".dropdown-clone")) {
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                }
            });
        });
    </script>

</x-app-layout>
