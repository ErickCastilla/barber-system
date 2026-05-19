<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Recepción - Agenda del Día') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Alertas de éxito o error -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">¡Éxito!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Filtro por fecha -->
            <div class="mb-6 bg-white p-4 shadow-sm sm:rounded-lg flex justify-between items-center">
                <form action="{{ route('receptionist.appointments.index') }}" method="GET" class="flex items-center space-x-4">
                    <label for="date" class="font-medium text-gray-700">Ver agenda del día:</label>
                    <input type="date" id="date" name="date" value="{{ $filterDate }}" 
                           class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <x-primary-button type="submit">
                        {{ __('Filtrar') }}
                    </x-primary-button>
                </form>
                
                @if($filterDate === \Carbon\Carbon::today()->toDateString())
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-bold">HOY</span>
                @else
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-bold">
                        {{ \Carbon\Carbon::parse($filterDate)->format('d/m/Y') }}
                    </span>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if($appointments->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500">No hay citas registradas para esta fecha.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horario</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barbero</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($appointments as $appointment)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                                {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $appointment->barber->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                                {{ $appointment->client->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $appointment->service->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $color = 'bg-gray-100 text-gray-800';
                                                    if($appointment->status === 'pendiente' || $appointment->status === 'confirmada') $color = 'bg-yellow-100 text-yellow-800';
                                                    if($appointment->status === 'completada') $color = 'bg-green-100 text-green-800';
                                                    if($appointment->status === 'cancelada') $color = 'bg-red-100 text-red-800';
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center flex justify-center space-x-2">
                                                @if($appointment->status === 'pendiente' || $appointment->status === 'confirmada')
                                                    <!-- Botón Completar -->
                                                    <form action="{{ route('receptionist.appointments.complete', $appointment->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded shadow text-xs transition" title="Marcar como Completada">
                                                            ✓ Completada
                                                        </button>
                                                    </form>

                                                    <!-- Botón Cancelar -->
                                                    <form action="{{ route('receptionist.appointments.cancel', $appointment->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas marcar esta cita como CANCELADA (inasistencia)?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow text-xs transition" title="Marcar como Cancelada">
                                                            ✕ Cancelada
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400 text-xs italic">Sin acciones</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
