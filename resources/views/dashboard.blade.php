<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Muestra un saludo personalizado al usuario autenticado --}}
                    <h3 class="text-2xl font-bold mb-4">
                        @if(Auth::user()->sex === "Femenino")
                        Bienvenida, {{ Auth::user()->name }}!
                        @else Bienvenido, {{ Auth::user()->name }}!@endif
                    </h3>

                    <p class="mb-6">
                        Estás en el Panel de Control de la academia.
                    </p>

                    <hr class="my-6 border-gray-200 dark:border-gray-700">

                    {{--
                        Aquí se renderizará contenido diferente dependiendo del rol del usuario.
                        @role y @endrole son directivas de Spatie que facilitan esta lógica.
                    --}}
                    @role('superAdmin|admin')
                        {{-- Contenedor GRID para los cards, directamente después de la descripción del rol --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {{-- Card de Registro de empleados --}}
                            <a href="{{ route('register') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Registro de empleados</h5>
                                <p class="font-normal text-gray-700 dark:text-gray-400">Formulario de registro de empleados para la Academia.</p>
                            </a>

                            {{-- Otro Card de ejemplo --}}
                            <a href="#" class="block p-6 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Gestionar Administradores</h5>
                                <p class="font-normal text-gray-700 dark:text-gray-400">Ver, editar o eliminar cuentas de administradores.</p>
                            </a>

                            {{-- Un tercer Card de ejemplo --}}
                            <a href="#" class="block p-6 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Configuración del Sistema</h5>
                                <p class="font-normal text-gray-700 dark:text-gray-400">Acceder a las opciones de configuración global.</p>
                            </a>

                            {{-- Puedes añadir más cards aquí --}}
                            <a href="#" class="block p-6 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Informes y Estadísticas</h5>
                                <p class="font-normal text-gray-700 dark:text-gray-400">Visualizar datos y reportes de la academia.</p>
                            </a>
                        </div>
                    @endrole
                </div>
            </div>
        </div>
    </div>
</x-app-layout>