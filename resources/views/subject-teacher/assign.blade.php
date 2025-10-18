<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">
                        Asignar Materias a {{ $teacher->user->full_name }}
                    </h1>

                    <form method="POST" action="{{ route('teachers.subjects.store', $teacher) }}">
                        @csrf

                        {{-- Lista de materias con checkboxes --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-3">
                                Selecciona las materias que puede impartir este profesor:
                            </label>

                            @if($subjects->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($subjects as $subject)
                                        <label class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                            <input 
                                                type="checkbox" 
                                                name="subjects[]" 
                                                value="{{ $subject->id }}"
                                                {{ $teacher->subjects->contains($subject->id) ? 'checked' : '' }}
                                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <span class="ml-3 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $subject->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>

                                <x-input-error :messages="$errors->get('subjects')" class="mt-2" />

                                {{-- Contador de selección --}}
                                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <p class="text-sm text-blue-800 dark:text-blue-200">
                                        <strong id="selected-count">{{ $teacher->subjects->count() }}</strong> 
                                        materia(s) seleccionada(s)
                                    </p>
                                </div>
                            @else
                                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600">
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                        No hay materias activas disponibles. 
                                        <a href="{{ route('subjects.index') }}" class="underline">Crear materias primero</a>
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Botones de acción --}}
                        <div class="flex items-center justify-end mt-6 gap-4">
                            <a href="{{ route('teachers.show', $teacher) }}"
                                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Cancelar
                            </a>

                            @if($subjects->count() > 0)
                                <x-primary-button>
                                    Guardar Materias
                                </x-primary-button>
                            @endif
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- Script para contador en tiempo real --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="subjects[]"]');
            const counter = document.getElementById('selected-count');

            function updateCounter() {
                const checked = document.querySelectorAll('input[name="subjects[]"]:checked').length;
                counter.textContent = checked;
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateCounter);
            });
        });
    </script>
</x-app-layout>