<x-app-layout>
    @php
        $relationshipTypes = \App\Enums\RelationshipType::cases();
    @endphp

    <div class="py-8 sm:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-emerald-600 via-emerald-600 to-teal-700 dark:from-emerald-800 dark:via-emerald-800 dark:to-teal-900 rounded-2xl shadow-xl mb-8">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-8">
                    <a href="{{ route('students.show', $student) }}" class="inline-flex items-center gap-2 text-emerald-200 hover:text-white transition-colors mb-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver al estudiante
                    </a>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Cambiar Representante</h1>
                    <p class="text-emerald-100 mt-2">Transferir la tutela del estudiante a otro representante</p>
                </div>
            </div>

            {{-- Self-represented Alert --}}
            @if($isSelfRepresented)
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-6 mb-8">
                    <div class="flex gap-4">
                        <svg class="w-6 h-6 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-amber-800 dark:text-amber-200 mb-1">Estudiante Auto-representado</h4>
                            <p class="text-sm text-amber-700 dark:text-amber-300">
                                Este estudiante es mayor de edad y actúa como su propio representante. No es posible cambiar su representante.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3 mb-8">
                {{-- Student Info Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Estudiante
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                {{ strtoupper(substr($student->user->name, 0, 1)) }}{{ strtoupper(substr($student->user->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $student->full_name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $student->student_code }}</p>
                                @if($student->age)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->age }} años</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Current Representative Card --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Representante Actual
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($student->representative)
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center text-white font-bold shadow-lg">
                                    {{ strtoupper(substr($student->representative->user->name, 0, 1)) }}{{ strtoupper(substr($student->representative->user->last_name, 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $student->representative->full_name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->representative->email }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Relación: {{ $student->relationship_type }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $student->representative->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $student->representative->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $student->representative->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 italic">Sin representante asignado</p>
                        @endif
                    </div>
                </div>
            </div>

            @if(!$isSelfRepresented)
                {{-- Convert to Self-Represented Card --}}
                @php
                    $isOfAge = $student->age !== null && $student->age >= 18;
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Convertir a Auto-representante
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="flex-1">
                                <p class="text-gray-700 dark:text-gray-300 mb-2">
                                    Si el estudiante ha alcanzado la mayoría de edad, puede convertirse en su propio representante.
                                </p>
                                @if(!$isOfAge)
                                    <p class="text-sm text-amber-600 dark:text-amber-400">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        El estudiante tiene {{ $student->age ?? 'edad desconocida' }} años. Debe ser mayor de 18 años.
                                    </p>
                                @endif
                            </div>
                            @if($canReassign && $isOfAge)
                                <button type="button" onclick="openSelfRepresentedModal()"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Convertir a Auto-representante
                                </button>
                            @elseif(!$canReassign)
                                <span class="text-sm text-gray-400 dark:text-gray-500 italic">Solo Supervisores</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Search & Results Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Buscar Nuevo Representante
                        </h3>
                    </div>
                    
                    {{-- Search Form --}}
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <form method="GET" action="{{ route('students.reassign-representative.form', $student) }}" class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}" 
                                           placeholder="Buscar por nombre, documento, correo, teléfono..."
                                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                                           autocomplete="off">
                                </div>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar
                            </button>
                        </form>
                    </div>

                    {{-- Results Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Representante</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Documento</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Contacto</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($representatives as $representative)
                                    @php
                                        $isCurrentRep = $representative->id === $student->representative_id;
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ $isCurrentRep ? 'bg-amber-50 dark:bg-amber-900/10' : '' }}">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                                    {{ strtoupper(substr($representative->user->name, 0, 1)) }}{{ strtoupper(substr($representative->user->last_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900 dark:text-white">{{ $representative->user->name }}</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $representative->user->last_name }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ $representative->document_id ?? 'Sin documento' }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $representative->email ?? 'Sin correo' }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $representative->phone ?? 'Sin teléfono' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($representative->is_active)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                                    Activo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                                                    Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($isCurrentRep)
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Actual
                                                </span>
                                            @elseif($canReassign)
                                                <button type="button" 
                                                        onclick="openReassignModal({{ $representative->id }}, '{{ addslashes($representative->full_name) }}', '{{ $representative->email ?? 'Sin correo' }}', {{ $representative->is_active ? 'true' : 'false' }})"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                    </svg>
                                                    Seleccionar
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-400 dark:text-gray-500">Sin permiso</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400">
                                                {{ request('search') ? 'No se encontraron representantes con los criterios de búsqueda.' : 'Ingrese un término de búsqueda para ver resultados.' }}
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($representatives instanceof \Illuminate\Pagination\LengthAwarePaginator && $representatives->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $representatives->links() }}
                        </div>
                    @endif
                </div>

                {{-- Permission Alert --}}
                @if(!$canReassign)
                    <div class="mt-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-6">
                        <div class="flex gap-4">
                            <svg class="w-6 h-6 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <h4 class="font-semibold text-amber-800 dark:text-amber-200 mb-1">Acción restringida</h4>
                                <p class="text-sm text-amber-700 dark:text-amber-300">
                                    Solo los usuarios con rol de <strong>Supervisor</strong> o <strong>Desarrollador</strong> pueden cambiar el representante de un estudiante.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </div>

    {{-- Reassign Modal --}}
    @if($canReassign && !$isSelfRepresented)
        <div id="reassignModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-200 dark:border-gray-700">
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Cambiar Representante</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Confirme el cambio de tutela</p>
                        </div>
                    </div>
                </div>

                {{-- Modal Body --}}
                <form id="reassignForm" action="{{ route('students.reassign-representative', $student) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="representative_id" id="modal_representative_id">

                    <div class="p-6 space-y-4">
                        {{-- Transfer Summary --}}
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 space-y-3">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-24">Estudiante:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $student->full_name }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-24">De:</span>
                                <span class="text-gray-700 dark:text-gray-200">{{ $student->representative?->full_name ?? 'Sin representante' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-24">A:</span>
                                <span id="modal_new_rep_name" class="font-semibold text-emerald-600 dark:text-emerald-400">--</span>
                                <span id="modal_new_rep_status" class="hidden inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"></span>
                            </div>
                        </div>

                        {{-- Relationship Type Field --}}
                        <div>
                            <label for="relationship_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Tipo de relación <span class="text-red-500">*</span>
                            </label>
                            <select name="relationship_type" id="relationship_type" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200">
                                <option value="">-- Seleccione --</option>
                                @foreach($relationshipTypes as $type)
                                    @if($type !== \App\Enums\RelationshipType::SelfRepresented)
                                        <option value="{{ $type->value }}">{{ $type->value }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        {{-- Reason Field --}}
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Motivo del cambio <span class="text-red-500">*</span>
                            </label>
                            <textarea name="reason" id="reason" rows="3" required maxlength="500"
                                      placeholder="Ej: El representante actual falleció, cambio de custodia legal, la madre/padre recupera la tutela..."
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 placeholder:text-gray-400/50 transition-all duration-200 resize-none"></textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Máximo 500 caracteres. Este registro quedará almacenado en el sistema.</p>
                        </div>

                        {{-- Info Alert --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                            <div class="flex gap-3">
                                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    El estado de ambos representantes se actualizará automáticamente según sus estudiantes activos.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-b-2xl flex gap-3">
                        <button type="button" onclick="closeReassignModal()"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-colors">
                            Confirmar Cambio
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Self-Represented Modal --}}
        <div id="selfRepresentedModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg border border-gray-200 dark:border-gray-700">
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-violet-100 dark:bg-violet-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Convertir a Auto-representante</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">El estudiante será su propio representante</p>
                        </div>
                    </div>
                </div>

                {{-- Modal Body --}}
                <form id="selfRepresentedForm" action="{{ route('students.convert-to-self-represented', $student) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="p-6 space-y-4">
                        {{-- Summary --}}
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 space-y-3">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-32">Estudiante:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $student->full_name }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-32">Edad:</span>
                                <span class="text-gray-700 dark:text-gray-200">{{ $student->age }} años</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-32">Rep. actual:</span>
                                <span class="text-gray-700 dark:text-gray-200">{{ $student->representative?->full_name ?? 'Sin representante' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-32">Nueva relación:</span>
                                <span class="font-semibold text-violet-600 dark:text-violet-400">Auto-representante</span>
                            </div>
                        </div>

                        {{-- Reason Field (optional) --}}
                        <div>
                            <label for="self_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Motivo <span class="text-gray-400">(opcional)</span>
                            </label>
                            <textarea name="reason" id="self_reason" rows="2" maxlength="500"
                                      placeholder="Ej: El estudiante alcanzó la mayoría de edad..."
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 placeholder:text-gray-400/50 transition-all duration-200 resize-none"></textarea>
                        </div>

                        {{-- Info Alert --}}
                        <div class="bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800 rounded-xl p-4">
                            <div class="flex gap-3">
                                <svg class="w-5 h-5 text-violet-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="text-sm text-violet-700 dark:text-violet-300">
                                    <p>Esta acción:</p>
                                    <ul class="list-disc list-inside mt-1 space-y-1">
                                        <li>Creará un registro de representante para el estudiante si no existe</li>
                                        <li>Actualizará el estado del representante anterior automáticamente</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-b-2xl flex gap-3">
                        <button type="button" onclick="closeSelfRepresentedModal()"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-violet-600 to-purple-600 rounded-xl hover:from-violet-700 hover:to-purple-700 transition-colors">
                            Confirmar Conversión
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Reassign Modal
            const reassignModal = document.getElementById('reassignModal');
            const modalRepId = document.getElementById('modal_representative_id');
            const modalNewRepName = document.getElementById('modal_new_rep_name');
            const modalNewRepStatus = document.getElementById('modal_new_rep_status');
            const reasonField = document.getElementById('reason');
            const relationshipField = document.getElementById('relationship_type');

            function openReassignModal(repId, repName, repEmail, isActive) {
                modalRepId.value = repId;
                modalNewRepName.textContent = repName;
                
                modalNewRepStatus.classList.remove('hidden', 'bg-green-100', 'dark:bg-green-900/30', 'text-green-700', 'dark:text-green-300', 'bg-gray-100', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
                if (isActive) {
                    modalNewRepStatus.innerHTML = '<span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>Activo';
                    modalNewRepStatus.classList.add('bg-green-100', 'dark:bg-green-900/30', 'text-green-700', 'dark:text-green-300');
                } else {
                    modalNewRepStatus.innerHTML = '<span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></span>Inactivo';
                    modalNewRepStatus.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
                }
                modalNewRepStatus.classList.remove('hidden');
                
                reassignModal.classList.remove('hidden');
                relationshipField.focus();
            }

            function closeReassignModal() {
                reassignModal.classList.add('hidden');
                reasonField.value = '';
                relationshipField.value = '';
            }

            reassignModal.addEventListener('click', function(e) {
                if (e.target === reassignModal) {
                    closeReassignModal();
                }
            });

            // Self-Represented Modal
            const selfRepModal = document.getElementById('selfRepresentedModal');
            const selfReasonField = document.getElementById('self_reason');

            function openSelfRepresentedModal() {
                selfRepModal.classList.remove('hidden');
            }

            function closeSelfRepresentedModal() {
                selfRepModal.classList.add('hidden');
                if (selfReasonField) selfReasonField.value = '';
            }

            selfRepModal.addEventListener('click', function(e) {
                if (e.target === selfRepModal) {
                    closeSelfRepresentedModal();
                }
            });

            // Global escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeReassignModal();
                    closeSelfRepresentedModal();
                }
            });
        </script>
    @endif
</x-app-layout>