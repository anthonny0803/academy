<x-app-layout>
    @php
        $situations = \App\Enums\StudentSituation::toArray();
        $hasActiveEnrollments = $student->enrollments->where('status', 'activo')->count() > 0;
        
        $situationColors = [
            'Cursando' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
            'Pausado' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
            'Baja médica' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
            'Suspendido' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
            'Situación familiar' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
            'Sin actividad' => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
        ];
        $situation = $student->situation?->value ?? 'Sin actividad';
        $situationColor = $situationColors[$situation] ?? $situationColors['Sin actividad'];
        
        $statusColors = [
            'activo' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
            'completado' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
            'retirado' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
            'transferido' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
            'promovido' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
        ];
    @endphp

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
                    <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 text-emerald-200 hover:text-white transition-colors mb-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Ir al listado
                    </a>

                    {{-- Student info --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                        <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-xl border border-white/30">
                            {{ strtoupper(substr($student->user->name ?? 'E', 0, 1)) }}{{ strtoupper(substr($student->user->last_name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-1">
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $student->full_name }}</h1>
                            </div>
                            <p class="text-emerald-100 font-mono">{{ $student->student_code }}</p>
                            <div class="flex flex-wrap items-center gap-2 mt-3">
                                @if ($student->is_active)
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
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm">
                                    {{ $situation }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('students.edit', $student) }}"
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
                                <dd class="mt-1 text-gray-900 dark:text-white font-medium">{{ $student->full_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Correo electrónico</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $student->email ?? 'Sin correo' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Documento de identidad</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $student->document_id ?? 'Sin documento' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sexo</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $student->sex ?? 'No especificado' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de nacimiento</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">
                                    {{ $student->birth_date?->format('d/m/Y') ?? 'No registrada' }}
                                    @if($student->age)
                                        <span class="text-gray-500 dark:text-gray-400">({{ $student->age }} años)</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Representative & Relationship --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Representante y Relación
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Representante</dt>
                                <dd class="mt-1">
                                    @if($student->representative)
                                        <a href="{{ route('representatives.show', $student->representative) }}"
                                           class="inline-flex items-center text-emerald-600 dark:text-emerald-400 hover:underline group">
                                            <span class="w-2 h-2 rounded-full mr-2 {{ $student->representative->is_active ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                                            {{ $student->representative->full_name }}
                                            <svg class="w-4 h-4 ml-1 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">Sin representante asignado</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de relación</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                        {{ $student->relationship_type }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de registro</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">{{ $student->created_at->format('d/m/Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

            </div>

            {{-- Enrollments Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Inscripciones
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $student->enrollments->count() }} inscripción(es) registrada(s)</p>
                        </div>
                        <a href="{{ route('students.enrollments.create', $student) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nueva inscripción
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    @if($student->enrollments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Sección</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Período</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Fecha</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($student->enrollments as $enrollment)
                                        @php
                                            $statusColor = $statusColors[$enrollment->status] ?? $statusColors['activo'];
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white font-medium">
                                                {{ $enrollment->section->name }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                                {{ $enrollment->section->academicPeriod->name }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                                    {{ ucfirst($enrollment->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                                {{ $enrollment->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <button data-dropdown-enrollment="{{ $enrollment->id }}"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                                    Gestionar
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                                <div id="dropdown-enrollment-{{ $enrollment->id }}" class="hidden">
                                                    <ul class="py-1 text-sm">
                                                        <li>
                                                            <a href="{{ route('enrollments.show', $enrollment) }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                </svg>
                                                                Ver Detalles
                                                            </a>
                                                        </li>
                                                        @if ($enrollment->status === 'activo')
                                                            <li>
                                                                <a href="{{ route('enrollments.transfer.form', $enrollment) }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                                    </svg>
                                                                    Transferir
                                                                </a>
                                                            </li>
                                                            @if ($enrollment->section->academicPeriod->isPromotable())
                                                                <li>
                                                                    <a href="{{ route('enrollments.promote.form', $enrollment) }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                                                        </svg>
                                                                        Promover
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Sin inscripciones registradas</p>
                            <a href="{{ route('students.enrollments.create', $student) }}"
                               class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Crear primera inscripción
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Acciones Rápidas</h3>
                </div>
                <div class="p-6 flex flex-wrap gap-3">
                    <a href="{{ route('students.edit', $student) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 font-medium rounded-xl hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar Datos
                    </a>
                    <button type="button" onclick="openSituationModal('{{ $student->id }}')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 font-medium rounded-xl hover:bg-violet-200 dark:hover:bg-violet-900/50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Cambiar Situación
                    </button>
                    <a href="{{ route('students.enrollments.create', $student) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium rounded-xl hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nueva Inscripción
                    </a>
                    @if ($student->is_active && $hasActiveEnrollments)
                        <a href="{{ route('students.withdraw.form', $student) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 font-medium rounded-xl hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Retirar Estudiante
                        </a>
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

    {{-- Modal de Situación --}}
    <div id="situationModal-{{ $student->id }}" class="modal hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-2xl w-96 max-w-[90vw] border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold mb-2 text-gray-900 dark:text-white">Cambiar Situación</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Estudiante: <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $student->full_name }}</span>
            </p>
            <form action="{{ route('students.situation', $student) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label for="situation-{{ $student->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Situación</label>
                    <select name="situation" id="situation-{{ $student->id }}" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        @foreach ($situations as $situationOption)
                            <option value="{{ $situationOption }}" {{ $student->situation?->value === $situationOption ? 'selected' : '' }}>
                                {{ $situationOption }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeSituationModal('{{ $student->id }}')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-colors">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openSituationModal(studentId) {
            document.getElementById('situationModal-' + studentId).classList.remove('hidden');
            closeAllDropdowns();
        }
        
        function closeSituationModal(studentId) {
            document.getElementById('situationModal-' + studentId).classList.add('hidden');
        }
        
        function closeAllDropdowns() {
            document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
        }
        
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                e.target.classList.add('hidden');
            }
        });
        
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll("[data-dropdown-enrollment]").forEach(function(btn) {
                btn.addEventListener("click", function(e) {
                    e.stopPropagation();
                    const enrollmentId = btn.dataset.dropdownEnrollment;
                    const existingId = "dropdownEnrollmentMenu-" + enrollmentId;
                    let existing = document.getElementById(existingId);
                    if (existing) { existing.remove(); return; }
                    closeAllDropdowns();
                    const tpl = document.getElementById("dropdown-enrollment-" + enrollmentId);
                    const clone = tpl.cloneNode(true);
                    clone.id = existingId;
                    clone.classList.remove("hidden");
                    clone.classList.add("dropdown-clone", "bg-white", "dark:bg-gray-800", "border", "border-gray-200", "dark:border-gray-700", "rounded-xl", "shadow-xl");
                    clone.style.position = "fixed";
                    clone.style.zIndex = "9999";
                    clone.style.width = "10rem";
                    document.body.appendChild(clone);
                    const rect = btn.getBoundingClientRect();
                    const dropdownHeight = clone.offsetHeight;
                    const spaceBelow = window.innerHeight - rect.bottom;
                    if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
                        clone.style.top = (rect.top - dropdownHeight - 5) + "px";
                    } else {
                        clone.style.top = (rect.bottom + 5) + "px";
                    }
                    clone.style.left = Math.min(rect.left, window.innerWidth - 170) + "px";
                });
            });
            
            document.addEventListener("click", function(e) {
                if (!e.target.closest("[data-dropdown-enrollment]") && !e.target.closest(".dropdown-clone")) {
                    closeAllDropdowns();
                }
            });
            
            window.addEventListener("scroll", closeAllDropdowns, true);
            window.addEventListener("resize", closeAllDropdowns);
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeAllDropdowns();
                    document.querySelectorAll('.modal').forEach(m => m.classList.add('hidden'));
                }
            });
        });
    </script>
</x-app-layout>