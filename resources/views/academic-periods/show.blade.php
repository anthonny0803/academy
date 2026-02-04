<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header Card --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-violet-600 via-violet-600 to-purple-700 dark:from-violet-800 dark:via-violet-800 dark:to-purple-900 rounded-2xl shadow-xl">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-8">
                    {{-- Back link --}}
                    <a href="{{ route('academic-periods.index') }}" class="inline-flex items-center gap-2 text-violet-200 hover:text-white transition-colors mb-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Ir al listado
                    </a>

                    {{-- Period info --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl border border-white/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $academicPeriod->name }}</h1>
                                @if($academicPeriod->isActive())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-100 border border-green-400/30">
                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-500/20 text-gray-200 border border-gray-400/30">
                                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                        Cerrado
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-violet-100">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $academicPeriod->start_date->format('d/m/Y') }} - {{ $academicPeriod->end_date->format('d/m/Y') }}
                                </span>
                                @if ($academicPeriod->is_promotable)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white">
                                        Período Académico
                                    </span>
                                    @if ($academicPeriod->is_transferable)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-100">
                                            Transferible
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-500/20 text-purple-100">
                                        Curso
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Info & Stats Grid --}}
            <div class="grid gap-6 lg:grid-cols-3">
                {{-- General Information --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Información General
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha Inicio</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white font-medium">{{ $academicPeriod->start_date->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha Fin</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white font-medium">{{ $academicPeriod->end_date->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Permite Promoción</dt>
                                <dd class="mt-1">
                                    @if($academicPeriod->isPromotable())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Sí</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">No</span>
                                    @endif
                                </dd>
                            </div>

                            {{-- Escala de Calificaciones --}}
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Escala de Calificaciones</dt>
                                <dd>
                                    <div class="grid grid-cols-3 gap-2 text-center">
                                        <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded-lg">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Mínima</div>
                                            <div class="font-semibold text-gray-900 dark:text-white">
                                                {{ number_format($academicPeriod->min_grade, 2) }}
                                            </div>
                                        </div>
                                        <div class="bg-amber-50 dark:bg-amber-900/30 p-2 rounded-lg">
                                            <div class="text-xs text-amber-600 dark:text-amber-400">Aprobación</div>
                                            <div class="font-semibold text-amber-700 dark:text-amber-300">
                                                {{ number_format($academicPeriod->passing_grade, 2) }}
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded-lg">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Máxima</div>
                                            <div class="font-semibold text-gray-900 dark:text-white">
                                                {{ number_format($academicPeriod->max_grade, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                </dd>
                            </div>

                            @if($academicPeriod->notes)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Notas</dt>
                                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $academicPeriod->notes }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                {{-- Statistics --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Estadísticas
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl text-center border border-blue-100 dark:border-blue-800">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_sections'] }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Secciones</div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-xl text-center border border-green-100 dark:border-green-800">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['active_sections'] }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Secciones Activas</div>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-xl text-center border border-purple-100 dark:border-purple-800">
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['total_enrollments'] }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Inscripciones</div>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-xl text-center border border-amber-100 dark:border-amber-800">
                                <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['active_enrollments'] }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Inscripciones Activas</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl text-center border border-gray-200 dark:border-gray-600">
                                <div class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $stats['completed_enrollments'] }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Completadas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Close Period Section --}}
            @if($academicPeriod->isActive())
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Cerrar Período Académico
                        </h3>
                    </div>
                    <div class="p-6">
                        {{-- Sin secciones - No se puede cerrar --}}
                        @if(!$academicPeriod->hasSections())
                            <div class="p-5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <h4 class="font-semibold text-gray-700 dark:text-gray-300">No hay secciones para cerrar</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Este período no tiene secciones asociadas. No hay inscripciones que procesar.
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                            Puedes <strong>editar</strong> el período o <strong>eliminarlo</strong> si ya no lo necesitas.
                                        </p>
                                    </div>
                                </div>
                                <button type="button" disabled
                                        class="mt-4 w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 font-semibold rounded-xl cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Cerrar Período Académico
                                </button>
                            </div>
                        @else
                            {{-- Date Warning --}}
                            @if(isset($closeValidation['has_date_warning']) && $closeValidation['has_date_warning'])
                                <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl flex items-start gap-3">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                        {{ $closeValidation['issues']['date']['message'] ?? '' }}
                                    </p>
                                </div>
                            @endif

                            @if($closeValidation['can_close'])
                                {{-- Close Preview --}}
                                @if($closePreview)
                                    <div class="mb-6 p-5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                                        <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-4 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Vista previa del cierre
                                        </h4>
                                        <div class="grid grid-cols-3 gap-4 mb-4">
                                            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl text-center shadow-sm">
                                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $closePreview['passed'] }}</div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">Aprobarán</div>
                                            </div>
                                            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl text-center shadow-sm">
                                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $closePreview['failed'] }}</div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">Reprobarán</div>
                                            </div>
                                            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl text-center shadow-sm">
                                                <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $closePreview['sections_to_deactivate'] }}</div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">Secciones a desactivar</div>
                                            </div>
                                        </div>

                                        {{-- Section Details --}}
                                        @if(count($closePreview['details']) > 0)
                                            <details class="mt-4">
                                                <summary class="cursor-pointer text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                                    Ver detalle por sección
                                                </summary>
                                                <div class="mt-3 space-y-2 max-h-60 overflow-y-auto">
                                                    @foreach($closePreview['details'] as $section)
                                                        <div class="p-3 bg-white dark:bg-gray-700 rounded-lg text-sm flex items-center justify-between">
                                                            <strong class="text-gray-900 dark:text-white">{{ $section['name'] }}</strong>
                                                            <div class="flex gap-4">
                                                                <span class="text-green-600 dark:text-green-400">
                                                                    {{ count($section['passed']) }} aprobados
                                                                </span>
                                                                <span class="text-red-600 dark:text-red-400">
                                                                    {{ count($section['failed']) }} reprobados
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </details>
                                        @endif
                                    </div>

                                    {{-- Close Button - Opens Modal --}}
                                    <button type="button"
                                            onclick="openModal('closeAcademicPeriodModal')"
                                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-xl shadow-lg shadow-red-500/25 hover:shadow-red-500/40 transition-all duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Cerrar Período Académico
                                    </button>
                                @endif
                            @else
                                {{-- Cannot Close - Issues Report --}}
                                <div class="p-5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                                    <div class="flex items-start gap-3 mb-4">
                                        <svg class="w-6 h-6 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div>
                                            <h4 class="font-semibold text-red-800 dark:text-red-200">No se puede cerrar el período</h4>
                                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                                Hay inscripciones con datos incompletos. Revise el siguiente reporte:
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Detailed Report --}}
                                    @if(isset($closeValidation['issues']['sections']))
                                        <div class="space-y-4 max-h-96 overflow-y-auto">
                                            @foreach($closeValidation['issues']['sections'] as $sectionName => $subjects)
                                                <div class="bg-white dark:bg-gray-700 p-4 rounded-xl">
                                                    <h5 class="font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                                        </svg>
                                                        Sección: {{ $sectionName }}
                                                    </h5>
                                                    @foreach($subjects as $subjectName => $issues)
                                                        <div class="ml-4 mb-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                                            <h6 class="font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                                                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                                </svg>
                                                                {{ $subjectName }}
                                                            </h6>
                                                            <ul class="space-y-1 text-sm">
                                                                @foreach($issues as $issue)
                                                                    @if($issue['type'] === 'configuration')
                                                                        <li class="text-orange-600 dark:text-orange-400 flex items-center gap-2">
                                                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                                            </svg>
                                                                            {{ $issue['message'] }}
                                                                        </li>
                                                                    @elseif($issue['type'] === 'grades')
                                                                        @foreach($issue['students'] as $student)
                                                                            <li class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                                                </svg>
                                                                                {{ $student['student'] }} - 
                                                                                <span class="text-red-600 dark:text-red-400">
                                                                                    Falta: {{ implode(', ', $student['missing']) }}
                                                                                </span>
                                                                            </li>
                                                                        @endforeach
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @else
                {{-- Period Already Closed --}}
                <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Período Cerrado</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Este período académico ya ha sido cerrado y no puede modificarse.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Sections List --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-sm font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Secciones del Período
                    </h3>
                </div>
                
                @if($academicPeriod->sections->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Sección</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Activas</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Completadas</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($academicPeriod->sections as $section)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-purple-500 rounded-lg flex items-center justify-center text-white font-semibold text-xs shadow-md">
                                                    {{ strtoupper(substr($section->name, 0, 2)) }}
                                                </div>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $section->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($section->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                    Activa
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                    Inactiva
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $section->enrollments_count }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm font-medium text-amber-600 dark:text-amber-400">
                                            {{ $section->active_enrollments_count }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm font-medium text-gray-600 dark:text-gray-400">
                                            {{ $section->completed_enrollments_count }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('sections.show', $section) }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/20 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">No hay secciones registradas en este período.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Quick Links --}}
            <div class="flex justify-center">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Volver al Panel de Control
                </a>
            </div>

        </div>
    </div>

    {{-- Modal de Confirmación de Cierre --}}
    @if($academicPeriod->isActive() && $academicPeriod->hasSections() && isset($closeValidation['can_close']) && $closeValidation['can_close'] && $closePreview)
        <div id="closeAcademicPeriodModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md my-8 border-t-4 border-red-500 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Confirmar Cierre de Período</h2>
                    </div>
                </div>

                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-gray-600 dark:text-gray-300">
                            ¿Está <strong class="text-red-600 dark:text-red-400">SEGURO</strong> de cerrar el período académico 
                            "<span class="font-semibold text-gray-900 dark:text-white">{{ $academicPeriod->name }}</span>"?
                        </p>
                    </div>

                    <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl mb-4">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200 mb-3">Esta acción realizará lo siguiente:</p>
                        <ul class="text-sm text-red-700 dark:text-red-300 space-y-2">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                                Completará <strong>{{ $closePreview['passed'] + $closePreview['failed'] }}</strong> inscripciones
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                                <strong>{{ $closePreview['passed'] }}</strong> estudiantes aprobarán
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                                <strong>{{ $closePreview['failed'] }}</strong> estudiantes reprobarán
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                                Desactivará <strong>{{ $closePreview['sections_to_deactivate'] }}</strong> secciones
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                                Desactivará el período académico
                            </li>
                        </ul>
                    </div>

                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                        <p class="text-sm text-amber-700 dark:text-amber-300 flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <strong>Esta acción NO se puede deshacer.</strong>
                        </p>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                    <button type="button" onclick="closeModal('closeAcademicPeriodModal')"
                            class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </button>
                    <form action="{{ route('academic-periods.close', $academicPeriod) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-xl shadow-lg shadow-red-500/25 transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Sí, Cerrar Período
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Scripts --}}
    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Close modal on backdrop click
            const modal = document.getElementById('closeAcademicPeriodModal');
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeModal('closeAcademicPeriodModal');
                    }
                });
            }

            // Close modal on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeModal('closeAcademicPeriodModal');
                }
            });
        });
    </script>
</x-app-layout>