<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header Card --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-emerald-600 via-emerald-600 to-teal-700 dark:from-emerald-800 dark:via-emerald-800 dark:to-teal-900 rounded-2xl shadow-xl mb-8">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-8">
                    {{-- Back link --}}
                    <a href="{{ route('representatives.index') }}" class="inline-flex items-center gap-2 text-emerald-200 hover:text-white transition-colors mb-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Ir al listado
                    </a>

                    {{-- Representative info --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                        <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-xl border border-white/30">
                            {{ strtoupper(substr($representative->user->name ?? 'R', 0, 1)) }}{{ strtoupper(substr($representative->user->last_name ?? 'E', 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-1">
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $representative->full_name }}</h1>
                                @if ($representative->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-400/20 text-green-100 border border-green-400/30">
                                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-400/20 text-yellow-100 border border-yellow-400/30">
                                        <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full mr-1.5"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </div>
                            <p class="text-emerald-100">{{ $representative->email }}</p>
                            <div class="flex flex-wrap items-center gap-2 mt-3">
                                @forelse ($representative->user->roles as $role)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white backdrop-blur-sm">
                                        {{ $role->name }}
                                    </span>
                                @empty
                                    <span class="text-emerald-200 italic">Sin rol asignado</span>
                                @endforelse
                            </div>
                        </div>
                        <a href="{{ route('representatives.edit', $representative) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white font-medium rounded-xl border border-white/20 transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </a>
                    </div>
                </div>
            </div>

            {{-- Content Grid --}}
            <div class="grid gap-6 md:grid-cols-2 mb-8">

                {{-- Personal Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Información Personal
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre completo</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white font-medium">{{ $representative->full_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Documento de identidad</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $representative->document_id ?? 'No registrado' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sexo</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $representative->sex }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Edad</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $representative->age ? $representative->age . ' años' : 'No registrada' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ocupación</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $representative->occupation ?? 'No registrada' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Información de Contacto
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Correo electrónico</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $representative->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $representative->phone ?? 'No registrado' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dirección</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $representative->address ?? 'No registrada' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de registro</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $representative->created_at->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

            </div>

            {{-- Students Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Estudiantes Asociados
                            </h3>
                            @php
                                $totalStudents = $representative->students->count();
                                $activeStudents = $representative->students->where('is_active', true)->count();
                                $inactiveStudents = $totalStudents - $activeStudents;
                            @endphp
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $totalStudents }} estudiante(s)
                                @if($totalStudents > 0)
                                    — <span class="text-green-600 dark:text-green-400">{{ $activeStudents }} activo(s)</span>,
                                    <span class="text-yellow-600 dark:text-yellow-400">{{ $inactiveStudents }} inactivo(s)</span>
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('representatives.students.create', $representative) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Agregar estudiante
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    @if($representative->students->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">No hay estudiantes asociados a este representante</p>
                            <a href="{{ route('representatives.students.create', $representative) }}"
                               class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Agregar el primer estudiante
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($representative->students as $student)
                                <a href="{{ route('students.show', $student) }}"
                                   class="group block p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-emerald-500 dark:hover:border-emerald-400 hover:shadow-lg transition-all duration-300">
                                    
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-lg flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                                {{ strtoupper(substr($student->user->name ?? 'E', 0, 1)) }}{{ strtoupper(substr($student->user->last_name ?? 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                                    {{ $student->full_name }}
                                                </h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $student->student_code }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="flex-shrink-0 w-2.5 h-2.5 rounded-full {{ $student->is_active ? 'bg-green-500' : 'bg-yellow-500' }}"
                                              title="{{ $student->is_active ? 'Activo' : 'Inactivo' }}">
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap gap-1.5 mb-3">
                                        {{-- Parentesco --}}
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                            {{ $student->relationship_type }}
                                        </span>
                                        {{-- Situación --}}
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                                            @switch($student->situation?->value)
                                                @case('Cursando')
                                                    bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300
                                                    @break
                                                @case('Pausado')
                                                @case('Baja médica')
                                                @case('Situación familiar')
                                                    bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300
                                                    @break
                                                @case('Suspendido')
                                                    bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300
                                                    @break
                                                @case('Sin actividad')
                                                    bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300
                                                    @break
                                                @default
                                                    bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300
                                            @endswitch">
                                            {{ $student->situation?->value ?? 'Sin situación' }}
                                        </span>
                                    </div>

                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $student->age ? $student->age . ' años' : 'Edad no registrada' }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="flex justify-center">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Volver al Panel de Control
                </a>
            </div>

        </div>
    </div>
</x-app-layout>