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
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Gestión de Asignaturas</h1>
                            <p class="mt-2 text-violet-100">Administra las materias del plan de estudios</p>
                        </div>
                        <button type="button" onclick="openModal('subjectModal')"
                                class="inline-flex items-center gap-2 px-5 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white font-semibold rounded-xl border border-white/20 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Nueva Asignatura
                        </button>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 dark:border-gray-700/50 overflow-hidden">

                {{-- Search & Filters --}}
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <form method="GET" action="{{ route('subjects.index') }}" class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="search" placeholder="Buscar por nombre o descripción..." value="{{ request('search') }}"
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 placeholder:text-gray-400/50"
                                   autocomplete="off">
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <select name="status"
                                    class="px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200">
                                <option value="">Todos los estados</option>
                                <option value="Activo" {{ request('status') === 'Activo' ? 'selected' : '' }}>Activa</option>
                                <option value="Inactivo" {{ request('status') === 'Inactivo' ? 'selected' : '' }}>Inactiva</option>
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
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Asignatura</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($subjects as $subject)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-500 rounded-lg flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                                {{ strtoupper(substr($subject->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $subject->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $subject->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                            {{ $subject->description ?? '-' }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <form method="POST" action="{{ route('subjects.toggle', $subject) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                                                               {{ $subject->is_active ? 'bg-violet-600' : 'bg-gray-300 dark:bg-gray-600' }}">
                                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow-lg transition-transform duration-300
                                                                 {{ $subject->is_active ? 'translate-x-6' : 'translate-x-1' }}">
                                                    </span>
                                                </button>
                                            </form>
                                            @if ($subject->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                    Activa
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                    Inactiva
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="relative inline-block">
                                            <button data-dropdown-subject="{{ $subject->id }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/20 rounded-lg transition-colors">
                                                <span>Acciones</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>

                                            {{-- Dropdown template --}}
                                            <div id="dropdown-template-{{ $subject->id }}" class="hidden">
                                                <div class="py-2">
                                                    <button type="button"
                                                            data-id="{{ $subject->id }}"
                                                            data-name="{{ e($subject->name) }}"
                                                            data-description="{{ e($subject->description) }}"
                                                            class="edit-btn flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 dark:hover:text-amber-300 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Editar
                                                    </button>
                                                    <button type="button"
                                                            onclick="openDeleteModal({{ $subject->id }}, '{{ e($subject->name) }}')"
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
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400">
                                                @if (request()->filled('search'))
                                                    No se encontraron asignaturas con los criterios de búsqueda
                                                @else
                                                    Ingresa un término de búsqueda para ver resultados
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
                @if ($subjects->count() > 0)
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $subjects->appends(request()->except('page'))->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>

    {{-- Modal CREAR --}}
    <div id="subjectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md my-8 border-t-4 border-violet-500 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-violet-100 dark:bg-violet-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Nueva Asignatura</h2>
                </div>
            </div>

            <form action="{{ route('subjects.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    {{-- Nombre --}}
                    <div class="space-y-1">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="off"
                               class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 placeholder:text-gray-400/50 transition-all duration-200"
                               placeholder="Ej: Matemáticas">
                        @error('name')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="space-y-1">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Descripción
                        </label>
                        <textarea id="description" name="description" rows="3"
                                  class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 placeholder:text-gray-400/50 transition-all duration-200 resize-none"
                                  placeholder="Descripción opcional de la asignatura">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                    <button type="button" onclick="closeModal('subjectModal')"
                            class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg shadow-violet-500/25 transition-all duration-300">
                        Crear Asignatura
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal EDITAR --}}
    <div id="editSubjectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md my-8 border-t-4 border-amber-500 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Editar Asignatura</h2>
                </div>
            </div>

            <form id="editSubjectForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    {{-- Nombre --}}
                    <div class="space-y-1">
                        <label for="edit-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="edit-name" name="name" required autocomplete="off"
                               class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 placeholder:text-gray-400/50 transition-all duration-200">
                        @error('name')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="space-y-1">
                        <label for="edit-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Descripción
                        </label>
                        <textarea id="edit-description" name="description" rows="3"
                                  class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 placeholder:text-gray-400/50 transition-all duration-200 resize-none"></textarea>
                        @error('description')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex gap-3 justify-end">
                    <button type="button" onclick="closeModal('editSubjectModal')"
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
    <div id="deleteSubjectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm overflow-y-auto p-4">
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
                    ¿Estás seguro de eliminar la asignatura "<span id="deleteSubjectName" class="font-semibold text-gray-900 dark:text-white"></span>"?
                </p>
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                    Esta acción no se puede deshacer.
                </p>
            </div>

            <form id="deleteSubjectForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModal('deleteSubjectModal')"
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
            document.getElementById('deleteSubjectForm').action = `/subjects/${id}`;
            document.getElementById('deleteSubjectName').textContent = name;
            document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
            openModal('deleteSubjectModal');
        }

        document.addEventListener("DOMContentLoaded", () => {
            const closeAllDropdowns = () => {
                document.querySelectorAll(".dropdown-clone").forEach(el => el.remove());
            };

            // Dropdown handling
            document.querySelectorAll("[data-dropdown-subject]").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    const id = btn.dataset.dropdownSubject;
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
                    const menuHeight = 120;
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
                if (!e.target.closest("[data-dropdown-subject]") && !e.target.closest(".dropdown-clone")) {
                    closeAllDropdowns();
                }
            });

            // Close dropdown on scroll/resize/escape
            window.addEventListener("scroll", closeAllDropdowns, true);
            window.addEventListener("resize", closeAllDropdowns);
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") {
                    closeAllDropdowns();
                    closeModal('subjectModal');
                    closeModal('editSubjectModal');
                    closeModal('deleteSubjectModal');
                }
            });

            // Close modals on backdrop click
            ['subjectModal', 'editSubjectModal', 'deleteSubjectModal'].forEach(modalId => {
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
                    const description = btn.getAttribute('data-description') || '';

                    closeAllDropdowns();

                    const form = document.getElementById("editSubjectForm");
                    form.action = `/subjects/${id}`;

                    document.getElementById("edit-name").value = name;
                    document.getElementById("edit-description").value = description;

                    openModal("editSubjectModal");
                }
            });

            // Reopen modals on validation errors
            @if ($errors->any() && session('form') === 'create')
                openModal('subjectModal');
            @endif

            @if ($errors->any() && session('form') === 'edit')
                @php $editId = session('edit_id'); @endphp
                const editForm = document.getElementById('editSubjectForm');
                if (editForm && '{{ $editId }}') {
                    editForm.action = "/subjects/{{ $editId }}";
                    document.getElementById('edit-name').value = @json(old('name'));
                    document.getElementById('edit-description').value = @json(old('description'));
                    openModal('editSubjectModal');
                }
            @endif
        });
    </script>
</x-app-layout>