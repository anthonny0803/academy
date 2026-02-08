<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('representatives.show', $representative) }}" class="inline-flex items-center gap-2 text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al representante
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Registrar Nuevo Estudiante</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Asociado al representante: <span class="font-medium text-emerald-600 dark:text-emerald-400">{{ $representative->full_name }}</span></p>
            </div>

            {{-- Form Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <form method="POST" action="{{ route('representatives.students.store', $representative) }}">
                    @csrf

                    <div class="p-6 sm:p-8 space-y-8">

                        {{-- Section: Datos del Representante (readonly) --}}
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Datos del Representante
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombres</label>
                                    <input type="text" value="{{ $representative->user->name }}" readonly class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl cursor-not-allowed opacity-60 uppercase">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellidos</label>
                                    <input type="text" value="{{ $representative->user->last_name }}" readonly class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl cursor-not-allowed opacity-60 uppercase">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">DNI/NIE</label>
                                    <input type="text" value="{{ $representative->document_id }}" readonly class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl cursor-not-allowed opacity-60 uppercase">
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700">

                        {{-- Section: Parentesco --}}
                        <div>
                            <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Parentesco
                            </h3>
                            <div class="max-w-md">
                                <label for="relationship_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Relación con el estudiante <span class="text-red-500">*</span>
                                </label>
                                <select id="relationship_type" name="relationship_type" required
                                        class="mt-1 block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200">
                                    <option value="">Selecciona una opción</option>
                                    @foreach ($relationshipTypes as $type)
                                        <option value="{{ $type }}" @selected(old('relationship_type') == $type)>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Si seleccionas "Auto-representado", los datos del estudiante se copiarán del representante.</p>
                                @error('relationship_type')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700">

                        {{-- Section: Datos del Estudiante --}}
                        <div>
                            <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Datos del Estudiante
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                {{-- Nombre --}}
                                <div class="space-y-1">
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nombres <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="off"
                                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 uppercase">
                                    @error('name')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Apellido --}}
                                <div class="space-y-1">
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Apellidos <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required autocomplete="off"
                                           class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 uppercase">
                                    @error('last_name')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- DNI/NIE --}}
                                <div class="space-y-1">
                                    <label for="document_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        DNI/NIE <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="document_id" name="document_id" value="{{ old('document_id') }}" required autocomplete="off"
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
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="off"
                                               class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 lowercase">
                                    </div>
                                    @error('email')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Sexo --}}
                                <div class="space-y-1">
                                    <label for="sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Sexo <span class="text-red-500">*</span>
                                    </label>
                                    <select id="sex" name="sex" required
                                            class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200">
                                        <option value="">Selecciona una opción</option>
                                        @foreach ($sexes as $sex)
                                            <option value="{{ $sex }}" @selected(old('sex') == $sex)>
                                                {{ ucfirst($sex) }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required autocomplete="off"
                                               class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 dark:[color-scheme:dark]">
                                    </div>
                                    @error('birth_date')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-700">

                        {{-- Section: Inscripción Inicial --}}
                        <div>
                            <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                                Inscripción Inicial
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
                                {{-- Período Académico --}}
                                <div class="space-y-1">
                                    <label for="academic_period_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Período Académico <span class="text-red-500">*</span>
                                    </label>
                                    <select id="academic_period_id" name="academic_period_id" required
                                            class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200">
                                        <option value="">Selecciona un período</option>
                                        @foreach ($academicPeriods as $period)
                                            <option value="{{ $period->id }}" @selected(old('academic_period_id') == $period->id)>
                                                {{ $period->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_period_id')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Sección --}}
                                <div class="space-y-1">
                                    <label for="section_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Sección <span class="text-red-500">*</span>
                                    </label>
                                    <select id="section_id" name="section_id" required disabled
                                            class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">Primero selecciona un período</option>
                                    </select>
                                    @error('section_id')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Hidden field --}}
                        <input type="hidden" id="is_self_represented" name="is_self_represented" value="0">

                    </div>

                    {{-- Form Actions --}}
                    <div class="px-6 sm:px-8 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row gap-3 sm:justify-end sm:items-center">
                        <a href="{{ route('representatives.show', $representative) }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Registrar Estudiante
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const representativeData = {
                name: @json($representative->user->name),
                last_name: @json($representative->user->last_name),
                email: @json($representative->user->email ?? ''),
                document_id: @json($representative->document_id),
                sex: @json($representative->user->sex),
                birth_date: @json($representative->birth_date ? $representative->birth_date->format('Y-m-d') : ''),
            };

            const academicPeriodsData = {!! json_encode(
                $academicPeriods->map(function ($period) {
                    return [
                        'id' => $period->id,
                        'sections' => $period->sections->map(fn($s) => ['id' => $s->id, 'name' => $s->name]),
                    ];
                }),
            ) !!};

            const selfRepresentedValue = @json(\App\Enums\RelationshipType::SelfRepresented->value);

            // Select inteligente de relationship_type
            const relationshipType = document.getElementById('relationship_type');
            const hiddenInput = document.getElementById('is_self_represented');
            const studentFields = {
                name: document.getElementById('name'),
                last_name: document.getElementById('last_name'),
                email: document.getElementById('email'),
                document_id: document.getElementById('document_id'),
                sex: document.getElementById('sex'),
                birth_date: document.getElementById('birth_date'),
            };

            function preventSelectInteraction(e) {
                if (e.target.getAttribute('data-readonly') === 'true') {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }

            relationshipType.addEventListener('change', function() {
                const isSelfRepresented = this.value === selfRepresentedValue;
                hiddenInput.value = isSelfRepresented ? '1' : '0';

                if (isSelfRepresented) {
                    Object.keys(studentFields).forEach(key => {
                        const field = studentFields[key];
                        field.value = representativeData[key];

                        if (field.tagName === 'SELECT') {
                            field.setAttribute('data-readonly', 'true');
                            field.setAttribute('tabindex', '-1');
                            field.addEventListener('mousedown', preventSelectInteraction);
                            field.addEventListener('keydown', preventSelectInteraction);
                        } else {
                            field.readOnly = true;
                        }

                        field.classList.add('cursor-not-allowed', 'opacity-60');
                    });
                } else {
                    Object.values(studentFields).forEach(field => {
                        field.value = '';

                        if (field.tagName === 'SELECT') {
                            field.removeAttribute('data-readonly');
                            field.removeAttribute('tabindex');
                            field.removeEventListener('mousedown', preventSelectInteraction);
                            field.removeEventListener('keydown', preventSelectInteraction);
                        } else {
                            field.readOnly = false;
                        }

                        field.classList.remove('cursor-not-allowed', 'opacity-60');
                    });
                }
            });

            // Select dinámico de secciones
            const periodSelect = document.getElementById('academic_period_id');
            const sectionSelect = document.getElementById('section_id');
            const oldSectionId = '{{ old('section_id') }}';
            const oldPeriodId = '{{ old('academic_period_id') }}';

            periodSelect.addEventListener('change', function() {
                const periodId = parseInt(this.value);
                sectionSelect.innerHTML = '<option value="">Selecciona una sección</option>';
                sectionSelect.disabled = !periodId;

                if (periodId) {
                    const period = academicPeriodsData.find(p => p.id === periodId);
                    if (period && period.sections) {
                        period.sections.forEach(section => {
                            const option = document.createElement('option');
                            option.value = section.id;
                            option.textContent = section.name;
                            if (section.id == oldSectionId) {
                                option.selected = true;
                            }
                            sectionSelect.appendChild(option);
                        });
                    }
                }
            });

            if (oldPeriodId) {
                periodSelect.value = oldPeriodId;
                periodSelect.dispatchEvent(new Event('change'));
            }

            if (relationshipType.value) {
                relationshipType.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>