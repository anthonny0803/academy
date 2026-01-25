<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-violet-600 via-violet-600 to-purple-700 dark:from-violet-800 dark:via-violet-800 dark:to-purple-900 rounded-2xl shadow-xl mb-8">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <a href="{{ route('dashboard') }}" class="text-violet-200 hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                </a>
                                <span class="text-violet-200">/</span>
                                <span class="text-violet-100">Académico</span>
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Gestión de Inscripciones</h1>
                            <p class="mt-2 text-violet-100">Administra las inscripciones de estudiantes</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 dark:border-gray-700/50 overflow-hidden">

                {{-- Search & Filters --}}
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <form method="GET" action="{{ route('enrollments.index') }}" class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="search" placeholder="Buscar estudiante..." value="{{ request('search') }}"
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 placeholder:text-gray-400/50 uppercase"
                                   autocomplete="off" style="text-transform:uppercase;">
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
                            <select name="status"
                                    class="px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                <option value="">Todos los estados</option>
                                @foreach ($statuses as $statusValue)
                                    <option value="{{ $statusValue }}" {{ request('status') === $statusValue ? 'selected' : '' }}>
                                        {{ ucfirst($statusValue) }}
                                    </option>
                                @endforeach
                            </select>

                            <select id="academic_period_filter" name="academic_period_id"
                                    class="px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                <option value="">Todos los períodos</option>
                                @foreach ($academicPeriods as $period)
                                    <option value="{{ $period->id }}" {{ request('academic_period_id') == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select id="section_filter" name="section_id"
                                    class="px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
                                    {{ request('academic_period_id') ? '' : 'disabled' }}>
                                <option value="">Todas las secciones</option>
                            </select>

                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Estudiante</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Sección</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Período</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($enrollments as $enrollment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                                {{ strtoupper(substr($enrollment->student->user->name, 0, 1) . substr($enrollment->student->user->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">
                                                    {{ $enrollment->student->user->name }} {{ $enrollment->student->user->last_name }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $enrollment->student->student_code }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-300">
                                            {{ $enrollment->section->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $enrollment->section->academicPeriod->name }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($enrollment->status === 'activo')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                                Activo
                                            </span>
                                        @elseif ($enrollment->status === 'completado')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1.5"></span>
                                                Completado
                                            </span>
                                        @elseif ($enrollment->status === 'retirado')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                                Retirado
                                            </span>
                                        @elseif ($enrollment->status === 'transferido')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1.5"></span>
                                                Transferido
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                                                <span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-1.5"></span>
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="relative inline-block">
                                            <button data-dropdown-enrollment="{{ $enrollment->id }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/20 rounded-lg transition-colors">
                                                <span>Acciones</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>

                                            {{-- Dropdown template --}}
                                            <div id="dropdown-template-{{ $enrollment->id }}" class="hidden">
                                                <div class="py-2">
                                                    <a href="{{ route('enrollments.show', $enrollment) }}"
                                                       class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-violet-900/20 hover:text-violet-700 dark:hover:text-violet-300 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Ver Detalles
                                                    </a>
                                                    @if ($enrollment->status === 'activo')
                                                        <a href="{{ route('enrollments.transfer.form', $enrollment) }}"
                                                           class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                            </svg>
                                                            Transferir Sección
                                                        </a>
                                                        <a href="{{ route('enrollments.promote.form', $enrollment) }}"
                                                           class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-700 dark:hover:text-green-300 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                                            </svg>
                                                            Promover Grado
                                                        </a>
                                                        <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                                                        <button type="button"
                                                                data-delete-enrollment="{{ $enrollment->id }}"
                                                                data-delete-name="{{ e($enrollment->student->user->name . ' ' . $enrollment->student->user->last_name) }}"
                                                                data-delete-section="{{ e($enrollment->section->name) }}"
                                                                class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Eliminar Inscripción
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400">No se encontraron inscripciones</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($enrollments->count() > 0)
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $enrollments->appends(request()->except('page'))->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>

    {{-- Modal ELIMINAR --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md my-8 border-t-4 border-red-500 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Eliminar Inscripción</h2>
                </div>
            </div>

            <div class="p-6">
                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    ¿Estás seguro de que deseas eliminar la inscripción de:
                </p>
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-xl mb-4">
                    <p class="font-semibold text-gray-900 dark:text-white" id="delete-student-name"></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Sección: <span id="delete-section-name"></span></p>
                </div>
                <p class="text-sm text-red-600 dark:text-red-400">
                    <strong>Advertencia:</strong> Esta acción no se puede deshacer y se perderán todas las calificaciones asociadas.
                </p>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancelar
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-xl shadow-lg shadow-red-500/25 transition-all duration-300">
                        Sí, Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const closeAllDropdowns = () => {
                document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
            };

            // Cascade filter: Period → Section
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

            // Trigger initial if there's a value
            if (periodSelect.value) {
                periodSelect.dispatchEvent(new Event('change'));
            }

            // Dropdown handling
            document.querySelectorAll("[data-dropdown-enrollment]").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    const id = btn.dataset.dropdownEnrollment;
                    let existing = document.getElementById("dropdownMenu-" + id);

                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }

                    closeAllDropdowns();

                    const tpl = document.getElementById("dropdown-template-" + id);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + id;
                    clone.classList.remove("hidden");
                    clone.classList.add(
                        "dropdown-clone", "fixed", "z-50", "w-52",
                        "bg-white", "dark:bg-gray-800", "border", "border-gray-200",
                        "dark:border-gray-700", "rounded-xl", "shadow-xl"
                    );

                    const rect = btn.getBoundingClientRect();
                    const menuHeight = 200;
                    const espacioAbajo = window.innerHeight - rect.bottom;
                    const espacioArriba = rect.top;

                    if (espacioAbajo >= menuHeight) {
                        clone.style.top = (rect.bottom + 4) + "px";
                    } else if (espacioArriba >= menuHeight) {
                        clone.style.top = (rect.top - menuHeight - 4) + "px";
                    } else {
                        clone.style.top = (rect.bottom + 4) + "px";
                        clone.style.maxHeight = espacioAbajo + "px";
                        clone.style.overflowY = "auto";
                    }

                    clone.style.left = Math.min(rect.left, window.innerWidth - 220) + "px";
                    document.body.appendChild(clone);
                });
            });

            // Close dropdown on outside click
            document.addEventListener("click", (e) => {
                if (!e.target.closest("[data-dropdown-enrollment]") && !e.target.closest(".dropdown-clone")) {
                    closeAllDropdowns();
                }
            });

            // Close dropdown on scroll/resize/escape
            window.addEventListener("scroll", closeAllDropdowns, true);
            window.addEventListener("resize", closeAllDropdowns);
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") {
                    closeAllDropdowns();
                    closeDeleteModal();
                }
            });

            // Delete button handler
            document.addEventListener("click", (e) => {
                const btn = e.target.closest("[data-delete-enrollment]");
                if (btn) {
                    const id = btn.getAttribute('data-delete-enrollment');
                    const name = btn.getAttribute('data-delete-name');
                    const section = btn.getAttribute('data-delete-section');

                    closeAllDropdowns();

                    document.getElementById('delete-student-name').textContent = name;
                    document.getElementById('delete-section-name').textContent = section;
                    document.getElementById('deleteForm').action = `/enrollments/${id}`;

                    openDeleteModal();
                }
            });

            // Close modal on backdrop click
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('click', (e) => {
                    if (e.target === deleteModal) {
                        closeDeleteModal();
                    }
                });
            }
        });

        // Modal functions
        function openDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    </script>
</x-app-layout>