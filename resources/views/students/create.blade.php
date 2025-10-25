<x-app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('representatives.students.store', $representative) }}">
                        @csrf

                        <div class="flex flex-wrap -mx-2 mb-4">
                            {{-- DATOS DEL REPRESENTANTE (SIN CAMBIOS) --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="representative_name" :value="__('Nombres del representante')" />
                                <x-text-input id="representative_name"
                                    class="block mt-1 w-full uppercase cursor-not-allowed opacity-60" :value="$representative->user->name"
                                    readonly />
                            </div>
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="representative_last_name" :value="__('Apellidos del representante')" />
                                <x-text-input id="representative_last_name"
                                    class="block mt-1 w-full uppercase cursor-not-allowed opacity-60" :value="$representative->user->last_name"
                                    readonly />
                            </div>
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="representative_document_id" :value="__('DNI/NIE del representante')" />
                                <x-text-input id="representative_document_id"
                                    class="block mt-1 w-full uppercase cursor-not-allowed opacity-60" :value="$representative->document_id"
                                    readonly />
                            </div>

                            {{-- PARENTESCO (PRIMERO, PARA DETECTAR AUTO-REPRESENTADO) --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="relationship_type" :value="__('Parentesco con estudiante *')" />
                                <select id="relationship_type" name="relationship_type"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">Selecciona una opción</option>
                                    @foreach ($relationshipTypes as $type)
                                        <option value="{{ $type }}" @selected(old('relationship_type') == $type)>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('relationship_type')" class="mt-2" />
                            </div>

                            {{-- DATOS DEL ESTUDIANTE --}}
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="name" :value="__('Nombres')" />
                                <x-text-input id="name" class="block mt-1 w-full uppercase" type="text"
                                    name="name" :value="old('name')" required autofocus autocomplete="off" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="last_name" :value="__('Apellidos')" />
                                <x-text-input id="last_name" class="block mt-1 w-full uppercase" type="text"
                                    name="last_name" :value="old('last_name')" required autocomplete="off" />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="document_id" :value="__('DNI/NIE')" />
                                <x-text-input id="document_id" class="block mt-1 w-full uppercase" type="text"
                                    name="document_id" :value="old('document_id')" pattern="[A-Za-z]{0,1}[0-9]{7,9}[A-Za-z]{1}"
                                    autocomplete="off" />
                                <x-input-error :messages="$errors->get('document_id')" class="mt-2" />
                            </div>
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="email" :value="__('Correo (Opcional)')" />
                                <x-text-input id="email" class="block mt-1 w-full lowercase" type="email"
                                    name="email" :value="old('email')" autocomplete="off" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="sex" :value="__('Sexo')" />
                                <select id="sex" name="sex"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">Selecciona una opción</option>
                                    @foreach ($sexes as $sex)
                                        <option value="{{ $sex }}" @selected(old('sex') == $sex)>
                                            {{ ucfirst($sex) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('sex')" class="mt-2" />
                            </div>
                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="birth_date" :value="__('Fecha de Nacimiento')" />
                                <div class="relative">
                                    <input type="date" id="birth_date" name="birth_date"
                                        value="{{ old('birth_date') }}"
                                        class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 p-2 dark:[color-scheme:dark]"
                                        required autocomplete="off" />
                                </div>
                                <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="academic_period_id" :value="__('Periodo Académico')" />
                                <select id="academic_period_id" name="academic_period_id"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required>
                                    <option value="">Selecciona un período</option>
                                    @foreach ($academicPeriods as $period)
                                        <option value="{{ $period->id }}" @selected(old('academic_period_id') == $period->id)>
                                            {{ $period->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('academic_period_id')" class="mt-2" />
                            </div>

                            <div class="w-full sm:w-1/3 px-2 mb-4">
                                <x-input-label for="section_id" :value="__('Sección')" />
                                <select id="section_id" name="section_id"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required disabled>
                                    <option value="">Primero selecciona un período</option>
                                </select>
                                <x-input-error :messages="$errors->get('section_id')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Hidden field para is_self_represented --}}
                        <input type="hidden" id="is_self_represented" name="is_self_represented" value="0">

                        <div class="flex items-center justify-end">
                            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                href="{{ route('dashboard') }}">
                                {{ __('Volver al Panel') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Registrar Estudiante') }}
                            </x-primary-button>
                        </div>
                    </form>

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

                            // Función auxiliar para prevenir interacción con el select
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
                                    // Rellenar con datos del representante
                                    Object.keys(studentFields).forEach(key => {
                                        const field = studentFields[key];
                                        field.value = representativeData[key];

                                        if (field.tagName === 'SELECT') {
                                            // Para select: prevenir cambios pero mantener cursor
                                            field.setAttribute('data-readonly', 'true');
                                            field.setAttribute('tabindex', '-1');
                                            
                                            // Prevenir que se abra el dropdown
                                            field.addEventListener('mousedown', preventSelectInteraction);
                                            field.addEventListener('keydown', preventSelectInteraction);
                                        } else {
                                            // Para inputs normales, usar readOnly
                                            field.readOnly = true;
                                        }

                                        field.classList.add('cursor-not-allowed', 'opacity-60');
                                    });
                                } else {
                                    // Limpiar campos
                                    Object.values(studentFields).forEach(field => {
                                        field.value = '';

                                        if (field.tagName === 'SELECT') {
                                            // Restaurar interactividad del select
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

                            // Restaurar período académico si hay old value
                            if (oldPeriodId) {
                                periodSelect.value = oldPeriodId;
                                periodSelect.dispatchEvent(new Event('change'));
                            }

                            // Trigger inicial para relationship_type si hay old value
                            if (relationshipType.value) {
                                relationshipType.dispatchEvent(new Event('change'));
                            }
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>