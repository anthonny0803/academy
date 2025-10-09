<x-app-layout>
    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Detalles del Representante</h1>

                    {{-- Nombre --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nombre</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $representative->user->name }}
                            {{ $representative->user->last_name }}</p>
                    </div>

                    {{-- Correo --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Correo</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $representative->user->email }}</p>
                    </div>

                    {{-- Sexo --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Sexo</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $representative->user->sex }}</p>
                    </div>

                    {{-- Roles --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Rol</label>
                        <div class="mt-1">
                            @if ($representative->user->roles->isNotEmpty())
                                @foreach ($representative->user->roles as $role)
                                    <span
                                        class="inline-block bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-100 text-xs px-2 py-1 rounded mr-1">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-gray-400 italic">Sin rol</span>
                            @endif
                        </div>
                    </div>

                    {{-- Toggle active state --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Estado de Representante
                        </label>
                        <div class="mt-1 flex items-center space-x-2">
                            <form method="POST" action="{{ route('representatives.toggle', $representative) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full cursor-pointer 
                       {{ $representative->is_active ? 'bg-blue-600' : 'bg-gray-400' }}">
                                    <span
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition
                           {{ $representative->is_active ? 'translate-x-6' : 'translate-x-1' }}">
                                    </span>
                                </button>
                            </form>
                            <span
                                class="inline-block {{ $representative->is_active ? 'bg-green-100 dark:bg-green-600 text-green-800 dark:text-green-100 text-xs px-2 py-1 rounded mr-1' : 'bg-yellow-100 dark:bg-yellow-600 text-yellow-800 dark:text-yellow-100 text-xs px-2 py-1 rounded mr-1' }}">
                                {{ $representative->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    {{-- Acciones y enlaces --}}
                    <div class="mt-6">
                        <a href="{{ route('representatives.index') }}"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Ir a la Lista
                        </a>
                        <a href="{{ route('dashboard') }}"
                            class="underline text-sm px-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Volver al Panel
                        </a>

                        {{-- Dropdown --}}
                        <button data-dropdown-representative="{{ $representative->id }}"
                            class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                            Acciones
                        </button>

                        <div id="dropdown-template-{{ $representative->id }}" class="hidden">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                <li><a href="{{ route('representatives.students.create', $representative->id) }}"
                                        class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Asignar
                                        Estudiante</a>
                                </li>
                                <li><a href="{{ route('representatives.edit', $representative) }}"
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
            document.querySelectorAll("[data-dropdown-representative]").forEach(btn => {
                btn.addEventListener("click", () => {
                    const representativeId = btn.dataset.dropdownRepresentative;
                    let existing = document.getElementById("dropdownMenu-" + representativeId);
                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                    const tpl = document.getElementById("dropdown-template-" + representativeId);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + representativeId;
                    clone.classList.remove("hidden");
                    clone.classList.add("dropdown-clone", "fixed", "z-50", "w-40", "bg-white",
                        "dark:bg-gray-800", "border", "border-gray-200", "dark:border-gray-700",
                        "rounded", "shadow-lg");
                    const rect = btn.getBoundingClientRect();
                    const menuHeight = 160;
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
                if (!e.target.closest("[data-dropdown-representative]") && !e.target.closest(
                        ".dropdown-clone")) {
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                }
            });
        });
    </script>
</x-app-layout>
