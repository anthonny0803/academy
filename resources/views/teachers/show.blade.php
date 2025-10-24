<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                        Detalles del Profesor
                    </h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Columna izquierda: Datos personales --}}
                        <div>
                            <h2 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Información Personal</h2>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nombre</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $teacher->user->name }}</p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Apellido</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $teacher->user->last_name }}</p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Correo</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $teacher->user->email }}</p>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Sexo</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $teacher->user->sex }}</p>
                            </div>

                            {{-- Estado --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Estado del Profesor
                                </label>
                                <div class="mt-1 flex items-center space-x-2">
                                    <form method="POST" action="{{ route('teachers.toggle', $teacher) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full cursor-pointer 
                                            {{ $teacher->is_active ? 'bg-blue-600' : 'bg-gray-400' }}">
                                            <span
                                                class="inline-block h-4 w-4 transform rounded-full bg-white transition
                                                {{ $teacher->is_active ? 'translate-x-6' : 'translate-x-1' }}">
                                            </span>
                                        </button>
                                    </form>
                                    <span
                                        class="inline-block {{ $teacher->is_active ? 'bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100 text-xs px-2 py-1 rounded mr-1' : 'bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100 text-xs px-2 py-1 rounded mr-1' }}">
                                        {{ $teacher->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Columna derecha: Materias --}}
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Materias que puede impartir</h2>
                                <a href="{{ route('teachers.subjects.assign', $teacher) }}"
                                    class="text-sm bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700">
                                    + Asignar
                                </a>
                            </div>

                            @if($teacher->subjects->count() > 0)
                                <div class="space-y-2">
                                    @foreach($teacher->subjects as $subject)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <span class="text-gray-900 dark:text-gray-100">{{ $subject->name }}</span>
                                            <form method="POST" action="{{ route('teachers.subjects.destroy', [$teacher, $subject]) }}" 
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar esta materia?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                                    ✕ Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600">
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                        Este profesor aún no tiene materias asignadas.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="mt-6 flex gap-4 items-center">
                        <a href="{{ route('teachers.index') }}"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Ir a la Lista
                        </a>
                        <a href="{{ route('dashboard') }}"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Volver al Panel
                        </a>

                        {{-- Dropdown acciones --}}
                        <button data-dropdown-teacher="{{ $teacher->id }}"
                            class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                            Acciones
                        </button>

                        <div id="dropdown-template-{{ $teacher->id }}" class="hidden">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                <li>
                                    <a href="{{ route('teachers.subjects.assign', $teacher) }}" 
                                       class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Asignar Materias
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('users.edit', $teacher->user) }}"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Editar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Script para dropdown --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll("[data-dropdown-teacher]").forEach(btn => {
                btn.addEventListener("click", () => {
                    const teacherId = btn.dataset.dropdownTeacher;
                    let existing = document.getElementById("dropdownMenu-" + teacherId);

                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }

                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());

                    const tpl = document.getElementById("dropdown-template-" + teacherId);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + teacherId;
                    clone.classList.remove("hidden");
                    clone.classList.add(
                        "dropdown-clone", "fixed", "z-50", "w-48",
                        "bg-white", "dark:bg-gray-800", "border", "border-gray-200",
                        "dark:border-gray-700", "rounded", "shadow-lg"
                    );

                    const rect = btn.getBoundingClientRect();
                    const menuHeight = 120;
                    const espacioAbajo = window.innerHeight - rect.bottom;
                    const espacioArriba = rect.top;

                    if (espacioAbajo >= menuHeight) {
                        clone.style.top = rect.bottom + "px";
                    } else if (espacioArriba >= menuHeight) {
                        clone.style.top = (rect.top - menuHeight) + "px";
                    } else {
                        clone.style.top = rect.bottom + "px";
                        clone.style.maxHeight = espacioAbajo + "px";
                        clone.style.overflowY = "auto";
                    }

                    clone.style.left = rect.left + "px";

                    document.body.appendChild(clone);
                });
            });

            document.addEventListener("click", e => {
                if (!e.target.closest("[data-dropdown-teacher]") &&
                    !e.target.closest(".dropdown-clone")) {
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                }
            });
        });
    </script>
</x-app-layout>