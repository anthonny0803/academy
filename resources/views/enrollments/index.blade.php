<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Gestión de Inscripciones</h1>

                    <!-- Formulario de búsqueda y filtros -->
                    <div class="flex justify-between items-center mb-4 flex-wrap gap-2">
                        <form method="GET" action="{{ route('enrollments.index') }}"
                            class="flex gap-2 flex-wrap items-center">

                            <input type="text" name="search" placeholder="Buscar estudiante..."
                                value="{{ request('search') }}"
                                class="block w-80 rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-800 p-2"
                                autocomplete="off" style="text-transform:uppercase;">

                            <select name="status"
                                class="block rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2">
                                <option value="">Todos los estados</option>
                                @foreach ($statuses as $statusValue)
                                    <option value="{{ $statusValue }}"
                                        {{ request('status') === $statusValue ? 'selected' : '' }}>
                                        {{ ucfirst($statusValue) }}
                                    </option>
                                @endforeach
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

                    <!-- Tabla de inscripciones -->
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Estudiante</th>
                                    <th class="py-2 px-4 border-b text-left">Código</th>
                                    <th class="py-2 px-4 border-b text-left">Sección</th>
                                    <th class="py-2 px-4 border-b text-left">Período</th>
                                    <th class="py-2 px-4 border-b text-left">Estado</th>
                                    <th class="py-2 px-4 border-b text-left">Fecha</th>
                                    <th class="py-2 px-4 border-b text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($enrollments as $enrollment)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-2 px-4 border-b">
                                            {{ $enrollment->student->user->name }}
                                            {{ $enrollment->student->user->last_name }}
                                        </td>
                                        <td class="py-2 px-4 border-b">{{ $enrollment->student->student_code }}</td>
                                        <td class="py-2 px-4 border-b">{{ $enrollment->section->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $enrollment->section->academicPeriod->name }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <span
                                                class="inline-block 
                                                @if ($enrollment->status === 'activo') bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100
                                                @elseif($enrollment->status === 'completado') bg-blue-100 dark:bg-blue-600 text-blue-800 dark:text-blue-100
                                                @elseif($enrollment->status === 'retirado') bg-red-100 dark:bg-red-600 text-red-800 dark:text-red-100
                                                @elseif($enrollment->status === 'transferido') bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100
                                                @else bg-purple-100 dark:bg-purple-600 text-purple-800 dark:text-purple-100 @endif
                                                text-xs px-2 py-1 rounded">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b">{{ $enrollment->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <button data-dropdown-enrollment="{{ $enrollment->id }}"
                                                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                                Acciones
                                            </button>

                                            <div id="dropdown-template-{{ $enrollment->id }}" class="hidden">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                                    <li><a href="{{ route('enrollments.show', $enrollment) }}"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Ver
                                                            Detalles</a>
                                                    </li>
                                                    @if ($enrollment->status === 'activo')
                                                        <li><a href="{{ route('enrollments.transfer.form', $enrollment) }}"
                                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Transferir
                                                                Sección</a>
                                                        </li>
                                                        <li><a href="{{ route('enrollments.promote.form', $enrollment) }}"
                                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Promover
                                                                Grado</a>
                                                        </li>
                                                        <li class="border-t border-gray-200 dark:border-gray-600">
                                                            <form
                                                                action="{{ route('enrollments.destroy', $enrollment) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('¿Estás seguro de eliminar esta inscripción?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="block w-full text-left px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                                                                    Eliminar Inscripción
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-2 px-4 text-center text-gray-300">
                                            No se encontraron inscripciones
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if ($enrollments->count() > 0)
                        <div class="mt-4">
                            {{ $enrollments->appends(request()->except('page'))->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Script para filtro cascada Período → Sección -->
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
            document.querySelectorAll("[data-dropdown-enrollment]").forEach(btn => {
                btn.addEventListener("click", () => {
                    const enrollmentId = btn.dataset.dropdownEnrollment;
                    let existing = document.getElementById("dropdownMenu-" + enrollmentId);

                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }

                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());

                    const tpl = document.getElementById("dropdown-template-" + enrollmentId);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + enrollmentId;
                    clone.classList.remove("hidden");
                    clone.classList.add(
                        "dropdown-clone", "fixed", "z-50", "w-48",
                        "bg-white", "dark:bg-gray-800", "border", "border-gray-200",
                        "dark:border-gray-700", "rounded", "shadow-lg"
                    );

                    const rect = btn.getBoundingClientRect();
                    const menuHeight = 180;
                    const espacioAbajo = window.innerHeight - rect.bottom;

                    if (espacioAbajo >= menuHeight + 10) {
                        clone.style.top = (rect.bottom + 5) + "px";
                    } else {
                        clone.style.top = (rect.top - menuHeight - 5) + "px";
                    }

                    clone.style.left = rect.left + "px";
                    document.body.appendChild(clone);

                    const scrollHandler = () => {
                        clone.remove();
                        window.removeEventListener('scroll', scrollHandler, true);
                    };
                    window.addEventListener('scroll', scrollHandler, true);
                });
            });

            document.addEventListener("click", e => {
                if (!e.target.closest("[data-dropdown-enrollment]") &&
                    !e.target.closest(".dropdown-clone")) {
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                }
            });
        });
    </script>

</x-app-layout>
