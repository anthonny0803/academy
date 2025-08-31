<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Lista de Usuarios</h1>

                    {{-- Buscador --}}
                    <div class="mb-4">
                        <form method="GET" action="{{ route('users.index') }}" class="flex gap-2">
                            <input type="text" name="search" placeholder="Buscar..." value="{{ request('search') }}"
                                class="block w-80 rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-800 p-2"
                                autocomplete="off" style="text-transform:uppercase;">
                            <button type="submit"
                                class="bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700">Buscar</button>
                            <a class="underline px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                href="{{ route('dashboard') }}">
                                {{ __('Volver al Panel') }}
                            </a>
                        </form>
                    </div>

                    {{-- Tabla --}}
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full text-white bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Nombre</th>
                                    <th class="py-2 px-4 border-b text-left">Correo</th>
                                    <th class="py-2 px-4 border-b text-left">Sexo</th>
                                    <th class="py-2 px-4 border-b text-left">Rol</th>
                                    <th class="py-2 px-4 border-b text-left">Gestionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-2 px-4 border-b">{{ $user->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                                        <td class="py-2 px-4 border-b">{{ $user->sex }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if ($user->roles->isNotEmpty())
                                                @foreach ($user->roles as $role)
                                                    <span
                                                        class="inline-block bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-100 text-xs px-2 py-1 rounded mr-1">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-gray-400 italic">Sin rol</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <button data-dropdown-user="{{ $user->id }}"
                                                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                                Acciones
                                            </button>

                                            {{-- Plantilla oculta del menú (se clona al body) --}}
                                            <div id="dropdown-template-{{ $user->id }}" class="hidden">
                                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                                                    @if ($user->hasRole('Profesor'))
                                                        <li>
                                                            <a href="#"
                                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Asignar
                                                                Materias</a>
                                                        </li>
                                                    @endif
                                                    <li><a href="{{ route('users.show', $user) }}"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Ver</a>
                                                    </li>
                                                    <li><a href="{{ route('users.edit', $user) }}"
                                                            class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Editar</a>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="#">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-red-600">
                                                                Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-2 px-4 text-center text-gray-500">
                                            @if (request()->filled('search'))
                                                No se encontraron resultados
                                            @else
                                                Ingresa un término de búsqueda para ver resultados
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    @if ($users->count() > 0)
                        <div class="mt-4">
                            {{ $users->appends(request()->except('page'))->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Script para teletransportar dropdowns al body --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll("[data-dropdown-user]").forEach(btn => {
                btn.addEventListener("click", () => {
                    const userId = btn.dataset.dropdownUser;
                    let existing = document.getElementById("dropdownMenu-" + userId);

                    // Si ya existe abierto → lo cerramos
                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }

                    // Cerrar otros
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());

                    // Clonar plantilla
                    const tpl = document.getElementById("dropdown-template-" + userId);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + userId;
                    clone.classList.remove("hidden");
                    clone.classList.add(
                        "dropdown-clone", "fixed", "z-50", "w-40",
                        "bg-white", "dark:bg-gray-800", "border", "border-gray-200",
                        "dark:border-gray-700", "rounded", "shadow-lg"
                    );

                    // Calcular posición del botón
                    const rect = btn.getBoundingClientRect();
                    const menuHeight =
                        160; // altura aproximada del menú (ajusta según tu contenido)
                    const espacioAbajo = window.innerHeight - rect.bottom;
                    const espacioArriba = rect.top;

                    if (espacioAbajo >= menuHeight) {
                        // Cabe hacia abajo
                        clone.style.top = rect.bottom + "px";
                    } else if (espacioArriba >= menuHeight) {
                        // Cabe hacia arriba
                        clone.style.top = (rect.top - menuHeight) + "px";
                    } else {
                        // Ajuste de emergencia: que ocupe lo que pueda abajo
                        clone.style.top = rect.bottom + "px";
                        clone.style.maxHeight = espacioAbajo + "px";
                        clone.style.overflowY = "auto";
                    }

                    clone.style.left = rect.left + "px";

                    document.body.appendChild(clone);
                });
            });

            // Cerrar al hacer clic fuera
            document.addEventListener("click", e => {
                if (!e.target.closest("[data-dropdown-user]") &&
                    !e.target.closest(".dropdown-clone")) {
                    document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
                }
            });
        });
    </script>

</x-app-layout>
