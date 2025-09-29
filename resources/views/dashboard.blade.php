<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-4">
                        @if (Auth::user()->sex === 'Femenino')
                            Bienvenida, {{ Auth::user()->name }}!
                        @else
                            Bienvenido, {{ Auth::user()->name }}!
                        @endif
                    </h3>
                    <p class="mb-6">
                        Estás en el Panel de Control de la academia.
                    </p>
                    <hr class="my-6 border-gray-200 dark:border-gray-700">

                    {{-- Render content for Supervisor and Administrador --}}
                    @role('Supervisor|Administrador')
                        @if (auth()->user()->isActive())
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                                {{-- Users card --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('usersModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                        Modulo de usuarios</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menu para la Gestión de usuarios
                                        de
                                        la Academia.</p>
                                </div>

                                {{-- Teachers card (Nuevo módulo) --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('teachersModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                        Modulo de profesores</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menú para la Gestión de
                                        profesores
                                        de
                                        la Academia.</p>
                                </div>

                                {{-- Representatives card --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('representativesModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Modulo
                                        de
                                        representantes</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menu para la Gestión de
                                        representantes de la Academia.</p>
                                </div>

                                {{-- Students card --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('studentsModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Modulo
                                        de
                                        estudiantes</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menu para la Gestión de
                                        estudiantes
                                        de la Academia.</p>
                                </div>

                                {{-- Subjects card --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('subjectsModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Modulo
                                        de
                                        asignaturas</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menu para la Gestión de
                                        asignaturas.
                                    </p>
                                </div>

                                {{-- Configuración del Sistema --}}
                                <a href="#"
                                    class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                        Configuración del Sistema</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Acceder a las opciones de
                                        configuración global.</p>
                                </a>

                                {{-- Informes y Estadísticas --}}
                                <a href="#"
                                    class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                        Informes y
                                        Estadísticas</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Visualizar datos y reportes de
                                        la
                                        academia.</p>
                                </a>

                            </div>
                        @endif
                    @endrole

                    {{-- End of Supervisor and Administrador render content --}}

                </div>
            </div>
        </div>
    </div>

    {{-- Users Modal --}}
    <div id="usersModal"
        class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white p-8 rounded-2xl shadow-xl w-96 text-center">
            <h3 class="text-xl font-bold mb-6">Usuarios</h3>
            <div class="space-y-3">
                <a href="{{ route('users.create') }}" class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">➕
                    Registrar</a>
                <a href="{{ route('users.index') }}" class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">🔍
                    Buscar y Gestionar</a>
            </div>
            <button class="close-btn mt-6 text-red-500">Cerrar</button>
        </div>
    </div>

    {{-- Teachers Modal --}}
    <div id="teachersModal"
        class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white p-8 rounded-2xl shadow-xl w-96 text-center">
            <h3 class="text-xl font-bold mb-6">Profesores</h3>
            <div class="space-y-3">
                <a href="{{ route('teachers.create') }}" class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">➕
                    Registrar</a>
                <a href="{{ route('teachers.index') }}" class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">🔍
                    Buscar y Gestionar</a>
            </div>
            <button class="close-btn mt-6 text-red-500">Cerrar</button>
        </div>
    </div>

    {{-- Representatives Modal --}}
    <div id="representativesModal"
        class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white p-8 rounded-2xl shadow-xl w-96 text-center">
            <h3 class="text-xl font-bold mb-6">Representantes</h3>
            <div class="space-y-3">
                <a href="{{ route('representatives.create') }}"
                    class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">➕ Registrar</a>
                <a href="{{ route('representatives.index') }}"
                    class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">🔍 Buscar y Gestionar</a>
            </div>
            <button class="close-btn mt-6 text-red-500">Cerrar</button>
        </div>
    </div>

    {{-- Students Modal --}}
    <div id="studentsModal"
        class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white p-8 rounded-2xl shadow-xl w-96 text-center">
            <h3 class="text-xl font-bold mb-6">Estudiantes</h3>
            <div class="space-y-3">
                <a href="#" class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">➕ Registrar</a>
                <a href="#" class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">🔍 Buscar y Gestionar</a>
            </div>
            <button class="close-btn mt-6 text-red-500">Cerrar</button>
        </div>
    </div>

    {{-- Subjects Modal --}}
    <div id="subjectsModal"
        class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white p-8 rounded-2xl shadow-xl w-96 text-center">
            <h3 class="text-xl font-bold mb-6">Asignaturas</h3>
            <div class="space-y-3">
                <a href="{{ route('subjects.index') }}" class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">🔍
                    Buscar <br>➕ Registrar y Gestionar</a>
            </div>
            <button class="close-btn mt-6 text-red-500">Cerrar</button>
        </div>
    </div>

    {{-- Modal Scripts --}}
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        // Cerrar modal al hacer click en el botón
        document.querySelectorAll('.close-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.target.closest('.modal').classList.add('hidden');
            });
        });

        // Cerrar modal al hacer click fuera del contenido
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
