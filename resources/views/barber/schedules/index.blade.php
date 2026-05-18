<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Horarios (Bloques de Disponibilidad)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <p class="mb-6 text-gray-600">
                        Selecciona las casillas correspondientes a las horas en las que estarás disponible para recibir citas. 
                        <strong>Si quieres tener una hora para comer o descansar, simplemente deja esa casilla desmarcada.</strong>
                    </p>

                    <form action="{{ route('barber.schedules.store') }}" method="POST">
                        @csrf

                        <div class="overflow-x-auto">
                            <!-- Cuadrícula interactiva -->
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <!-- Primera columna vacía (para las horas) -->
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider border-r">
                                            Día / Hora
                                        </th>
                                        <!-- Cabeceras de los Días -->
                                        @foreach($days as $dayKey => $dayName)
                                            <th scope="col" class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $dayName }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!-- Generamos las filas por cada bloque de hora -->
                                    @foreach($timeSlots as $slot)
                                        <tr>
                                            <!-- Columna de la Hora -->
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-700 text-center border-r bg-gray-50">
                                                {{ $slot['label'] }}
                                            </td>
                                            
                                            <!-- Columnas de Checkboxes (Días) -->
                                            @foreach($days as $dayKey => $dayName)
                                                @php
                                                    // Verificamos si este bloque de este día está guardado en la BD
                                                    $isChecked = isset($activeSlots[$dayKey][$slot['start']]);
                                                    
                                                    // Creamos una llave única para el input (Ej: 1_08:00)
                                                    $inputName = "schedules[{$dayKey}_{$slot['start']}]";
                                                @endphp
                                                <td class="px-2 py-3 whitespace-nowrap text-center hover:bg-gray-100 transition">
                                                    <!-- El Checkbox -->
                                                    <input type="checkbox" name="{{ $inputName }}" 
                                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-5 h-5 cursor-pointer"
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <x-primary-button>
                                {{ __('Guardar Horarios') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
