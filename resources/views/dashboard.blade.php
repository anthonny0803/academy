<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Card --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 dark:from-indigo-800 dark:via-purple-800 dark:to-indigo-900 rounded-2xl shadow-xl mb-8">
                {{-- Decorative elements --}}
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                
                <div class="relative px-6 py-8 sm:px-8 sm:py-10">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">
                                @if (Auth::user()->sex === 'Femenino')
                                    Bienvenida, {{ Auth::user()->full_name }}!
                                @else
                                    Bienvenido, {{ Auth::user()->full_name }}!
                                @endif
                            </h1>
                            <p class="mt-2 text-indigo-100 dark:text-indigo-200">
                                Panel de Control de la Academia
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-xl text-white text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content Card --}}
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 dark:border-gray-700/50 overflow-hidden">
                <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100">

                    {{-- Render content for Supervisor and Administrador --}}
                    @role('Supervisor|Administrador')
                        @if (auth()->user()->isActive())
                            
                            {{-- SECCIÓN: Administración --}}
                            <div class="mb-8">
                                <h4 class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Administración
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    
                                    {{-- Users card --}}
                                    <div class="group relative overflow-hidden bg-gradient-to-br from-indigo-50 to-white dark:from-indigo-950 dark:to-gray-800 border-l-4 border-indigo-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer"
                                        onclick="openModal('usersModal')">
                                        <div class="p-5">
                                            <div class="flex items-start justify-between">
                                                <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                    </svg>
                                                </div>
                                                <svg class="w-5 h-5 text-indigo-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                            <h5 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Usuarios</h5>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gestión de cuentas y accesos</p>
                                        </div>
                                    </div>

                                    {{-- RoleAssignment card --}}
                                    <div class="group relative overflow-hidden bg-gradient-to-br from-indigo-50 to-white dark:from-indigo-950 dark:to-gray-800 border-l-4 border-indigo-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer"
                                        onclick="openModal('roleAssignmentsModal')">
                                        <div class="p-5">
                                            <div class="flex items-start justify-between">
                                                <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                    </svg>
                                                </div>
                                                <svg class="w-5 h-5 text-indigo-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                            <h5 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Roles</h5>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Asignación de permisos</p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- SECCIÓN: Personas --}}
                            <div class="mb-8">
                                <h4 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Personas
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    
                                    {{-- Teachers card --}}
                                    <div class="group relative overflow-hidden bg-gradient-to-br from-emerald-50 to-white dark:from-emerald-950 dark:to-gray-800 border-l-4 border-emerald-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer"
                                        onclick="openModal('teachersModal')">
                                        <div class="p-5">
                                            <div class="flex items-start justify-between">
                                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900 rounded-lg">
                                                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <svg class="w-5 h-5 text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                            <h5 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Profesores</h5>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Personal docente</p>
                                        </div>
                                    </div>

                                    {{-- Representatives card --}}
                                    <div class="group relative overflow-hidden bg-gradient-to-br from-emerald-50 to-white dark:from-emerald-950 dark:to-gray-800 border-l-4 border-emerald-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer"
                                        onclick="openModal('representativesModal')">
                                        <div class="p-5">
                                            <div class="flex items-start justify-between">
                                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900 rounded-lg">
                                                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                </div>
                                                <svg class="w-5 h-5 text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                            <h5 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Representantes</h5>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Padres y tutores</p>
                                        </div>
                                    </div>

                                    {{-- Students card --}}
                                    <div class="group relative overflow-hidden bg-gradient-to-br from-emerald-50 to-white dark:from-emerald-950 dark:to-gray-800 border-l-4 border-emerald-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer"
                                        onclick="openModal('studentsModal')">
                                        <div class="p-5">
                                            <div class="flex items-start justify-between">
                                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900 rounded-lg">
                                                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                    </svg>
                                                </div>
                                                <svg class="w-5 h-5 text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                            <h5 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Estudiantes</h5>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Alumnos matriculados</p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- SECCIÓN: Académico --}}
                            <div class="mb-8">
                                <h4 class="text-sm font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                                    </svg>
                                    Académico
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    
                                    {{-- Academic Periods card --}}
                                    <div class="group relative overflow-hidden bg-gradient-to-br from-violet-50 to-white dark:from-violet-950 dark:to-gray-800 border-l-4 border-violet-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer"
                                        onclick="openModal('academicPeriodsModal')">
                                        <div class="p-5">
                                            <div class="flex items-start justify-between">
                                                <div class="p-2 bg-violet-100 dark:bg-violet-900 rounded-lg">
                                                    <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <svg class="w-5 h-5 text-violet-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                            <h5 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Períodos</h5>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ciclos académicos</p>
                                        </div>
                                    </div>

                                    {{-- Sections card --}}
                                    <div class="group relative overflow-hidden bg-gradient-to-br from-violet-50 to-white dark:from-violet-950 dark:to-gray-800 border-l-4 border-violet-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer"
                                        onclick="openModal('sectionsModal')">
                                        <div class="p-5">
                                            <div class="flex items-start justify-between">
                                                <div class="p-2 bg-violet-100 dark:bg-violet-900 rounded-lg">
                                                    <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                    </svg>
                                                </div>
                                                <svg class="w-5 h-5 text-violet-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                            <h5 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Secciones</h5>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Aulas y grupos</p>
                                        </div>
                                    </div>

                                    {{-- Subjects card --}}
                                    <div class="group relative overflow-hidden bg-gradient-to-br from-violet-50 to-white dark:from-violet-950 dark:to-gray-800 border-l-4 border-violet-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer"
                                        onclick="openModal('subjectsModal')">
                                        <div class="p-5">
                                            <div class="flex items-start justify-between">
                                                <div class="p-2 bg-violet-100 dark:bg-violet-900 rounded-lg">
                                                    <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                                    </svg>
                                                </div>
                                                <svg class="w-5 h-5 text-violet-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                            <h5 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Asignaturas</h5>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Materias y cursos</p>
                                        </div>
                                    </div>

                                    {{-- Enrollments card --}}
                                    <div class="group relative overflow-hidden bg-gradient-to-br from-violet-50 to-white dark:from-violet-950 dark:to-gray-800 border-l-4 border-violet-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer"
                                        onclick="openModal('enrollmentsModal')">
                                        <div class="p-5">
                                            <div class="flex items-start justify-between">
                                                <div class="p-2 bg-violet-100 dark:bg-violet-900 rounded-lg">
                                                    <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <svg class="w-5 h-5 text-violet-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                            <h5 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Inscripciones</h5>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Matrículas activas</p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- SECCIÓN: Sistema --}}
                            <div class="mb-8">
                                <h4 class="text-sm font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Sistema
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    
                                    {{-- Configuración del Sistema --}}
                                    <div class="group relative overflow-hidden bg-gradient-to-br from-slate-50 to-white dark:from-slate-900 dark:to-gray-800 border-l-4 border-slate-400 rounded-lg shadow-md opacity-60 cursor-not-allowed">
                                        <div class="absolute top-2 right-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                                Próximamente
                                            </span>
                                        </div>
                                        <div class="p-5">
                                            <div class="flex items-start justify-between">
                                                <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg">
                                                    <svg class="w-6 h-6 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <h5 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Configuración</h5>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ajustes del sistema</p>
                                        </div>
                                    </div>

                                    {{-- Informes y Estadísticas --}}
                                    <div class="group relative overflow-hidden bg-gradient-to-br from-slate-50 to-white dark:from-slate-900 dark:to-gray-800 border-l-4 border-slate-400 rounded-lg shadow-md opacity-60 cursor-not-allowed">
                                        <div class="absolute top-2 right-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                                Próximamente
                                            </span>
                                        </div>
                                        <div class="p-5">
                                            <div class="flex items-start justify-between">
                                                <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg">
                                                    <svg class="w-6 h-6 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <h5 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Informes</h5>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Reportes y estadísticas</p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        @endif
                    @endrole

                    {{-- Render content for Profesor --}}
                    @role('Profesor')
                        @if (auth()->user()->teacher?->isActive())
                            <div class="mb-8">
                                <h4 class="text-sm font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Mi Área Docente
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    
                                    {{-- Mis Asignaturas --}}
                                    <a href="{{ route('teacher.assignments') }}"
                                        class="group relative overflow-hidden bg-gradient-to-br from-amber-50 to-white dark:from-amber-950 dark:to-gray-800 border-l-4 border-amber-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                                        <div class="p-5">
                                            <div class="flex items-start justify-between">
                                                <div class="p-2 bg-amber-100 dark:bg-amber-900 rounded-lg">
                                                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                    </svg>
                                                </div>
                                                <svg class="w-5 h-5 text-amber-400 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                            <h5 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">Mis Asignaturas</h5>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ver asignaciones, configurar evaluaciones y calificar</p>
                                        </div>
                                    </a>

                                </div>
                            </div>
                        @else
                            <div class="p-4 bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-500 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <p class="text-yellow-800 dark:text-yellow-200">
                                        Tu perfil de profesor no está activo. Contacta al administrador.
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endrole

                </div>
            </div>

            {{-- Footer info --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Academy Management System
                </p>
            </div>
        </div>
    </div>

    {{-- ==================== MODALES ==================== --}}

    {{-- Users Modal --}}
    <div id="usersModal" class="modal hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-96 border-t-4 border-indigo-500">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Usuarios</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('users.create') }}" class="flex items-center gap-3 px-4 py-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Registrar usuario
                </a>
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar y gestionar
                </a>
            </div>
            <button class="close-btn mt-6 w-full py-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Cerrar</button>
        </div>
    </div>

    {{-- Teachers Modal --}}
    <div id="teachersModal" class="modal hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-96 border-t-4 border-emerald-500">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Profesores</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('teachers.create') }}" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Registrar profesor
                </a>
                <a href="{{ route('teachers.index') }}" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar y gestionar
                </a>
            </div>
            <button class="close-btn mt-6 w-full py-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Cerrar</button>
        </div>
    </div>

    {{-- Representatives Modal --}}
    <div id="representativesModal" class="modal hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-96 border-t-4 border-emerald-500">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Representantes</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('representatives.create') }}" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Registrar representante
                </a>
                <a href="{{ route('representatives.index') }}" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar y gestionar
                </a>
            </div>
            <button class="close-btn mt-6 w-full py-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Cerrar</button>
        </div>
    </div>

    {{-- Students Modal --}}
    <div id="studentsModal" class="modal hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-96 border-t-4 border-emerald-500">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Estudiantes</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('students.index') }}" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar y gestionar
                </a>
            </div>
            <button class="close-btn mt-6 w-full py-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Cerrar</button>
        </div>
    </div>

    {{-- Subjects Modal --}}
    <div id="subjectsModal" class="modal hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-96 border-t-4 border-violet-500">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-violet-100 dark:bg-violet-900 rounded-lg">
                    <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Asignaturas</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('subjects.index') }}" class="flex items-center gap-3 px-4 py-3 bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 rounded-xl hover:bg-violet-100 dark:hover:bg-violet-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar, registrar y gestionar
                </a>
            </div>
            <button class="close-btn mt-6 w-full py-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Cerrar</button>
        </div>
    </div>

    {{-- Academic Periods Modal --}}
    <div id="academicPeriodsModal" class="modal hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-96 border-t-4 border-violet-500">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-violet-100 dark:bg-violet-900 rounded-lg">
                    <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Períodos Académicos</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('academic-periods.index') }}" class="flex items-center gap-3 px-4 py-3 bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 rounded-xl hover:bg-violet-100 dark:hover:bg-violet-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar, registrar y gestionar
                </a>
            </div>
            <button class="close-btn mt-6 w-full py-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Cerrar</button>
        </div>
    </div>

    {{-- Sections Modal --}}
    <div id="sectionsModal" class="modal hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-96 border-t-4 border-violet-500">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-violet-100 dark:bg-violet-900 rounded-lg">
                    <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Secciones</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('sections.index') }}" class="flex items-center gap-3 px-4 py-3 bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 rounded-xl hover:bg-violet-100 dark:hover:bg-violet-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar, registrar y gestionar
                </a>
            </div>
            <button class="close-btn mt-6 w-full py-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Cerrar</button>
        </div>
    </div>

    {{-- Enrollments Modal --}}
    <div id="enrollmentsModal" class="modal hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-96 border-t-4 border-violet-500">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-violet-100 dark:bg-violet-900 rounded-lg">
                    <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Inscripciones</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('enrollments.index') }}" class="flex items-center gap-3 px-4 py-3 bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 rounded-xl hover:bg-violet-100 dark:hover:bg-violet-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar, registrar y gestionar
                </a>
            </div>
            <button class="close-btn mt-6 w-full py-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Cerrar</button>
        </div>
    </div>

    {{-- Grades Modal --}}
    <div id="gradesModal" class="modal hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-96 border-t-4 border-amber-500">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-amber-100 dark:bg-amber-900 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Calificaciones</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('sections.index') }}" class="flex items-center gap-3 px-4 py-3 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-xl hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Ir a Secciones
                </a>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center px-2">
                    Selecciona una sección → Ver asignaciones → Calificar
                </p>
            </div>
            <button class="close-btn mt-6 w-full py-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Cerrar</button>
        </div>
    </div>

    {{-- RoleAssignment Modal --}}
    <div id="roleAssignmentsModal" class="modal hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="modal-content bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-96 border-t-4 border-indigo-500">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Asignación de Roles</h3>
            </div>
            <div class="space-y-3">
                <a href="{{ route('role-management.index') }}" class="flex items-center gap-3 px-4 py-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Buscar y gestionar
                </a>
            </div>
            <button class="close-btn mt-6 w-full py-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Cerrar</button>
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

        // Cerrar modal con tecla Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal:not(.hidden)').forEach(modal => {
                    modal.classList.add('hidden');
                });
            }
        });
    </script>
</x-app-layout>