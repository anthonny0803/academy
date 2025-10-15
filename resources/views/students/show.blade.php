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
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($student->enrollments as $enrollment)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="py-2 px-4 border-b">{{ $enrollment->section->name }}</td>
                                            <td class="py-2 px-4 border-b">
                                                {{ $enrollment->section->academicPeriod->name }}</td>
                                            <td class="py-2 px-4 border-b">
                                                <span
                                                    class="inline-block bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-100 text-xs px-2 py-1 rounded">
                                                    {{ ucfirst($enrollment->status) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                {{ $enrollment->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-2 px-4 text-center text-gray-300">Sin
                                                inscripciones registradas</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Toggle active state --}}
                    <div class="mt-6 mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Estado del Estudiante
                        </label>
                        <div class="mt-1 flex items-center space-x-2">
                            <form method="POST" action="{{ route('students.toggle', $student) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full cursor-pointer 
                       {{ $student->is_active ? 'bg-blue-600' : 'bg-gray-400' }}">
                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition
                           {{ $student->is_active ? 'translate-x-6' : 'translate-x-1' }}">
                                    </span>
                                </button>
                            </form>
                            <span
                                class="inline-block {{ $student->is_active ? 'bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100 text-xs px-2 py-1 rounded mr-1' : 'bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100 text-xs px-2 py-1 rounded mr-1' }}">
                                {{ $student->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
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
                                <li><a href="{{ route('students.enrollments.create', $student) }}"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Asignar Nueva
                                        Inscripción</a>
                                </li>
                                <li><a href="{{ route('students.edit', $student) }}"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Editar</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Dropdown script --}}
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
                    clone.classList.add("dropdown-clone", "absolute", "z-50", "w-40", "bg-white",
                        "dark:bg-gray-800", "border", "border-gray-200", "dark:border-gray-700",
                        "rounded", "shadow-lg");

                    // Posicionar relativamente al botón
                    btn.style.position = 'relative';
                    const menuHeight = 100;

                    // Siempre hacia ARRIBA
                    clone.style.bottom = "100%";
                    clone.style.left = "0";
                    clone.style.marginBottom = "5px";

                    btn.appendChild(clone);
                });
            });

            document.addEventListener("click", e => {
                if (!e.target.closest("[data-dropdown-student]") && !e.target.closest(".dropdown-clone")) {
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                }
            });
        });
    </script>

</x-app-layout>
