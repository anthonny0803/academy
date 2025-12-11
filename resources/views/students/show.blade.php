<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Detalles del Estudiante</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Información Personal --}}
                        <div>
                            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Información Personal
                            </h2>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Código de
                                    Estudiante</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $student->student_code }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nombre
                                    Completo</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $student->user->name }} {{ $student->user->last_name }}
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Correo</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $student->user->email ?? 'Sin correo' }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Sexo</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ ucfirst($student->user->sex) }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Documento de
                                    Identidad</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $student->document_id ?? 'Sin documento' }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Fecha de
                                    Nacimiento</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $student->birth_date->format('d/m/Y') }}
                                    ({{ $student->birth_date->age }} años)
                                </p>
                            </div>
                        </div>

                        {{-- Información del Representante --}}
                        <div>
                            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Representante</h2>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nombre del
                                    Representante</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $student->representative?->user?->name ?? 'Sin representante' }}
                                    {{ $student->representative?->user?->last_name ?? '' }}
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Tipo de
                                    Relación</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ ucfirst($student->relationship_type) }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Correo del
                                    Representante</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $student->representative?->user?->email ?? 'Sin correo' }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Teléfono del
                                    Representante</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $student->representative?->phone ?? 'Sin teléfono' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Inscripciones --}}
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Historial de
                            Inscripciones</h2>
                        <div class="overflow-x-auto">
                            <table
                                class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left">Sección</th>
                                        <th class="py-2 px-4 border-b text-left">Período</th>
                                        <th class="py-2 px-4 border-b text-left">Estado</th>
                                        <th class="py-2 px-4 border-b text-left">Fecha</th>
                                        <th class="py-2 px-4 border-b text-left">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($student->enrollments as $enrollment)
                                        @php
                                            $statusColors = [
                                                'activo' =>
                                                    'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100',
                                                'completado' =>
                                                    'bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100',
                                                'retirado' =>
                                                    'bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100',
                                                'transferido' =>
                                                    'bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100',
                                                'promovido' =>
                                                    'bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-100',
                                            ];
                                            $statusColor =
                                                $statusColors[$enrollment->status] ??
                                                'bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-100';
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="py-2 px-4 border-b">{{ $enrollment->section->name }}</td>
                                            <td class="py-2 px-4 border-b">
                                                {{ $enrollment->section->academicPeriod->name }}</td>
                                            <td class="py-2 px-4 border-b">
                                                <span
                                                    class="inline-block text-xs px-2 py-1 rounded {{ $statusColor }}">
                                                    {{ ucfirst($enrollment->status) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                {{ $enrollment->created_at->format('d/m/Y') }}</td>
                                            <td class="py-2 px-4 border-b">
                                                <button data-dropdown-enrollment="{{ $enrollment->id }}"
                                                    class="px-2 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                                    Gestionar
                                                </button>

                                                {{-- Dropdown template para cada inscripción (oculto) --}}
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
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-2 px-4 text-center text-gray-300">Sin
                                                inscripciones registradas</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Estados del Estudiante --}}
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Estado Técnico (Activo/Inactivo) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Estado Técnico
                            </label>
                            <div class="mt-1 flex items-center space-x-2">
                                <span
                                    class="inline-block {{ $student->is_active ? 'bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100' : 'bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100' }} text-xs px-2 py-1 rounded">
                                    {{ $student->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>

                        {{-- Estado Situacional --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Situación del Estudiante
                            </label>
                            <div class="mt-1">
                                @php
                                    $situationColors = [
                                        'Cursando' =>
                                            'bg-green-100 text-green-800 dark:bg-green-600 dark:text-green-100',
                                        'Pausado' =>
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100',
                                        'Baja médica' =>
                                            'bg-blue-100 text-blue-800 dark:bg-blue-600 dark:text-blue-100',
                                        'Suspendido' => 'bg-red-100 text-red-800 dark:bg-red-600 dark:text-red-100',
                                        'Situación familiar' =>
                                            'bg-purple-100 text-purple-800 dark:bg-purple-600 dark:text-purple-100',
                                    ];
                                    $situation = $student->situation?->value ?? 'Cursando';
                                    $colorClass = $situationColors[$situation] ?? 'bg-gray-100 text-gray-800';
                                    $hasActiveEnrollments =
                                        $student->enrollments->where('status', 'activo')->count() > 0;
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded text-xs font-medium {{ $colorClass }}">
                                    {{ $situation }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Acciones y enlaces --}}
                    <div class="mt-6">
                        <a href="{{ route('students.index') }}"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Ir a la Lista
                        </a>
                        <a href="{{ route('dashboard') }}"
                            class="underline text-sm px-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Volver al Panel
                        </a>

                        {{-- Dropdown --}}
                        <button data-dropdown-student="{{ $student->id }}"
                            class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                            Acciones
                        </button>

                        <div id="dropdown-template-{{ $student->id }}" class="hidden">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                <li>
                                    <a href="{{ route('students.enrollments.create', $student) }}"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Asignar Nueva Inscripción
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
    @php
        $situations = \App\Enums\StudentSituation::toArray();
    @endphp
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
                    <label for="situation-{{ $student->id }}"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Situación
                    </label>
                    <select name="situation" id="situation-{{ $student->id }}"
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
                    <button type="button" onclick="closeSituationModal('{{ $student->id }}')"
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

    {{-- Scripts --}}
    <script>
        // Funciones para el modal de situación
        function openSituationModal(studentId) {
            document.getElementById('situationModal-' + studentId).classList.remove('hidden');
            closeAllDropdowns();
        }

        function closeSituationModal(studentId) {
            document.getElementById('situationModal-' + studentId).classList.add('hidden');
        }

        // Función para cerrar todos los dropdowns
        function closeAllDropdowns() {
            document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
        }

        // Cerrar modal al hacer click fuera
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.classList.add('hidden');
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Dropdown del estudiante (botón Acciones principal)
            document.querySelectorAll("[data-dropdown-student]").forEach(function(btn) {
                btn.addEventListener("click", function(e) {
                    e.stopPropagation();

                    const studentId = btn.dataset.dropdownStudent;
                    const existingId = "dropdownMenu-" + studentId;
                    let existing = document.getElementById(existingId);

                    if (existing) {
                        existing.remove();
                        return;
                    }

                    closeAllDropdowns();

                    const tpl = document.getElementById("dropdown-template-" + studentId);
                    const clone = tpl.cloneNode(true);
                    clone.id = existingId;
                    clone.classList.remove("hidden");
                    clone.classList.add("dropdown-clone", "bg-white", "dark:bg-gray-800", "border",
                        "border-gray-200", "dark:border-gray-700", "rounded-md", "shadow-lg");
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
                    clone.style.left = rect.left + "px";
                });
            });

            // Dropdown de inscripciones (botón Gestionar)
            document.querySelectorAll("[data-dropdown-enrollment]").forEach(function(btn) {
                btn.addEventListener("click", function(e) {
                    e.stopPropagation();

                    const enrollmentId = btn.dataset.dropdownEnrollment;
                    const existingId = "dropdownEnrollmentMenu-" + enrollmentId;
                    let existing = document.getElementById(existingId);

                    if (existing) {
                        existing.remove();
                        return;
                    }

                    closeAllDropdowns();

                    const tpl = document.getElementById("dropdown-enrollment-" + enrollmentId);
                    const clone = tpl.cloneNode(true);
                    clone.id = existingId;
                    clone.classList.remove("hidden");
                    clone.classList.add("dropdown-clone", "bg-white", "dark:bg-gray-800", "border",
                        "border-gray-200", "dark:border-gray-700", "rounded-md", "shadow-lg");
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
                    clone.style.left = rect.left + "px";
                });
            });

            document.addEventListener("click", function(e) {
                if (!e.target.closest("[data-dropdown-student]") &&
                    !e.target.closest("[data-dropdown-enrollment]") &&
                    !e.target.closest(".dropdown-clone")) {
                    closeAllDropdowns();
                }
            });

            window.addEventListener("scroll", closeAllDropdowns, true);
            window.addEventListener("resize", closeAllDropdowns);
        });
    </script>
</x-app-layout>
