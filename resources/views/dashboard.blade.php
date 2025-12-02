<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-4">
                        @if (Auth::user()->sex === 'Femenino')
                            Bienvenida, {{ Auth::user()->full_name }}!
                        @else
                            Bienvenido, {{ Auth::user()->full_name }}!
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
                                        Gestión de usuarios y administración</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menu para la Gestión de usuarios
                                        y administración de la Academia.</p>
                                </div>

                                {{-- Teachers card --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('teachersModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                        Modulo de profesores</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menú para la Gestión de
                                        profesores de la Academia.</p>
                                </div>

                                {{-- Representatives card --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('representativesModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Modulo
                                        de representantes</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menu para la Gestión de
                                        representantes de la Academia.</p>
                                </div>

                                {{-- Students card --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('studentsModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Modulo
                                        de estudiantes</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menu para la Gestión de
                                        estudiantes de la Academia.</p>
                                </div>

                                {{-- Subjects card --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('subjectsModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Modulo
                                        de asignaturas</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menu para la Gestión de
                                        asignaturas.</p>
                                </div>

                                {{-- Academic Periods card --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('academicPeriodsModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Modulo
                                        de períodos académicos</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menu para la Gestión de
                                        períodos académicos.</p>
                                </div>

                                {{-- Sections card --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('sectionsModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Modulo
                                        de secciones</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menu para la Gestión de
                                        secciones.</p>
                                </div>

                                {{-- Enrollments card --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('enrollmentsModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Modulo
                                        de inscripciones</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menu para la Gestión de
                                        inscripciones.</p>
                                </div>

                                {{-- Grades card (NUEVO) --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('gradesModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Modulo
                                        de calificaciones</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Configurar evaluaciones y
                                        registrar calificaciones.</p>
                                </div>

                                {{-- RoleAssignment card --}}
                                <div class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 cursor-pointer"
                                    onclick="openModal('roleAssignmentsModal')">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Modulo
                                        de asignación de rol</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Menu para la Gestión de
                                        Roles.</p>
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
                                        Informes y Estadísticas</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Visualizar datos y reportes de
                                        la academia.</p>
                                </a>

                            </div>
                        @endif
                    @endrole

                    {{-- Render content for Profesor --}}
                    @role('Profesor')
                        @if (auth()->user()->teacher?->isActive())
                            <h4 class="text-xl font-semibold mb-4 text-white">Mis Asignaciones</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                
                                {{-- Card para ir a Mis Asignaciones --}}
                                <a href="{{ route('teacher.assignments') }}"
                                    class="block p-4 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                        📚 Mis Materias</h5>
                                    <p class="font-normal text-gray-700 dark:text-gray-400">Ver mis asignaciones, 
                                        configurar evaluaciones y calificar estudiantes.</p>
                                </a>

                            </div>
                        @else
                            <div class="p-4 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                                <p class="text-yellow-800 dark:text-yellow-200">
                                    Tu perfil de profesor no está activo. Contacta al administrador.
                                </p>
                            </div>
                        @endif
                    @endrole

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
                <a href="{{ route('students.index') }}" class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">🔍
                    Buscar y Gestionar</a>
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

    {{-- Academic Periods Modal --}}
    <div id="academicPeriodsModal"
        class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white p-8 rounded-2xl shadow-xl w-96 text-center">
            <h3 class="text-xl font-bold mb-6">Períodos Académicos</h3>
            <div class="space-y-3">
                <a href="{{ route('academic-periods.index') }}"
                    class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">🔍
                    Buscar <br>➕ Registrar y Gestionar</a>
            </div>
            <button class="close-btn mt-6 text-red-500">Cerrar</button>
        </div>
    </div>

    {{-- Sections Modal --}}
    <div id="sectionsModal"
        class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white p-8 rounded-2xl shadow-xl w-96 text-center">
            <h3 class="text-xl font-bold mb-6">Secciones</h3>
            <div class="space-y-3">
                <a href="{{ route('sections.index') }}"
                    class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">🔍
                    Buscar <br>➕ Registrar y Gestionar</a>
            </div>
            <button class="close-btn mt-6 text-red-500">Cerrar</button>
        </div>
    </div>

    {{-- Enrollments Modal --}}
    <div id="enrollmentsModal"
        class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white p-8 rounded-2xl shadow-xl w-96 text-center">
            <h3 class="text-xl font-bold mb-6">Inscripciones</h3>
            <div class="space-y-3">
                <a href="{{ route('enrollments.index') }}"
                    class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">🔍
                    Buscar <br>➕ Registrar y Gestionar</a>
            </div>
            <button class="close-btn mt-6 text-red-500">Cerrar</button>
        </div>
    </div>

    {{-- Grades Modal (NUEVO) --}}
    <div id="gradesModal"
        class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white p-8 rounded-2xl shadow-xl w-96 text-center">
            <h3 class="text-xl font-bold mb-6">Calificaciones</h3>
            <div class="space-y-3">
                <a href="{{ route('sections.index') }}"
                    class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">📋
                    Ir a Secciones</a>
                <p class="text-sm text-gray-500 mt-2">
                    Selecciona una sección → Ver asignaciones → Calificar
                </p>
            </div>
            <button class="close-btn mt-6 text-red-500">Cerrar</button>
        </div>
    </div>

    {{-- RoleAssignment Modal --}}
    <div id="roleAssignmentsModal"
        class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white p-8 rounded-2xl shadow-xl w-96 text-center">
            <h3 class="text-xl font-bold mb-6">Asignación de rol</h3>
            <div class="space-y-3">
                <a href="{{ route('role-management.index') }}"
                    class="block bg-gray-200 py-2 rounded-lg hover:bg-blue-700">🔍
                    Buscar y Gestionar</a>
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