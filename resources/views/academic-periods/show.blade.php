<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Período Académico: {{ $academicPeriod->name }}
            </h2>
            <a href="{{ route('academic-periods.index') }}"
                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">
                ← Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Mensajes flash --}}
            @if (session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Información general --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Información General
                    </h3>
                    <dl class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Estado</dt>
                            <dd class="mt-1">
                                @if($academicPeriod->isActive())
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Activo
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Cerrado
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Fecha Inicio</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                                {{ $academicPeriod->start_date->format('d/m/Y') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Fecha Fin</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                                {{ $academicPeriod->end_date->format('d/m/Y') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Permite Promoción</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                                {{ $academicPeriod->isPromotable() ? 'Sí' : 'No' }}
                            </dd>
                        </div>
                    </dl>
                    @if($academicPeriod->notes)
                        <div class="mt-4">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Notas</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $academicPeriod->notes }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Estadísticas --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Estadísticas
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $stats['total_sections'] }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Secciones</div>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ $stats['active_sections'] }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Secciones Activas</div>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ $stats['total_enrollments'] }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Inscripciones</div>
                        </div>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ $stats['active_enrollments'] }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Inscripciones Activas</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-gray-600 dark:text-gray-300">
                                {{ $stats['completed_enrollments'] }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Completadas</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sección de Cierre de Período (solo si está activo) --}}
            @if($academicPeriod->isActive())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Cerrar Período Académico
                        </h3>

                        {{-- Advertencia de fecha --}}
                        @if(isset($closeValidation['has_date_warning']) && $closeValidation['has_date_warning'])
                            <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                        {{ $closeValidation['issues']['date']['message'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($closeValidation['can_close'])
                            {{-- Preview del cierre --}}
                            @if($closePreview)
                                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-3">
                                        Vista previa del cierre
                                    </h4>
                                    <div class="grid grid-cols-3 gap-4 mb-4">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                                {{ $closePreview['passed'] }}
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Aprobarán</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                                                {{ $closePreview['failed'] }}
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Reprobarán</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">
                                                {{ $closePreview['sections_to_deactivate'] }}
                                            </div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Secciones a desactivar</div>
                                        </div>
                                    </div>

                                    {{-- Detalle por sección --}}
                                    @if(count($closePreview['details']) > 0)
                                        <details class="mt-4">
                                            <summary class="cursor-pointer text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                                Ver detalle por sección
                                            </summary>
                                            <div class="mt-2 space-y-2 max-h-60 overflow-y-auto">
                                                @foreach($closePreview['details'] as $section)
                                                    <div class="p-2 bg-white dark:bg-gray-700 rounded text-sm">
                                                        <strong>{{ $section['name'] }}</strong>
                                                        <span class="text-green-600 dark:text-green-400 ml-2">
                                                            {{ count($section['passed']) }} aprobados
                                                        </span>
                                                        <span class="text-red-600 dark:text-red-400 ml-2">
                                                            {{ count($section['failed']) }} reprobados
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </details>
                                    @endif
                                </div>

                                {{-- Botón de cierre --}}
                                <form action="{{ route('academic-periods.close', $academicPeriod) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition"
                                        onclick="return confirm('¿Está SEGURO de cerrar este período académico?\n\nEsta acción:\n- Completará {{ $closePreview['passed'] + $closePreview['failed'] }} inscripciones\n- Desactivará {{ $closePreview['sections_to_deactivate'] }} secciones\n- Desactivará el período académico\n\nEsta acción NO se puede deshacer.')">
                                        🔒 Cerrar Período Académico
                                    </button>
                                </form>
                            @endif
                        @else
                            {{-- Reporte de problemas --}}
                            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <div class="flex items-start mb-4">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <h4 class="font-medium text-red-800 dark:text-red-200">
                                            No se puede cerrar el período
                                        </h4>
                                        <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                            Hay inscripciones con datos incompletos. Revise el siguiente reporte:
                                        </p>
                                    </div>
                                </div>

                                {{-- Reporte detallado --}}
                                @if(isset($closeValidation['issues']['sections']))
                                    <div class="space-y-4 max-h-96 overflow-y-auto">
                                        @foreach($closeValidation['issues']['sections'] as $sectionName => $subjects)
                                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                                <h5 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                                    📁 Sección: {{ $sectionName }}
                                                </h5>
                                                @foreach($subjects as $subjectName => $issues)
                                                    <div class="ml-4 mb-2">
                                                        <h6 class="font-medium text-gray-700 dark:text-gray-300">
                                                            📚 {{ $subjectName }}
                                                        </h6>
                                                        <ul class="ml-4 text-sm text-gray-600 dark:text-gray-400">
                                                            @foreach($issues as $issue)
                                                                @if($issue['type'] === 'configuration')
                                                                    <li class="text-orange-600 dark:text-orange-400">
                                                                        ⚠️ {{ $issue['message'] }}
                                                                    </li>
                                                                @elseif($issue['type'] === 'grades')
                                                                    @foreach($issue['students'] as $student)
                                                                        <li>
                                                                            👤 {{ $student['student'] }} - 
                                                                            <span class="text-red-600 dark:text-red-400">
                                                                                Falta: {{ implode(', ', $student['missing']) }}
                                                                            </span>
                                                                        </li>
                                                                    @endforeach
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @else
                {{-- Período ya cerrado --}}
                <div class="bg-gray-100 dark:bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">
                            Período Cerrado
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Este período académico ya ha sido cerrado y no puede modificarse.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Lista de secciones --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Secciones del Período
                    </h3>
                    @if($academicPeriod->sections->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Sección
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Estado
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Total
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Activas
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Completadas
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($academicPeriod->sections as $section)
                                        <tr>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $section->name }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($section->is_active)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        Activa
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        Inactiva
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">
                                                {{ $section->enrollments_count }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm text-yellow-600 dark:text-yellow-400">
                                                {{ $section->active_enrollments_count }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-400">
                                                {{ $section->completed_enrollments_count }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('sections.show', $section) }}"
                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">
                                                    Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                            No hay secciones registradas en este período.
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>