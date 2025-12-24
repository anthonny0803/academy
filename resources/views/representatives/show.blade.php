<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            Detalles del Representante
                        </h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $representative->is_active 
                                ? 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100' 
                                : 'bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100' }}">
                            {{ $representative->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    {{-- Información Personal --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        {{-- Columna izquierda --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nombre completo</label>
                                <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $representative->full_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Correo electrónico</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $representative->email }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Documento de identidad</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $representative->document_id ?? 'No registrado' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Sexo</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $representative->sex }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Edad</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">
                                    {{ $representative->age ? $representative->age . ' años' : 'No registrada' }}
                                </p>
                            </div>
                        </div>

                        {{-- Columna derecha --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $representative->phone ?? 'No registrado' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Dirección</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $representative->address ?? 'No registrada' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Ocupación</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $representative->occupation ?? 'No registrada' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de registro</label>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $representative->created_at->format('d/m/Y') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Roles</label>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @forelse ($representative->user->roles as $role)
                                        <span class="inline-block bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-100 text-xs px-2 py-1 rounded">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400 italic text-sm">Sin rol</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200 dark:border-gray-700">

                    {{-- Sección de Estudiantes --}}
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Estudiantes asociados</h2>
                                @php
                                    $totalStudents = $representative->students->count();
                                    $activeStudents = $representative->students->where('is_active', true)->count();
                                    $inactiveStudents = $totalStudents - $activeStudents;
                                @endphp
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $totalStudents }} estudiante(s)
                                    @if($totalStudents > 0)
                                        <span class="text-green-600 dark:text-green-400">({{ $activeStudents }} activo(s)</span>,
                                        <span class="text-yellow-600 dark:text-yellow-400">{{ $inactiveStudents }} inactivo(s))</span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('representatives.students.create', $representative) }}"
                               class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Agregar estudiante
                            </a>
                        </div>

                        @if($representative->students->isEmpty())
                            <div class="text-center py-8 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <p class="mt-2 text-gray-500 dark:text-gray-400">No hay estudiantes asociados</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($representative->students as $student)
                                    <a href="{{ route('students.show', $student) }}"
                                       class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-indigo-500 dark:hover:border-indigo-400 hover:shadow-md transition-all">
                                        
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                                {{ $student->full_name }}
                                            </h3>
                                            {{-- Estado técnico --}}
                                            <span class="flex-shrink-0 ml-2 w-2 h-2 rounded-full {{ $student->is_active ? 'bg-green-500' : 'bg-yellow-500' }}"
                                                  title="{{ $student->is_active ? 'Activo' : 'Inactivo' }}">
                                            </span>
                                        </div>

                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                            {{ $student->student_code }}
                                        </p>

                                        <div class="flex flex-wrap gap-1 mb-2">
                                            {{-- Parentesco --}}
                                            <span class="inline-block bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 text-xs px-2 py-0.5 rounded">
                                                {{ $student->relationship_type }}
                                            </span>
                                            {{-- Situación --}}
                                            <span class="inline-block text-xs px-2 py-0.5 rounded
                                                @switch($student->situation?->value)
                                                    @case('Cursando')
                                                        bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100
                                                        @break
                                                    @case('Pausado')
                                                    @case('Baja médica')
                                                    @case('Situación familiar')
                                                        bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100
                                                        @break
                                                    @case('Suspendido')
                                                        bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100
                                                        @break
                                                    @case('Sin actividad')
                                                        bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-100
                                                        @break
                                                    @default
                                                        bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-100
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

                    <hr class="my-6 border-gray-200 dark:border-gray-700">

                    {{-- Acciones --}}
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('representatives.index') }}"
                           class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline">
                            ← Volver a la lista
                        </a>
                        <a href="{{ route('dashboard') }}"
                           class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline">
                            Ir al Panel
                        </a>
                        <a href="{{ route('representatives.edit', $representative) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                            Editar representante
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>