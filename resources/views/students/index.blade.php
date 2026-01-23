<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-emerald-600 via-emerald-600 to-teal-700 dark:from-emerald-800 dark:via-emerald-800 dark:to-teal-900 rounded-2xl shadow-xl mb-8">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <nav class="flex items-center gap-2 text-emerald-200 text-sm mb-2">
                                <a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Panel</a>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span class="text-white">Personas</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span class="text-white font-medium">Estudiantes</span>
                            </nav>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Lista de Estudiantes</h1>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                {{-- Search & Filters --}}
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <form method="GET" action="{{ route('students.index') }}" class="space-y-4">
                        <div class="flex flex-col lg:flex-row gap-4">
                            {{-- Search Input --}}
                            <div class="flex-1">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, código, correo..."
                                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 uppercase" autocomplete="off">
                                </div>
                            </div>

                            {{-- Filters --}}
                            <div class="flex flex-wrap gap-3">
                                <select name="status" class="px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">Todos los estados</option>
                                    <option value="Activo" {{ request('status') === 'Activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="Inactivo" {{ request('status') === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                </select>

                                <select id="academic_period_filter" name="academic_period_id" class="px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">Todos los períodos</option>
                                    @foreach ($academicPeriods as $period)
                                        <option value="{{ $period->id }}" {{ request('academic_period_id') == $period->id ? 'selected' : '' }}>
                                            {{ $period->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <select id="section_filter" name="section_id" class="px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed" {{ request('academic_period_id') ? '' : 'disabled' }}>
                                    <option value="">Todas las secciones</option>
                                </select>

                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Estudiante</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Contacto</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Representante</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Situación</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @php
                                $situationColors = [
                                    'Cursando' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                                    'Pausado' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                                    'Baja médica' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                                    'Suspendido' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                                    'Situación familiar' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
                                    'Sin actividad' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
                                ];
                                $situations = \App\Enums\StudentSituation::toArray();
                            @endphp

                            @forelse ($students as $student)
                                @php
                                    $situation = $student->situation?->value ?? 'Sin actividad';
                                    $colorClass = $situationColors[$situation] ?? $situationColors['Sin actividad'];
                                    $hasActiveEnrollments = $student->enrollments->where('status', 'activo')->count() > 0;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-lg flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                                {{ strtoupper(substr($student->user->name, 0, 1)) }}{{ strtoupper(substr($student->user->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $student->user->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->user->last_name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-sm text-gray-600 dark:text-gray-300">{{ $student->student_code }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ $student->user->email ?? 'Sin correo' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($student->representative)
                                            <a href="{{ route('representatives.show', $student->representative) }}" class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline">
                                                {{ $student->representative->user->name }} {{ $student->representative->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-sm text-gray-400 italic">Sin representante</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($student->is_active)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
                                            {{ $situation }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="relative inline-block">
                                            <button data-dropdown-student="{{ $student->id }}"
                                                    class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                                Acciones
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>

                                            {{-- Dropdown Template --}}
                                            <div id="dropdown-template-{{ $student->id }}" class="hidden">
                                                <ul class="py-1 text-sm">
                                                    <li>
                                                        <a href="{{ route('students.show', $student) }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                            </svg>
                                                            Ver
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('students.enrollments.create', $student) }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                            </svg>
                                                            Inscribir
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('students.edit', $student) }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                            Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button type="button" onclick="openSituationModal('{{ $student->id }}')" class="flex items-center gap-2 w-full text-left px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                            </svg>
                                                            Cambiar Situación
                                                        </button>
                                                    </li>
                                                    @if ($student->is_active && $hasActiveEnrollments)
                                                        <li class="border-t border-gray-200 dark:border-gray-600">
                                                            <a href="{{ route('students.withdraw.form', $student) }}" class="flex items-center gap-2 px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                                </svg>
                                                                Retirar
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Modal de Situación --}}
                                <div id="situationModal-{{ $student->id }}" class="modal hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-96 max-w-[90vw] border border-gray-200 dark:border-gray-700">
                                        <h3 class="text-lg font-bold mb-2 text-gray-900 dark:text-white">Cambiar Situación</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                            Estudiante: <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $student->full_name }}</span>
                                        </p>
                                        <form action="{{ route('students.situation', $student) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="mb-4">
                                                <label for="situation-{{ $student->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Situación</label>
                                                <select name="situation" id="situation-{{ $student->id }}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                                                    @foreach ($situations as $situationOption)
                                                        <option value="{{ $situationOption }}" {{ $student->situation?->value === $situationOption ? 'selected' : '' }}>
                                                            {{ $situationOption }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="flex justify-end gap-3">
                                                <button type="button" onclick="closeSituationModal('{{ $student->id }}')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                                                    Cancelar
                                                </button>
                                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-colors">
                                                    Guardar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400">
                                            {{ request('search') ? 'No se encontraron estudiantes con los criterios de búsqueda.' : 'Ingrese un término de búsqueda para ver resultados.' }}
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($students instanceof \Illuminate\Pagination\LengthAwarePaginator && $students->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $students->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Floating Dropdown Container --}}
    <div id="floating-dropdown" class="hidden fixed bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl z-50 min-w-[180px]"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const floatingDropdown = document.getElementById('floating-dropdown');
            let currentOpenButton = null;

            // Dropdown handlers
            document.querySelectorAll('[data-dropdown-student]').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const studentId = this.dataset.dropdownStudent;
                    const template = document.getElementById(`dropdown-template-${studentId}`);

                    if (currentOpenButton === this && !floatingDropdown.classList.contains('hidden')) {
                        floatingDropdown.classList.add('hidden');
                        currentOpenButton = null;
                        return;
                    }

                    floatingDropdown.innerHTML = template.innerHTML;
                    const rect = this.getBoundingClientRect();
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

                    let top = rect.bottom + scrollTop + 5;
                    let left = rect.left + scrollLeft;

                    const dropdownWidth = 180;
                    if (left + dropdownWidth > window.innerWidth) {
                        left = rect.right + scrollLeft - dropdownWidth;
                    }

                    const menuHeight = 220;
                    if (rect.bottom + menuHeight > window.innerHeight) {
                        top = rect.top + scrollTop - menuHeight - 5;
                    }

                    floatingDropdown.style.top = `${top}px`;
                    floatingDropdown.style.left = `${left}px`;
                    floatingDropdown.classList.remove('hidden');
                    currentOpenButton = this;
                });
            });

            document.addEventListener('click', function(e) {
                if (!floatingDropdown.contains(e.target) && !e.target.hasAttribute('data-dropdown-student')) {
                    floatingDropdown.classList.add('hidden');
                    currentOpenButton = null;
                }
            });

            window.addEventListener('scroll', function() {
                floatingDropdown.classList.add('hidden');
                currentOpenButton = null;
            });

            window.addEventListener('resize', function() {
                floatingDropdown.classList.add('hidden');
                currentOpenButton = null;
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    floatingDropdown.classList.add('hidden');
                    currentOpenButton = null;
                }
            });

            // Dynamic section filter
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

            if (periodSelect.value) {
                updateSections();
            }
        });

        function openSituationModal(studentId) {
            document.getElementById('situationModal-' + studentId).classList.remove('hidden');
            document.getElementById('floating-dropdown').classList.add('hidden');
        }

        function closeSituationModal(studentId) {
            document.getElementById('situationModal-' + studentId).classList.add('hidden');
        }

        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>