<x-app-layout>
    @php
        $situations = \App\Enums\StudentSituation::toArray();
        $hasActiveEnrollments = $student->enrollments->where('status', 'activo')->count() > 0;
        
        $situationColors = [
            'Cursando' => 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
            'Pausado' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
            'Baja médica' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
            'Suspendido' => 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
            'Situación familiar' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
            'Sin actividad' => 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100',
        ];
        $situation = $student->situation?->value ?? 'Sin actividad';
        $situationColor = $situationColors[$situation] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100';
        
        $statusColors = [
            'activo' => 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
            'completado' => 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100',
            'retirado' => 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
            'transferido' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
            'promovido' => 'bg-purple-100 text-purple-800 dark:bg-purple-700 dark:text-purple-100',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- Header con badges --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $student->full_name }}
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $student->student_code }}
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $student->is_active 
                                    ? 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100' 
                                    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100' }}">
                                <span class="w-2 h-2 rounded-full mr-2 {{ $student->is_active ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                                {{ $student->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $situationColor }}">
                                {{ $situation }}
                            </span>
                        </div>
                    </div>

                    {{-- Información Personal + Representante en grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nombre completo</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $student->full_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Correo electrónico</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $student->email ?? 'Sin correo' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Documento de identidad</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $student->document_id ?? 'Sin documento' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Sexo</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $student->sex ?? 'No especificado' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de nacimiento</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $student->birth_date?->format('d/m/Y') ?? 'No registrada' }}
                                @if($student->age)
                                    <span class="text-gray-500 dark:text-gray-400">({{ $student->age }} años)</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de relación</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100">
                                    {{ $student->relationship_type }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de registro</label>
                            <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $student->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Representante</label>
                            @if($student->representative)
                                <a href="{{ route('representatives.show', $student->representative) }}"
                                   class="mt-1 inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline group">
                                    <span class="w-2 h-2 rounded-full mr-2 {{ $student->representative->is_active ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                                    {{ $student->representative->full_name }}
                                    <svg class="w-4 h-4 ml-1 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @else
                                <p class="mt-1 text-gray-500 dark:text-gray-400">Sin representante asignado</p>
                            @endif
                        </div>
                    </div>

                    {{-- Historial de Inscripciones --}}
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Inscripciones
                                <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                    ({{ $student->enrollments->count() }})
                                </span>
                            </h2>
                            <a href="{{ route('students.enrollments.create', $student) }}"
                               class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg
                                      bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Nueva inscripción
                            </a>
                        </div>

                        @if($student->enrollments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-900">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sección</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Período</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($student->enrollments as $enrollment)
                                            @php
                                                $statusColor = $statusColors[$enrollment->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100';
                                            @endphp
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $enrollment->section->name }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $enrollment->section->academicPeriod->name }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                                        {{ ucfirst($enrollment->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $enrollment->created_at->format('d/m/Y') }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <button data-dropdown-enrollment="{{ $enrollment->id }}"
                                                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded
                                                               bg-gray-100 text-gray-700 hover:bg-gray-200 
                                                               dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                                                        Gestionar
                                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                    </button>
                                                    <div id="dropdown-enrollment-{{ $enrollment->id }}" class="hidden">
                                                        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                                            <li>
                                                                <a href="{{ route('enrollments.show', $enrollment) }}"
                                                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                    Ver Detalles
                                                                </a>
                                                            </li>
                                                            @if ($enrollment->status === 'activo')
                                                                <li>
                                                                    <a href="{{ route('enrollments.transfer.form', $enrollment) }}"
                                                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                        Transferir
                                                                    </a>
                                                                </li>
                                                                @if ($enrollment->section->academicPeriod->isPromotable())
                                                                    <li>
                                                                        <a href="{{ route('enrollments.promote.form', $enrollment) }}"
                                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                            Promover
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm">Sin inscripciones registradas</p>
                            </div>
                        @endif
                    </div>

                    {{-- Navegación y Acciones --}}
                    <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('students.index') }}"
                           class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver
                        </a>
                        <span class="text-gray-300 dark:text-gray-600">|</span>
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                            Panel
                        </a>
                        <div class="flex-1"></div>
                        <a href="{{ route('students.edit', $student) }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg
                                  border border-gray-300 dark:border-gray-600 
                                  text-gray-700 dark:text-gray-300 
                                  hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Editar
                        </a>
                        <button data-dropdown-student="{{ $student->id }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg
                                   bg-gray-600 text-white hover:bg-gray-700 transition-colors">
                            Acciones
                            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div id="dropdown-template-{{ $student->id }}" class="hidden">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                <li>
                                    <a href="{{ route('students.enrollments.create', $student) }}"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Nueva Inscripción
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('students.edit', $student) }}"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Editar
                                    </a>
                                </li>
                                <li>
                                    <button type="button" onclick="openSituationModal('{{ $student->id }}')"
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
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal para cambiar situación --}}
    <div id="situationModal-{{ $student->id }}"
        class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl w-96 max-w-[90vw]">
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Cambiar Situación</h3>
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
                    <select name="situation" id="situation-{{ $student->id }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500" required>
                        @foreach ($situations as $situationOption)
                            <option value="{{ $situationOption }}" {{ $student->situation?->value === $situationOption ? 'selected' : '' }}>
                                {{ $situationOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeSituationModal('{{ $student->id }}')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openSituationModal(studentId) {
            document.getElementById('situationModal-' + studentId).classList.remove('hidden');
            closeAllDropdowns();
        }
        function closeSituationModal(studentId) {
            document.getElementById('situationModal-' + studentId).classList.add('hidden');
        }
        function closeAllDropdowns() {
            document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
        }
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.classList.add('hidden');
            }
        });
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll("[data-dropdown-student]").forEach(function(btn) {
                btn.addEventListener("click", function(e) {
                    e.stopPropagation();
                    const studentId = btn.dataset.dropdownStudent;
                    const existingId = "dropdownMenu-" + studentId;
                    let existing = document.getElementById(existingId);
                    if (existing) { existing.remove(); return; }
                    closeAllDropdowns();
                    const tpl = document.getElementById("dropdown-template-" + studentId);
                    const clone = tpl.cloneNode(true);
                    clone.id = existingId;
                    clone.classList.remove("hidden");
                    clone.classList.add("dropdown-clone", "bg-white", "dark:bg-gray-800", "border", "border-gray-200", "dark:border-gray-700", "rounded-md", "shadow-lg");
                    clone.style.position = "fixed";
                    clone.style.zIndex = "9999";
                    clone.style.width = "12rem";
                    document.body.appendChild(clone);
                    const rect = btn.getBoundingClientRect();
                    const dropdownHeight = clone.offsetHeight;
                    const spaceBelow = window.innerHeight - rect.bottom;
                    if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
                        clone.style.top = (rect.top - dropdownHeight - 5) + "px";
                    } else {
                        clone.style.top = (rect.bottom + 5) + "px";
                    }
                    clone.style.left = Math.min(rect.left, window.innerWidth - 200) + "px";
                });
            });
            document.querySelectorAll("[data-dropdown-enrollment]").forEach(function(btn) {
                btn.addEventListener("click", function(e) {
                    e.stopPropagation();
                    const enrollmentId = btn.dataset.dropdownEnrollment;
                    const existingId = "dropdownEnrollmentMenu-" + enrollmentId;
                    let existing = document.getElementById(existingId);
                    if (existing) { existing.remove(); return; }
                    closeAllDropdowns();
                    const tpl = document.getElementById("dropdown-enrollment-" + enrollmentId);
                    const clone = tpl.cloneNode(true);
                    clone.id = existingId;
                    clone.classList.remove("hidden");
                    clone.classList.add("dropdown-clone", "bg-white", "dark:bg-gray-800", "border", "border-gray-200", "dark:border-gray-700", "rounded-md", "shadow-lg");
                    clone.style.position = "fixed";
                    clone.style.zIndex = "9999";
                    clone.style.width = "10rem";
                    document.body.appendChild(clone);
                    const rect = btn.getBoundingClientRect();
                    const dropdownHeight = clone.offsetHeight;
                    const spaceBelow = window.innerHeight - rect.bottom;
                    if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
                        clone.style.top = (rect.top - dropdownHeight - 5) + "px";
                    } else {
                        clone.style.top = (rect.bottom + 5) + "px";
                    }
                    clone.style.left = Math.min(rect.left, window.innerWidth - 170) + "px";
                });
            });
            document.addEventListener("click", function(e) {
                if (!e.target.closest("[data-dropdown-student]") && !e.target.closest("[data-dropdown-enrollment]") && !e.target.closest(".dropdown-clone")) {
                    closeAllDropdowns();
                }
            });
            window.addEventListener("scroll", closeAllDropdowns, true);
            window.addEventListener("resize", closeAllDropdowns);
        });
    </script>
</x-app-layout>