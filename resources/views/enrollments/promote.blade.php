<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-green-500 via-green-500 to-emerald-600 dark:from-green-700 dark:via-green-700 dark:to-emerald-800 rounded-2xl shadow-xl mb-8">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white rounded-full"></div>
                    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-white rounded-full"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-8">
                    {{-- Back link --}}
                    <a href="{{ route('enrollments.show', $enrollment) }}" class="inline-flex items-center gap-2 text-green-100 hover:text-white transition-colors mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Ir a la inscripción
                    </a>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl border border-white/30">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">Promover Estudiante</h1>
                            <p class="mt-1 text-green-100">Ej: 1er año -> 2do año</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Current Enrollment Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="text-sm font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Inscripción Actual
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                            {{ strtoupper(substr($enrollment->student->user->name, 0, 1) . substr($enrollment->student->user->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $enrollment->student->user->name }} {{ $enrollment->student->user->last_name }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $enrollment->student->student_code }}</p>
                        </div>
                    </div>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Sección actual</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white font-medium">{{ $enrollment->section->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500 dark:text-gray-400">Período académico</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-violet-100 dark:bg-violet-900/30 text-violet-800 dark:text-violet-300">
                                    {{ $academicPeriod->name }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Info Alert --}}
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-1">Promoción</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            El estudiante avanzará a otra sección dentro del mismo período académico 
                            (<strong>{{ $academicPeriod->name }}</strong>). La inscripción actual se marcará como 
                            <strong>"promovido"</strong> y se creará una nueva inscripción activa en la sección destino.
                        </p>
                    </div>
                </div>
            </div>

            @if ($sections->count() > 0)
                {{-- Form Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-sm font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Seleccionar Nueva Sección
                        </h3>
                    </div>

                    <form method="POST" action="{{ route('enrollments.promote', $enrollment) }}">
                        @csrf
                        @method('PATCH')
                        <div class="p-6">
                            <div class="space-y-1">
                                <label for="section_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nueva Sección <span class="text-red-500">*</span>
                                </label>
                                <select id="section_id" name="section_id" required
                                        class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                                    <option value="">Selecciona la sección destino</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}" @selected(old('section_id') == $section->id)>
                                            {{ $section->name }}
                                            @if($section->description)
                                                - {{ $section->description }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('section_id')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Secciones disponibles en el período "{{ $academicPeriod->name }}".
                                </p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                            <a href="{{ route('enrollments.index') }}"
                               class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-medium rounded-xl shadow-lg shadow-green-500/25 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                Promover Estudiante
                            </button>
                        </div>
                    </form>
                </div>
            @else
                {{-- No Sections Available --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Sin secciones disponibles</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            No hay otras secciones disponibles en el período académico 
                            "<strong>{{ $academicPeriod->name }}</strong>" para realizar la promoción.
                        </p>
                        <a href="{{ route('enrollments.show', $enrollment) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 text-violet-600 dark:text-violet-400 font-medium hover:bg-violet-50 dark:hover:bg-violet-900/20 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver a detalles
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>