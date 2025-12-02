<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <h1 class="text-2xl font-bold mb-2 text-white">Mis Asignaciones</h1>
                    <p class="text-gray-400 mb-6">
                        Prof. {{ $teacher->user->full_name }}
                    </p>

                    @if($assignments->isEmpty())
                        <div class="p-4 bg-yellow-900 rounded-lg">
                            <p class="text-yellow-200">No tienes asignaciones activas en este momento.</p>
                        </div>
                    @else
                        @foreach($assignments as $periodName => $periodAssignments)
                            <div class="mb-8">
                                <h2 class="text-xl font-semibold text-white mb-4 border-b border-gray-700 pb-2">
                                    📅 {{ $periodName }}
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($periodAssignments as $assignment)
                                        <div class="bg-gray-900 border border-gray-700 rounded-lg p-4 hover:border-gray-500 transition-colors">
                                            {{-- Header --}}
                                            <div class="mb-3">
                                                <h3 class="text-lg font-bold text-white">
                                                    {{ $assignment->subject->name }}
                                                </h3>
                                                <p class="text-gray-400 text-sm">
                                                    {{ $assignment->section->name }}
                                                </p>
                                            </div>

                                            {{-- Estado de configuración --}}
                                            @php
                                                $totalWeight = $assignment->gradeColumns->sum('weight');
                                                $isComplete = $totalWeight == 100;
                                                $hasColumns = $assignment->gradeColumns->count() > 0;
                                            @endphp

                                            <div class="mb-3">
                                                @if($isComplete)
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-900 text-green-300">
                                                        ✓ Configuración completa
                                                    </span>
                                                @elseif($hasColumns)
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-yellow-900 text-yellow-300">
                                                        ⚠ {{ number_format($totalWeight, 0) }}% configurado
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-red-900 text-red-300">
                                                        ✗ Sin evaluaciones
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Barra de progreso mini --}}
                                            <div class="w-full bg-gray-700 rounded-full h-2 mb-4">
                                                <div class="h-2 rounded-full transition-all duration-300 
                                                    {{ $isComplete ? 'bg-green-500' : ($totalWeight > 100 ? 'bg-red-500' : 'bg-yellow-500') }}"
                                                    style="width: {{ min($totalWeight, 100) }}%">
                                                </div>
                                            </div>

                                            {{-- Acciones --}}
                                            <div class="flex gap-2">
                                                <a href="{{ route('grade-columns.index', $assignment) }}"
                                                    class="flex-1 text-center px-3 py-2 text-sm bg-gray-700 text-white rounded hover:bg-gray-600 transition-colors">
                                                    ⚙️ Evaluaciones
                                                </a>
                                                
                                                @if($isComplete)
                                                    <a href="{{ route('grades.index', $assignment) }}"
                                                        class="flex-1 text-center px-3 py-2 text-sm bg-green-700 text-white rounded hover:bg-green-600 transition-colors">
                                                        📝 Calificar
                                                    </a>
                                                @else
                                                    <span class="flex-1 text-center px-3 py-2 text-sm bg-gray-800 text-gray-500 rounded cursor-not-allowed"
                                                        title="Completa la configuración primero">
                                                        📝 Calificar
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif

                    {{-- Botón volver --}}
                    <div class="mt-6">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-600">
                            ← Volver al Dashboard
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>