<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-violet-600 via-violet-600 to-purple-700 dark:from-violet-800 dark:via-violet-800 dark:to-purple-900 rounded-2xl shadow-xl mb-8">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <a href="{{ route('dashboard') }}" class="text-violet-200 hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                </a>
                                <span class="text-violet-200">/</span>
                                <span class="text-violet-100">Académico</span>
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Períodos Académicos</h1>
                            <p class="mt-2 text-violet-100">Gestiona los ciclos académicos de la institución</p>
                        </div>
                        <button type="button" onclick="openModal('academicPeriodModal')"
                                class="inline-flex items-center gap-2 px-5 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white font-semibold rounded-xl border border-white/20 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nuevo Período
                        </button>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 dark:border-gray-700/50 overflow-hidden">

                {{-- Search & Filters --}}
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <form method="GET" action="{{ route('academic-periods.index') }}" class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="search" placeholder="Buscar por nombre o notas..." value="{{ request('search') }}"
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 placeholder:text-gray-400/50"
                                   autocomplete="off">
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <select name="status"
                                    class="px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                <option value="">Todos los estados</option>
                                <option value="Activo" {{ request('status') === 'Activo' ? 'selected' : '' }}>Activo</option>
                                <option value="Inactivo" {{ request('status') === 'Inactivo' ? 'selected' : '' }}>Cerrado</option>
                            </select>

                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-violet-500/25 hover:shadow-violet-500/40 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Período</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Fechas</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Notas</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($academicPeriods as $academicPeriod)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-500 rounded-lg flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $academicPeriod->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $academicPeriod->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($academicPeriod->is_promotable)
                                            <div class="flex items-center gap-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-300">
                                                    Período Académico
                                                </span>
                                                @if ($academicPeriod->is_transferable)
                                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300" title="Permite transferencias">
                                                        T
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                                                Curso
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $academicPeriod->start_date->format('d/m/Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            al {{ $academicPeriod->end_date->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                            {{ $academicPeriod->notes ?? '-' }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($academicPeriod->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                                                Cerrado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="relative inline-block">
                                            <button data-dropdown-academic-period="{{ $academicPeriod->id }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/20 rounded-lg transition-colors">
                                                <span>Acciones</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>

                                            {{-- Dropdown template --}}
                                            <div id="dropdown-template-{{ $academicPeriod->id }}" class="hidden">
                                                <div class="py-2">
                                                    <a href="{{ route('academic-periods.show', $academicPeriod) }}"
                                                       class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-violet-900/20 hover:text-violet-700 dark:hover:text-violet-300 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Ver Detalles
                                                    </a>
                                                    <button type="button"
                                                            data-id="{{ $academicPeriod->id }}"
                                                            data-name="{{ e($academicPeriod->name) }}"
                                                            data-notes="{{ e($academicPeriod->notes) }}"
                                                            data-start-date="{{ $academicPeriod->start_date->format('Y-m-d') }}"
                                                            data-end-date="{{ $academicPeriod->end_date->format('Y-m-d') }}"
                                                            data-is-promotable="{{ $academicPeriod->is_promotable ? '1' : '0' }}"
                                                            data-is-transferable="{{ $academicPeriod->is_transferable ? '1' : '0' }}"
                                                            data-min-grade="{{ $academicPeriod->min_grade }}"
                                                            data-passing-grade="{{ $academicPeriod->passing_grade }}"
                                                            data-max-grade="{{ $academicPeriod->max_grade }}"
                                                            data-sections-count="{{ $academicPeriod->sections_count }}"
                                                            class="edit-btn flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Editar
                                                    </button>
                                                    <button type="button"
                                                            onclick="openDeleteModal({{ $academicPeriod->id }}, '{{ e($academicPeriod->name) }}')"
                                                            class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400">
                                                @if (request()->filled('search'))
                                                    No se encontraron períodos con los criterios de búsqueda
                                                @else
                                                    No hay períodos académicos registrados
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($academicPeriods->count() > 0)
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $academicPeriods->appends(request()->except('page'))->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>

    {{-- Modal CREAR --}}
    <div id="academicPeriodModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg my-8 border-t-4 border-violet-500 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-violet-100 dark:bg-violet-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Nuevo Período Académico</h2>
                </div>
            </div>

            <form action="{{ route('academic-periods.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">

                    {{-- Advertencia de campos inmutables --}}
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                <strong>Importante:</strong> Las fechas, escala de calificaciones y configuración de promoción/transferencia no podrán ser modificadas después de crear el período.
                            </p>
                        </div>
                    </div>

                    {{-- Nombre --}}
                    <div class="space-y-1">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="off"
                               class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 placeholder:text-gray-400/50 transition-all duration-200"
                               placeholder="Ej: 2024-2025">
                        @error('name')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notas --}}
                    <div class="space-y-1">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Notas
                        </label>
                        <textarea id="notes" name="notes" rows="2"
                                  class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 placeholder:text-gray-400/50 transition-all duration-200 resize-none"
                                  placeholder="Notas opcionales">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fechas --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Fecha inicio <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required
                                   min="{{ now()->format('Y-m-d') }}"
                                   max="{{ now()->addYears(4)->format('Y-m-d') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 dark:[color-scheme:dark]">
                            @error('start_date')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Fecha fin <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required
                                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 dark:[color-scheme:dark]">
                            @error('end_date')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Escala de Calificaciones --}}
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span class="text-sm font-medium text-amber-800 dark:text-amber-200">Escala de Calificaciones</span>
                            <span class="text-xs text-amber-600 dark:text-amber-400">(opcional)</span>
                        </div>
                        <p class="text-xs text-amber-700 dark:text-amber-300 mb-3">
                            Si no se especifica, se usará la escala por defecto: Mín: 1, Aprob: 60, Máx: 100
                        </p>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="space-y-1">
                                <label for="min_grade" class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Nota Mínima
                                </label>
                                <input type="number" id="min_grade" name="min_grade" 
                                       value="{{ old('min_grade') }}" 
                                       step="0.01" min="0" max="1000"
                                       placeholder="1"
                                       class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200">
                                @error('min_grade')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-1">
                                <label for="passing_grade" class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Nota Aprobación
                                </label>
                                <input type="number" id="passing_grade" name="passing_grade" 
                                       value="{{ old('passing_grade') }}" 
                                       step="0.01" min="0" max="1000"
                                       placeholder="60"
                                       class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200">
                                @error('passing_grade')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-1">
                                <label for="max_grade" class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Nota Máxima
                                </label>
                                <input type="number" id="max_grade" name="max_grade" 
                                       value="{{ old('max_grade') }}" 
                                       step="0.01" min="0" max="1000"
                                       placeholder="100"
                                       class="block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200">
                                @error('max_grade')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Checkbox is_promotable --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="hidden" name="is_promotable" value="0">
                            <input type="checkbox" id="is_promotable" name="is_promotable" value="1"
                                   {{ old('is_promotable', '1') == '1' ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 dark:border-gray-600 text-violet-600 shadow-sm focus:ring-violet-500 dark:bg-gray-800">
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Permite promoción</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Marcar si es un Período Académico. Desmarcar si es un Curso donde los estudiantes no se promueven.
                                </p>
                            </div>
                        </label>

                        {{-- Checkbox is_transferable (condicional) --}}
                        <div id="is_transferable_container" class="ml-6 pt-2 border-t border-gray-200 dark:border-gray-700" style="{{ old('is_promotable', '1') == '1' ? '' : 'display: none;' }}">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="hidden" name="is_transferable" value="0">
                                <input type="checkbox" id="is_transferable" name="is_transferable" value="1"
                                       {{ old('is_transferable', '1') == '1' ? 'checked' : '' }}
                                       class="mt-1 rounded border-gray-300 dark:border-gray-600 text-green-600 shadow-sm focus:ring-green-500 dark:bg-gray-800">
                                <div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Permite transferencia</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        Permite que estudiantes se transfieran a otra institución.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                    <button type="button" onclick="closeModal('academicPeriodModal')"
                            class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-violet-500/25 transition-all duration-300">
                        Crear Período
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal EDITAR --}}
    <div id="editAcademicPeriodModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg my-8 border-t-4 border-amber-500 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Editar Período Académico</h2>
                </div>
            </div>

            <form id="editAcademicPeriodForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">

                    {{-- Aviso condicional (se muestra/oculta por JS) --}}
                    <div id="edit-locked-notice" class="hidden p-3 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                Este período tiene secciones asociadas. Solo se pueden modificar el nombre y las notas.
                            </p>
                        </div>
                    </div>

                    {{-- Nombre (siempre editable) --}}
                    <div class="space-y-1">
                        <label for="edit-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="edit-name" name="name" required autocomplete="off"
                               class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200">
                        @error('name')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notas (siempre editable) --}}
                    <div class="space-y-1">
                        <label for="edit-notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Notas
                        </label>
                        <textarea id="edit-notes" name="notes" rows="2"
                                  class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 resize-none"></textarea>
                        @error('notes')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fechas (condicional) --}}
                    <div id="edit-dates-container" class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="edit-start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Fecha inicio <span class="text-red-500 edit-required-mark">*</span>
                                <svg class="edit-lock-icon hidden inline w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </label>
                            <input type="date" id="edit-start_date" name="start_date"
                                   class="edit-sensitive-field block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 dark:[color-scheme:dark]">
                            @error('start_date')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <label for="edit-end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Fecha fin <span class="text-red-500 edit-required-mark">*</span>
                                <svg class="edit-lock-icon hidden inline w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </label>
                            <input type="date" id="edit-end_date" name="end_date"
                                   class="edit-sensitive-field block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 dark:[color-scheme:dark]">
                            @error('end_date')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Escala de Calificaciones (condicional) --}}
                    <div id="edit-grades-container" class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span class="text-sm font-medium text-amber-800 dark:text-amber-200">Escala de Calificaciones</span>
                            <svg class="edit-lock-icon hidden w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="space-y-1">
                                <label for="edit-min_grade" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Nota Mínima</label>
                                <input type="number" id="edit-min_grade" name="min_grade" step="0.01" min="0" max="1000"
                                       class="edit-sensitive-field block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200">
                                @error('min_grade')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-1">
                                <label for="edit-passing_grade" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Nota Aprobación</label>
                                <input type="number" id="edit-passing_grade" name="passing_grade" step="0.01" min="0" max="1000"
                                       class="edit-sensitive-field block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200">
                                @error('passing_grade')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-1">
                                <label for="edit-max_grade" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Nota Máxima</label>
                                <input type="number" id="edit-max_grade" name="max_grade" step="0.01" min="0" max="1000"
                                       class="edit-sensitive-field block w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200">
                                @error('max_grade')
                                    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Checkboxes (condicional) --}}
                    <div id="edit-checkboxes-container" class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer edit-checkbox-label">
                            <input type="hidden" name="is_promotable" value="0" class="edit-sensitive-hidden">
                            <input type="checkbox" id="edit-is_promotable" name="is_promotable" value="1"
                                   class="edit-sensitive-field mt-1 rounded border-gray-300 dark:border-gray-600 text-violet-600 shadow-sm focus:ring-violet-500 dark:bg-gray-800">
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Permite promoción</span>
                                <svg class="edit-lock-icon hidden inline w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Marcar si es un Período Académico.
                                </p>
                            </div>
                        </label>

                        <div id="edit-is_transferable_container" class="ml-6 pt-2 border-t border-gray-200 dark:border-gray-700">
                            <label class="flex items-start gap-3 cursor-pointer edit-checkbox-label">
                                <input type="hidden" name="is_transferable" value="0" class="edit-sensitive-hidden">
                                <input type="checkbox" id="edit-is_transferable" name="is_transferable" value="1"
                                       class="edit-sensitive-field mt-1 rounded border-gray-300 dark:border-gray-600 text-green-600 shadow-sm focus:ring-green-500 dark:bg-gray-800">
                                <div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Permite transferencia</span>
                                    <svg class="edit-lock-icon hidden inline w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        Permite que estudiantes se transfieran a otra institución.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                    <button type="button" onclick="closeModal('editAcademicPeriodModal')"
                            class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-medium rounded-xl shadow-lg shadow-amber-500/25 transition-all duration-300">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal ELIMINAR --}}
    <div id="deleteAcademicPeriodModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm my-8 border-t-4 border-red-500 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Confirmar Eliminación</h2>
                </div>
            </div>

            <div class="p-6">
                <p class="text-gray-600 dark:text-gray-300">
                    ¿Estás seguro de eliminar el período "<span id="deletePeriodName" class="font-semibold text-gray-900 dark:text-white"></span>"?
                </p>
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                    Esta acción no se puede deshacer.
                </p>
            </div>

            <form id="deleteAcademicPeriodForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModal('deleteAcademicPeriodModal')"
                            class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-xl shadow-lg shadow-red-500/25 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        // Modal functions
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openDeleteModal(id, name) {
            document.getElementById('deleteAcademicPeriodForm').action = `/academic-periods/${id}`;
            document.getElementById('deletePeriodName').textContent = name;
            document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
            openModal('deleteAcademicPeriodModal');
        }

        document.addEventListener("DOMContentLoaded", () => {
            const closeAllDropdowns = () => {
                document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
            };

            // Toggle is_transferable visibility based on is_promotable (Create modal only)
            const isPromotable = document.getElementById('is_promotable');
            const transferableContainer = document.getElementById('is_transferable_container');
            
            if (isPromotable && transferableContainer) {
                isPromotable.addEventListener('change', function() {
                    transferableContainer.style.display = this.checked ? '' : 'none';
                    if (!this.checked) {
                        document.getElementById('is_transferable').checked = false;
                    }
                });
            }

            // Dropdown handling
            document.querySelectorAll("[data-dropdown-academic-period]").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    const id = btn.dataset.dropdownAcademicPeriod;
                    let existing = document.getElementById("dropdownMenu-" + id);

                    if (existing && !existing.classList.contains("hidden")) {
                        existing.remove();
                        return;
                    }

                    closeAllDropdowns();

                    const tpl = document.getElementById("dropdown-template-" + id);
                    const clone = tpl.cloneNode(true);
                    clone.id = "dropdownMenu-" + id;
                    clone.classList.remove("hidden");
                    clone.classList.add(
                        "dropdown-clone", "fixed", "z-50", "w-48",
                        "bg-white", "dark:bg-gray-800", "border", "border-gray-200",
                        "dark:border-gray-700", "rounded-xl", "shadow-xl"
                    );

                    const rect = btn.getBoundingClientRect();
                    const menuHeight = 160;
                    const espacioAbajo = window.innerHeight - rect.bottom;
                    const espacioArriba = rect.top;

                    if (espacioAbajo >= menuHeight) {
                        clone.style.top = (rect.bottom + 4) + "px";
                    } else if (espacioArriba >= menuHeight) {
                        clone.style.top = (rect.top - menuHeight - 4) + "px";
                    } else {
                        clone.style.top = (rect.bottom + 4) + "px";
                        clone.style.maxHeight = espacioAbajo + "px";
                        clone.style.overflowY = "auto";
                    }

                    clone.style.left = Math.min(rect.left, window.innerWidth - 200) + "px";
                    document.body.appendChild(clone);
                });
            });

            // Close dropdown on outside click
            document.addEventListener("click", (e) => {
                if (!e.target.closest("[data-dropdown-academic-period]") && !e.target.closest(".dropdown-clone")) {
                    closeAllDropdowns();
                }
            });

            // Close dropdown on scroll/resize/escape
            window.addEventListener("scroll", closeAllDropdowns, true);
            window.addEventListener("resize", closeAllDropdowns);
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") {
                    closeAllDropdowns();
                    closeModal('academicPeriodModal');
                    closeModal('editAcademicPeriodModal');
                    closeModal('deleteAcademicPeriodModal');
                }
            });

            // Close modals on backdrop click
            ['academicPeriodModal', 'editAcademicPeriodModal', 'deleteAcademicPeriodModal'].forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('click', (e) => {
                        if (e.target === modal) {
                            closeModal(modalId);
                        }
                    });
                }
            });

            // Edit button handler - habilitar/deshabilitar campos según sections_count
            document.addEventListener("click", (e) => {
                const btn = e.target.closest(".edit-btn");
                if (btn) {
                    const id = btn.getAttribute('data-id');
                    const name = btn.getAttribute('data-name') || '';
                    const notes = btn.getAttribute('data-notes') || '';
                    const startDate = btn.getAttribute('data-start-date') || '';
                    const endDate = btn.getAttribute('data-end-date') || '';
                    const isPromotable = btn.getAttribute('data-is-promotable') === '1';
                    const isTransferable = btn.getAttribute('data-is-transferable') === '1';
                    const minGrade = btn.getAttribute('data-min-grade') || '';
                    const passingGrade = btn.getAttribute('data-passing-grade') || '';
                    const maxGrade = btn.getAttribute('data-max-grade') || '';
                    const sectionsCount = parseInt(btn.getAttribute('data-sections-count') || '0');

                    const hasSections = sectionsCount > 0;

                    closeAllDropdowns();

                    const form = document.getElementById("editAcademicPeriodForm");
                    form.action = `/academic-periods/${id}`;

                    // Campos siempre editables
                    document.getElementById("edit-name").value = name;
                    document.getElementById("edit-notes").value = notes;

                    // Campos sensibles - establecer valores
                    document.getElementById("edit-start_date").value = startDate;
                    document.getElementById("edit-end_date").value = endDate;
                    document.getElementById("edit-is_promotable").checked = isPromotable;
                    document.getElementById("edit-is_transferable").checked = isTransferable;
                    document.getElementById("edit-min_grade").value = minGrade;
                    document.getElementById("edit-passing_grade").value = passingGrade;
                    document.getElementById("edit-max_grade").value = maxGrade;

                    // Obtener elementos para habilitar/deshabilitar
                    const sensitiveFields = document.querySelectorAll('.edit-sensitive-field');
                    const sensitiveHiddens = document.querySelectorAll('.edit-sensitive-hidden');
                    const lockIcons = document.querySelectorAll('.edit-lock-icon');
                    const requiredMarks = document.querySelectorAll('.edit-required-mark');
                    const lockedNotice = document.getElementById('edit-locked-notice');
                    const datesContainer = document.getElementById('edit-dates-container');
                    const gradesContainer = document.getElementById('edit-grades-container');
                    const checkboxesContainer = document.getElementById('edit-checkboxes-container');
                    const checkboxLabels = document.querySelectorAll('.edit-checkbox-label');

                    if (hasSections) {
                        // BLOQUEADO: tiene secciones
                        sensitiveFields.forEach(field => {
                            field.disabled = true;
                            field.classList.add('cursor-not-allowed', 'bg-gray-100', 'dark:bg-gray-800');
                        });
                        sensitiveHiddens.forEach(hidden => hidden.disabled = true);
                        lockIcons.forEach(icon => icon.classList.remove('hidden'));
                        requiredMarks.forEach(mark => mark.classList.add('hidden'));
                        lockedNotice.classList.remove('hidden');
                        datesContainer.classList.add('opacity-60');
                        gradesContainer.classList.add('opacity-60');
                        checkboxesContainer.classList.add('opacity-60');
                        checkboxLabels.forEach(label => label.classList.remove('cursor-pointer'));
                        
                        // Quitar min de fecha
                        document.getElementById("edit-start_date").removeAttribute('min');
                    } else {
                        // DESBLOQUEADO: sin secciones
                        sensitiveFields.forEach(field => {
                            field.disabled = false;
                            field.classList.remove('cursor-not-allowed', 'bg-gray-100', 'dark:bg-gray-800');
                        });
                        sensitiveHiddens.forEach(hidden => hidden.disabled = false);
                        lockIcons.forEach(icon => icon.classList.add('hidden'));
                        requiredMarks.forEach(mark => mark.classList.remove('hidden'));
                        lockedNotice.classList.add('hidden');
                        datesContainer.classList.remove('opacity-60');
                        gradesContainer.classList.remove('opacity-60');
                        checkboxesContainer.classList.remove('opacity-60');
                        checkboxLabels.forEach(label => label.classList.add('cursor-pointer'));

                        // Establecer min de fecha = fecha original (no puede ir hacia atrás)
                        document.getElementById("edit-start_date").setAttribute('min', startDate);

                        // Configurar listener para is_promotable (solo si desbloqueado)
                        const editIsPromotable = document.getElementById('edit-is_promotable');
                        const editTransferableContainer = document.getElementById('edit-is_transferable_container');
                        
                        // Remover listener anterior si existe
                        editIsPromotable.onchange = function() {
                            editTransferableContainer.style.display = this.checked ? '' : 'none';
                            if (!this.checked) {
                                document.getElementById('edit-is_transferable').checked = false;
                            }
                        };
                    }

                    // Mostrar/ocultar transferable según promotable
                    document.getElementById('edit-is_transferable_container').style.display = isPromotable ? '' : 'none';

                    openModal("editAcademicPeriodModal");
                }
            });

            // Reopen modals on validation errors
            @if ($errors->any() && session('form') === 'create')
                openModal('academicPeriodModal');
            @endif

            @if ($errors->any() && session('form') === 'edit')
                @php $editId = session('edit_id'); @endphp
                const editForm = document.getElementById('editAcademicPeriodForm');
                if (editForm && '{{ $editId }}') {
                    editForm.action = "/academic-periods/{{ $editId }}";
                    document.getElementById('edit-name').value = @json(old('name'));
                    document.getElementById('edit-notes').value = @json(old('notes'));
                    openModal('editAcademicPeriodModal');
                }
            @endif
        });
    </script>
</x-app-layout>