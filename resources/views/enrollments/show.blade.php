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

                    {{-- Calificaciones (si existen) --}}
                    @if ($enrollment->grades->count() > 0)
                        <div class="mt-6">
                            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Calificaciones</h2>
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="py-2 px-4 border-b text-left">Materia</th>
                                            <th class="py-2 px-4 border-b text-left">Calificación</th>
                                            <th class="py-2 px-4 border-b text-left">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($enrollment->grades as $grade)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                                <td class="py-2 px-4 border-b">{{ $grade->subject->name }}</td>
                                                <td class="py-2 px-4 border-b">{{ $grade->grade }}</td>
                                                <td class="py-2 px-4 border-b">
                                                    {{ $grade->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-gray-700 dark:text-gray-300">
                                No hay calificaciones registradas para esta inscripción.
                            </p>
                        </div>
                    @endif

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
                                    <li><a href="{{ route('enrollments.edit', $enrollment) }}"
                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Cambiar
                                            Estado</a>
                                    </li>
                                    <li><a href="{{ route('enrollments.transfer.form', $enrollment) }}"
                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Transferir
                                            Sección</a>
                                    </li>
                                    <li><a href="{{ route('enrollments.promote.form', $enrollment) }}"
                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Promover
                                            Grado</a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Dropdown script --}}
    @if ($enrollment->status === 'activo')
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
        </script>
    @endif
</x-app-layout>
