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
                    <a href="{{ route('sections.index') }}" class="inline-flex items-center gap-2 text-violet-200 hover:text-white transition-colors mb-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Ir a Secciones
                    </a>

                    {{-- Section info --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl border border-white/30">
                            <span class="text-2xl font-bold text-white">{{ strtoupper(substr($section->name, 0, 2)) }}</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $section->name }}</h1>
                                @if($section->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-100 border border-green-400/30">
                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-500/20 text-gray-200 border border-gray-400/30">
                                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-violet-100">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $section->academicPeriod->name }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Capacidad: {{ $section->capacity }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section Details Card --}}
            @if($section->description)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Descripción</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $section->description }}</p>
                </div>
            @endif

            {{-- Subject/Teacher Assignments --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="text-sm font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Materias y Profesores Asignados
                    </h3>
                    <button type="button" onclick="openModal('assignModal')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-violet-500/25 transition-all duration-300 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Agregar Materia/Profesor
                    </button>
                </div>

                {{-- Assignments Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Materia</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Profesor</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Principal</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($section->sectionSubjectTeachers as $sst)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-violet-500 rounded-lg flex items-center justify-center text-white font-semibold text-xs shadow-md">
                                                {{ strtoupper(substr($sst->subject->name, 0, 2)) }}
                                            </div>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $sst->subject->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full flex items-center justify-center text-white font-semibold text-xs shadow-md">
                                                {{ strtoupper(substr($sst->teacher->user->name, 0, 1) . substr($sst->teacher->user->last_name, 0, 1)) }}
                                            </div>
                                            <span class="text-gray-700 dark:text-gray-300">{{ $sst->teacher->user->full_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($sst->is_primary)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Sí
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($sst->status === 'activo')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                                Activo
                                            </span>
                                        @elseif($sst->status === 'suplente')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300">
                                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1.5"></span>
                                                Suplente
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="relative inline-block">
                                            <button data-dropdown-sst="{{ $sst->id }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/20 rounded-lg transition-colors">
                                                <span>Acciones</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>

                                            {{-- Dropdown template --}}
                                            <div id="dropdown-template-{{ $sst->id }}" class="hidden">
                                                <div class="py-2">
                                                    <button type="button"
                                                            data-id="{{ $sst->id }}"
                                                            data-subject-id="{{ $sst->subject_id }}"
                                                            data-subject-name="{{ e($sst->subject->name) }}"
                                                            data-teacher-id="{{ $sst->teacher_id }}"
                                                            data-teacher-name="{{ e($sst->teacher->user->full_name) }}"
                                                            data-is-primary="{{ $sst->is_primary ? '1' : '0' }}"
                                                            data-status="{{ $sst->status }}"
                                                            class="edit-sst-btn flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Editar
                                                    </button>
                                                    <form method="POST" action="{{ route('section-subject-teacher.destroy', $sst) }}"
                                                          onsubmit="return confirm('¿Seguro que deseas eliminar esta asignación?');">
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
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 mb-4">No hay materias/profesores asignados a esta sección</p>
                                            <button type="button" onclick="openModal('assignModal')"
                                                    class="inline-flex items-center gap-2 px-4 py-2 text-violet-600 dark:text-violet-400 font-medium hover:bg-violet-50 dark:hover:bg-violet-900/20 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Agregar primera asignación
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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

    {{-- Modal CREAR Asignación --}}
    <div id="assignModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md my-8 border-t-4 border-violet-500 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-violet-100 dark:bg-violet-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Asignar Materia/Profesor</h2>
                </div>
            </div>

            <form action="{{ route('section-subject-teacher.store') }}" method="POST">
                @csrf
                <input type="hidden" name="section_id" value="{{ $section->id }}">
                <div class="p-6 space-y-4">
                    {{-- Materia --}}
                    <div class="space-y-1">
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Materia <span class="text-red-500">*</span>
                        </label>
                        <select id="subject_id" name="subject_id" required
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                            <option value="">Seleccione una materia</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Profesor (dinámico) --}}
                    <div class="space-y-1">
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Profesor <span class="text-red-500">*</span>
                        </label>
                        <select id="teacher_id" name="teacher_id" required disabled
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed">
                            <option value="">Primero selecciona una materia</option>
                        </select>
                        @error('teacher_id')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Es principal --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_primary" value="1" {{ old('is_primary', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 dark:border-gray-600 text-violet-600 shadow-sm focus:ring-violet-500 dark:bg-gray-800">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">¿Es profesor principal?</span>
                        </label>
                        @error('is_primary')
                            <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div class="space-y-1">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                            <option value="activo" {{ old('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="suplente" {{ old('status') == 'suplente' ? 'selected' : '' }}>Suplente</option>
                            <option value="inactivo" {{ old('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('status')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                    <button type="button" onclick="closeModal('assignModal')"
                            class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-violet-500/25 transition-all duration-300">
                        Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal EDITAR Asignación --}}
    <div id="editSSTModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md my-8 border-t-4 border-amber-500 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Editar Asignación</h2>
                </div>
            </div>

            <form id="editSSTForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    {{-- Materia (readonly) --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Materia</label>
                        <input type="text" id="edit-subject-name" disabled
                               class="block w-full px-4 py-3 border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 rounded-xl cursor-not-allowed">
                        <p class="text-xs text-gray-500 dark:text-gray-400">No se puede cambiar la materia. Elimina y crea nueva asignación.</p>
                    </div>

                    {{-- Profesor (readonly) --}}
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profesor</label>
                        <input type="text" id="edit-teacher-name" disabled
                               class="block w-full px-4 py-3 border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 rounded-xl cursor-not-allowed">
                        <p class="text-xs text-gray-500 dark:text-gray-400">No se puede cambiar el profesor. Elimina y crea nueva asignación.</p>
                    </div>

                    {{-- Es principal --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="edit-is_primary" name="is_primary" value="1"
                                   class="rounded border-gray-300 dark:border-gray-600 text-amber-600 shadow-sm focus:ring-amber-500 dark:bg-gray-800">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">¿Es profesor principal?</span>
                        </label>
                    </div>

                    {{-- Estado --}}
                    <div class="space-y-1">
                        <label for="edit-status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select id="edit-status" name="status" required
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200">
                            <option value="activo">Activo</option>
                            <option value="suplente">Suplente</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                    <button type="button" onclick="closeModal('editSSTModal')"
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
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const closeAllDropdowns = () => {
                document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
            };

            // Teacher data by subject (from backend)
            const teachersBySubject = @json($teachersBySubject);

            // Subject select
            const subjectSelect = document.getElementById('subject_id');
            const teacherSelect = document.getElementById('teacher_id');

            // Load teachers based on selected subject
            subjectSelect.addEventListener('change', function() {
                const subjectId = parseInt(this.value);
                teacherSelect.innerHTML = '<option value="">Seleccione un profesor</option>';
                teacherSelect.disabled = !subjectId;

                if (subjectId && teachersBySubject[subjectId]) {
                    teachersBySubject[subjectId].forEach(teacher => {
                        const option = document.createElement('option');
                        option.value = teacher.id;
                        option.textContent = teacher.name;
                        teacherSelect.appendChild(option);
                    });
                }
            });

            // Reload teachers if old values exist
            @if(old('subject_id'))
                subjectSelect.dispatchEvent(new Event('change'));
                teacherSelect.value = '{{ old('teacher_id') }}';
            @endif

            // Dropdown handling
            document.querySelectorAll("[data-dropdown-sst]").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    const id = btn.dataset.dropdownSst;
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
                        "dropdown-clone", "fixed", "z-50", "w-44",
                        "bg-white", "dark:bg-gray-800", "border", "border-gray-200",
                        "dark:border-gray-700", "rounded-xl", "shadow-xl"
                    );

                    const rect = btn.getBoundingClientRect();
                    const menuHeight = 100;
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

                    clone.style.left = Math.min(rect.left, window.innerWidth - 180) + "px";
                    document.body.appendChild(clone);
                });
            });

            // Close dropdown on outside click
            document.addEventListener("click", (e) => {
                if (!e.target.closest("[data-dropdown-sst]") && !e.target.closest(".dropdown-clone")) {
                    closeAllDropdowns();
                }
            });

            // Close dropdown on scroll/resize/escape
            window.addEventListener("scroll", closeAllDropdowns, true);
            window.addEventListener("resize", closeAllDropdowns);
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") {
                    closeAllDropdowns();
                    closeModal('assignModal');
                    closeModal('editSSTModal');
                }
            });

            // Close modals on backdrop click
            ['assignModal', 'editSSTModal'].forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('click', (e) => {
                        if (e.target === modal) {
                            closeModal(modalId);
                        }
                    });
                }
            });

            // Edit SST button handler
            document.addEventListener("click", (e) => {
                const btn = e.target.closest(".edit-sst-btn");
                if (btn) {
                    const id = btn.getAttribute('data-id');
                    const subjectName = btn.getAttribute('data-subject-name') || '';
                    const teacherName = btn.getAttribute('data-teacher-name') || '';
                    const isPrimary = btn.getAttribute('data-is-primary') === '1';
                    const status = btn.getAttribute('data-status');

                    closeAllDropdowns();

                    const form = document.getElementById("editSSTForm");
                    form.action = `/section-subject-teacher/${id}`;

                    document.getElementById("edit-subject-name").value = subjectName;
                    document.getElementById("edit-teacher-name").value = teacherName;
                    document.getElementById("edit-is_primary").checked = isPrimary;
                    document.getElementById("edit-status").value = status;

                    openModal("editSSTModal");
                }
            });

            // Reopen modal if validation errors
            @if($errors->any())
                openModal('assignModal');
            @endif
        });
    </script>
</x-app-layout>