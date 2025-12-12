<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Detalles de la Inscripción</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Información del Estudiante --}}
                        <div>
                            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Estudiante</h2>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nombre
                                    Completo</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $enrollment->student->user->name }} {{ $enrollment->student->user->last_name }}
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Código de
                                    Estudiante</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $enrollment->student->student_code }}</p>
                            </div>

                            <div class="mb-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">Documento</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $enrollment->student->user->document_id ?? 'Sin documento' }}
                                </p>
                            </div>

                            <div class="mb-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">Representante</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $enrollment->student->representative?->user?->name ?? 'Sin representante' }}
                                    {{ $enrollment->student->representative?->user?->last_name ?? '' }}
                                </p>
                            </div>
                        </div>

                        {{-- Información de la Inscripción --}}
                        <div>
                            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Datos de Inscripción
                            </h2>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Período
                                    Académico</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $enrollment->section->academicPeriod->name }}
                                </p>
                            </div>

                            <div class="mb-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">Sección</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $enrollment->section->name }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Estado</label>
                                <p class="mt-1">
                                    <span
                                        class="inline-block 
                                        @if ($enrollment->status === 'activo') bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100
                                        @elseif($enrollment->status === 'completado') bg-blue-100 dark:bg-blue-600 text-blue-800 dark:text-blue-100
                                        @elseif($enrollment->status === 'retirado') bg-red-100 dark:bg-red-600 text-red-800 dark:text-red-100
                                        @elseif($enrollment->status === 'transferido') bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100
                                        @else bg-purple-100 dark:bg-purple-600 text-purple-800 dark:text-purple-100 @endif
                                        text-xs px-3 py-1 rounded">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Fecha de
                                    Inscripción</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $enrollment->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Última
                                    Actualización</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $enrollment->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Calificaciones por Asignatura --}}
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Calificaciones por Asignatura</h2>
                        
                        @if ($subjectsData->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="py-2 px-4 border-b text-left">Asignatura</th>
                                            <th class="py-2 px-4 border-b text-left">Profesor</th>
                                            <th class="py-2 px-4 border-b text-center">Promedio</th>
                                            <th class="py-2 px-4 border-b text-center">Detalles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subjectsData as $index => $subject)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                                <td class="py-2 px-4 border-b font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $subject['subject_name'] }}
                                                </td>
                                                <td class="py-2 px-4 border-b text-gray-600 dark:text-gray-400">
                                                    {{ $subject['teacher_name'] }}
                                                </td>
                                                <td class="py-2 px-4 border-b text-center">
                                                    @if ($subject['average'] !== null)
                                                        <span class="font-bold {{ $subject['average'] >= $passingGrade ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                            {{ number_format($subject['average'], 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-500">Sin notas</span>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-4 border-b text-center">
                                                    <button type="button"
                                                        onclick="openGradeModal({{ $index }})"
                                                        class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                                        Ver detalles
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Leyenda --}}
                            <div class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                                <span class="text-green-600 dark:text-green-400">■</span> Aprobado (≥ {{ $passingGrade }})
                                <span class="mx-2">|</span>
                                <span class="text-red-600 dark:text-red-400">■</span> Reprobado (&lt; {{ $passingGrade }})
                            </div>
                        @else
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-gray-700 dark:text-gray-300">
                                    No hay asignaturas asignadas a esta sección.
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Acciones y enlaces --}}
                    <div class="mt-6 flex gap-4">
                        <a href="{{ route('enrollments.index') }}"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Ir a la Lista
                        </a>

                        <a href="{{ route('students.show', $enrollment->student) }}"
                            class="underline text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Ver Estudiante
                        </a>

                        <a href="{{ route('dashboard') }}"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Volver al Panel
                        </a>

                        {{-- Dropdown de acciones --}}
                        @if ($enrollment->status === 'activo')
                            <button data-dropdown-enrollment="{{ $enrollment->id }}"
                                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                Acciones
                            </button>

                            <div id="dropdown-template-{{ $enrollment->id }}" class="hidden">
                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                    <li><a href="{{ route('enrollments.transfer.form', $enrollment) }}"
                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Transferir
                                            Sección</a>
                                    </li>
                                    <li><a href="{{ route('enrollments.promote.form', $enrollment) }}"
                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Promover
                                            Grado</a>
                                    </li>
                                    <li class="border-t border-gray-200 dark:border-gray-600">
                                        <form action="{{ route('enrollments.destroy', $enrollment) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro de eliminar esta inscripción?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="block w-full text-left px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                                                Eliminar Inscripción
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modales de calificaciones por asignatura --}}
    @foreach ($subjectsData as $index => $subject)
        <div id="gradeModal-{{ $index }}"
            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-4 max-h-[80vh] overflow-hidden">
                {{-- Header --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        {{ $subject['subject_name'] }}
                    </h3>
                    <button onclick="closeGradeModal({{ $index }})"
                        class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-2xl leading-none">
                        &times;
                    </button>
                </div>
                
                {{-- Body --}}
                <div class="p-4 overflow-y-auto max-h-[60vh]">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <strong>Profesor:</strong> {{ $subject['teacher_name'] }}
                    </p>

                    @if ($subject['grades_detail']->count() > 0)
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-2 text-left">Evaluación</th>
                                    <th class="py-2 text-center">Ponderación</th>
                                    <th class="py-2 text-center">Calificación</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-900 dark:text-gray-100">
                                @foreach ($subject['grades_detail'] as $grade)
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-2">{{ $grade['column_name'] }}</td>
                                        <td class="py-2 text-center text-gray-500 dark:text-gray-400">
                                            {{ number_format($grade['weight'], 0) }}%
                                        </td>
                                        <td class="py-2 text-center font-medium">
                                            @if ($grade['value'] !== null)
                                                <span class="{{ $grade['value'] >= $passingGrade ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ number_format($grade['value'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                    <td class="py-3 text-gray-600 dark:text-gray-300 font-bold text-lg">Promedio Ponderado</td>
                                    <td class="py-3 text-center text-gray-500 dark:text-gray-400">100%</td>
                                    <td class="py-3 text-center font-bold text-lg">
                                        @if ($subject['average'] !== null)
                                            <span class="{{ $subject['average'] >= $passingGrade ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ number_format($subject['average'], 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <p class="text-gray-500 text-center py-4">
                            No hay evaluaciones configuradas para esta asignatura.
                        </p>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 text-right">
                    <button onclick="closeGradeModal({{ $index }})"
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Scripts --}}
    <script>
        // Modal de calificaciones
        function openGradeModal(index) {
            document.getElementById('gradeModal-' + index).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeGradeModal(index) {
            document.getElementById('gradeModal-' + index).classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Cerrar modal con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id^="gradeModal-"]').forEach(modal => {
                    modal.classList.add('hidden');
                });
                document.body.style.overflow = '';
            }
        });

        // Cerrar modal al hacer clic fuera
        document.querySelectorAll('[id^="gradeModal-"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        });

        // Dropdown de acciones (original)
        @if ($enrollment->status === 'activo')
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
                        clone.classList.add("dropdown-clone", "fixed", "z-50", "w-48", "bg-white",
                            "dark:bg-gray-800", "border", "border-gray-200", "dark:border-gray-700",
                            "rounded", "shadow-lg");
                        const rect = btn.getBoundingClientRect();
                        const menuHeight = 120;
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
                    if (!e.target.closest("[data-dropdown-enrollment]") && !e.target.closest(
                            ".dropdown-clone")) {
                        document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                    }
                });
            });
        @endif
    </script>
</x-app-layout>