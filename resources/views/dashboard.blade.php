<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Control') }}
        </h2>
    </x-slot>

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
                    @role('superAdmin')
                    <div class="p-4 bg-red-50 dark:bg-red-900 rounded-lg">
                        <h4 class="text-xl font-semibold text-red-800 dark:text-red-200">Panel de SuperAdmin</h4>
                        <p class="mt-2 text-red-600 dark:text-red-400">
                            Tienes acceso a todas las funcionalidades del sistema.
                        </p>
                        
                        <a href="{{ route('register') }}" class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Noteworthy technology acquisitions 2021</h5>
                            <p class="font-normal text-gray-700 dark:text-gray-400">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
                        </a>
                        
                    </div>
                    @endrole

                    @role('Admin')
                    <div class="p-4 bg-red-50 dark:bg-red-900 rounded-lg">
                        <h4 class="text-xl font-semibold text-red-800 dark:text-red-200">Panel de Administrador</h4>
                        <p class="mt-2 text-red-600 dark:text-red-400">
                            Tienes acceso a muchas funcionalidades del sistema.
                        </p>
                    </div>
                    @endrole

                    @role('teacher')
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                        <h4 class="text-xl font-semibold text-yellow-800 dark:text-yellow-200">Panel de Profesor</h4>
                        <p class="mt-2 text-yellow-600 dark:text-yellow-400">
                            Tienes acceso a tus clases asignadas, a la gestión de calificaciones y a la asistencia de tus estudiantes.
                        </p>
                    </div>
                    @endrole

                    @role('student')
                    <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <h4 class="text-xl font-semibold text-blue-800 dark:text-blue-200">Panel de Estudiante</h4>
                        <p class="mt-2 text-blue-600 dark:text-blue-400">
                            Tienes acceso a tus calificaciones, horarios y a la información de tus cursos.
                        </p>
                    </div>
                    @endrole

                    @role('representative')
                    <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg">
                        <h4 class="text-xl font-semibold text-green-800 dark:text-green-200">Panel de Representante</h4>
                        <p class="mt-2 text-green-600 dark:text-green-400">
                            Tienes acceso a la información académica de tu estudiante representado.
                        </p>
                    </div>
                    @endrole
                </div>
            </div>
        </div>
    </div>
</x-app-layout>