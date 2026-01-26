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
                                                            class="edit-btn flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Editar
                                                    </button>
                                                    <form method="POST" action="{{ route('academic-periods.destroy', $academicPeriod) }}"
                                                          onsubmit="return confirm('¿Estás seguro de eliminar este período? Esta acción no se puede deshacer.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Eliminar
                                                        </button>
                                                    </form>
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
                <div class="p-6 space-y-4">
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
                <div class="p-6 space-y-4">
                    {{-- Nombre --}}
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

                    {{-- Notas --}}
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

                    {{-- Fechas --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="edit-start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Fecha inicio <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="edit-start_date" name="start_date" required
                                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 dark:[color-scheme:dark]">
                            @error('start_date')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <label for="edit-end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Fecha fin <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="edit-end_date" name="end_date" required
                                   class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 dark:[color-scheme:dark]">
                            @error('end_date')
                                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Checkbox is_promotable --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="hidden" name="is_promotable" value="0">
                            <input type="checkbox" id="edit-is_promotable" name="is_promotable" value="1"
                                   class="mt-1 rounded border-gray-300 dark:border-gray-600 text-violet-600 shadow-sm focus:ring-violet-500 dark:bg-gray-800">
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Permite promoción</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Marcar si es un Período Académico.
                                </p>
                            </div>
                        </label>

                        {{-- Checkbox is_transferable (condicional) --}}
                        <div id="edit-is_transferable_container" class="ml-6 pt-2 border-t border-gray-200 dark:border-gray-700">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="hidden" name="is_transferable" value="0">
                                <input type="checkbox" id="edit-is_transferable" name="is_transferable" value="1"
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

        document.addEventListener("DOMContentLoaded", () => {
            const closeAllDropdowns = () => {
                document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
            };

            // Toggle is_transferable visibility based on is_promotable (Create modal)
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

            // Toggle is_transferable visibility based on is_promotable (Edit modal)
            const editIsPromotable = document.getElementById('edit-is_promotable');
            const editTransferableContainer = document.getElementById('edit-is_transferable_container');
            
            if (editIsPromotable && editTransferableContainer) {
                editIsPromotable.addEventListener('change', function() {
                    editTransferableContainer.style.display = this.checked ? '' : 'none';
                    if (!this.checked) {
                        document.getElementById('edit-is_transferable').checked = false;
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
                }
            });

            // Close modals on backdrop click
            ['academicPeriodModal', 'editAcademicPeriodModal'].forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('click', (e) => {
                        if (e.target === modal) {
                            closeModal(modalId);
                        }
                    });
                }
            });

            // Edit button handler
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

                    closeAllDropdowns();

                    const form = document.getElementById("editAcademicPeriodForm");
                    form.action = `/academic-periods/${id}`;

                    document.getElementById("edit-name").value = name;
                    document.getElementById("edit-notes").value = notes;
                    document.getElementById("edit-start_date").value = startDate;
                    document.getElementById("edit-end_date").value = endDate;
                    document.getElementById("edit-is_promotable").checked = isPromotable;
                    document.getElementById("edit-is_transferable").checked = isTransferable;

                    // Toggle transferable visibility
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
                    document.getElementById('edit-start_date').value = @json(old('start_date'));
                    document.getElementById('edit-end_date').value = @json(old('end_date'));
                    document.getElementById('edit-is_promotable').checked = @json(old('is_promotable')) == '1';
                    document.getElementById('edit-is_transferable').checked = @json(old('is_transferable')) == '1';
                    openModal('editAcademicPeriodModal');
                }
            @endif
        });
    </script>
</x-app-layout>