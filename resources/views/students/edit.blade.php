<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al listado
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Editar Estudiante</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Modifica los datos de <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $student->full_name }}</span></p>
            </div>

            {{-- Form Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <form method="POST" action="{{ route('students.update', $student) }}">
                    @csrf
                    @method('PATCH')

                    <div class="p-6 sm:p-8">

                        {{-- Section: Información Personal --}}
                        <div class="mb-8">
                            <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Información Personal
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                {{-- Nombre --}}
                                <div class="space-y-1">
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nombres @if($canEditSensitiveFields)<span class="text-red-500">*</span>@endif
                                    </label>
                                    <input type="text" id="name" name="name" 
                                           value="{{ old('name', $student->user->name) }}" 
                                           autocomplete="off"
                                           @if(!$canEditSensitiveFields) disabled @endif
                                           @if($canEditSensitiveFields) required @endif
                                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 uppercase {{ !$canEditSensitiveFields ? 'cursor-not-allowed opacity-60 bg-gray-100 dark:bg-gray-800' : '' }}">
                                    @if (!$canEditSensitiveFields)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Editable desde su perfil</p>
                                    @endif
                                    @error('name')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Apellido --}}
                                <div class="space-y-1">
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Apellidos @if($canEditSensitiveFields)<span class="text-red-500">*</span>@endif
                                    </label>
                                    <input type="text" id="last_name" name="last_name" 
                                           value="{{ old('last_name', $student->user->last_name) }}" 
                                           autocomplete="off"
                                           @if(!$canEditSensitiveFields) disabled @endif
                                           @if($canEditSensitiveFields) required @endif
                                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 uppercase {{ !$canEditSensitiveFields ? 'cursor-not-allowed opacity-60 bg-gray-100 dark:bg-gray-800' : '' }}">
                                    @if (!$canEditSensitiveFields)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Editable desde su perfil</p>
                                    @endif
                                    @error('last_name')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- DNI/NIE --}}
                                <div class="space-y-1">
                                    <label for="document_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        DNI/NIE
                                    </label>
                                    <input type="text" id="document_id" name="document_id" 
                                           value="{{ old('document_id', $student->user->document_id) }}" 
                                           autocomplete="off"
                                           pattern="[A-Za-z]{0,1}[0-9]{7,9}[A-Za-z]{1}"
                                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 uppercase">
                                    @error('document_id')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="space-y-1">
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Correo <span class="text-gray-400 text-xs">(Opcional)</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                            </svg>
                                        </div>
                                        <input type="email" id="email" name="email" 
                                               value="{{ old('email', $student->user->email) }}" 
                                               autocomplete="off"
                                               @if(!$canEditSensitiveFields) disabled @endif
                                               class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 lowercase {{ !$canEditSensitiveFields ? 'cursor-not-allowed opacity-60 bg-gray-100 dark:bg-gray-800' : '' }}">
                                    </div>
                                    @if (!$canEditSensitiveFields)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Editable desde su perfil</p>
                                    @endif
                                    @error('email')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Sexo --}}
                                <div class="space-y-1">
                                    <label for="sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Sexo @if($canEditSensitiveFields)<span class="text-red-500">*</span>@endif
                                    </label>
                                    <select id="sex" name="sex" 
                                            @if(!$canEditSensitiveFields) disabled @endif
                                            @if($canEditSensitiveFields) required @endif
                                            class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 {{ !$canEditSensitiveFields ? 'cursor-not-allowed opacity-60 bg-gray-100 dark:bg-gray-800' : '' }}">
                                        <option value="">Selecciona una opción</option>
                                        @foreach ($sexes as $sex)
                                            <option value="{{ $sex }}" {{ old('sex', $student->user->sex) === $sex ? 'selected' : '' }}>
                                                {{ ucfirst($sex) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if (!$canEditSensitiveFields)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Editable desde su perfil</p>
                                    @endif
                                    @error('sex')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Fecha de nacimiento --}}
                                <div class="space-y-1">
                                    <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Fecha de Nacimiento <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <input type="date" id="birth_date" name="birth_date" 
                                               value="{{ old('birth_date', $student->birth_date ? $student->birth_date->format('Y-m-d') : '') }}" 
                                               required autocomplete="off"
                                               class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 dark:[color-scheme:dark]">
                                    </div>
                                    @error('birth_date')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700 my-6">

                        {{-- Section: Parentesco --}}
                        <div class="mb-8">
                            <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Parentesco
                            </h3>
                            <div class="max-w-md">
                                <label for="relationship_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Relación con Representante <span class="text-red-500">*</span>
                                </label>
                                <select id="relationship_type" name="relationship_type" 
                                        @disabled($student->isSelfRepresented()) required
                                        class="mt-1 block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 {{ $student->isSelfRepresented() ? 'cursor-not-allowed opacity-60 bg-gray-100 dark:bg-gray-800' : '' }}">
                                    <option value="">Selecciona una opción</option>
                                    @foreach ($relationshipTypes as $type)
                                        <option value="{{ $type }}" {{ old('relationship_type', $student->relationship_type) === $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($student->isSelfRepresented())
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">No se puede cambiar (auto-representado)</p>
                                @endif
                                @error('relationship_type')
                                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                    </div>

                    {{-- Form Actions --}}
                    <div class="px-6 sm:px-8 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3 sm:justify-between sm:items-center">
                        <a href="{{ route('students.show', $student) }}"
                           class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver detalles completos
                        </a>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('students.index') }}"
                               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Actualizar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>