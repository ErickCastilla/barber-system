<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mis Próximas Citas') }}
            </h2>
            <a href="{{ route('client.appointments.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
                + Agendar Cita
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Alerta de éxito al agendar o cancelar -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">¡Éxito!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Alerta de error (Ej. al intentar cancelar tarde) -->
            @if(session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-bold">Aviso importante</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if($appointments->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No tienes citas próximas</h3>
                            <p class="mt-1 text-sm text-gray-500">¿Qué esperas para lucir un corte fresco?</p>
                            <div class="mt-6">
                                <a href="{{ route('client.appointments.create') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Agendar mi primera cita &rarr;
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Listado de próximas citas -->
                        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($appointments as $appointment)
                                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 shadow-sm hover:shadow-md transition">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                            {{ $appointment->status === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                        <span class="text-gray-500 text-sm font-medium">
                                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M, Y') }}
                                        </span>
                                    </div>
                                    
                                    <h4 class="text-xl font-bold text-gray-800 mb-2">{{ $appointment->service->name ?? 'N/A' }}</h4>
                                    
                                    <div class="flex flex-col space-y-2 text-gray-600 mb-4">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            Con: {{ $appointment->barber->name ?? 'N/A' }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Hora: {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                        </div>
                                    </div>

                                    <!-- Botón de Cancelación (Solo visible si falta más de 1 hora y no está cancelada/completada) -->
                                    @php
                                        $appointmentDateTime = \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->start_time);
                                        $isCancelable = \Carbon\Carbon::now()->diffInMinutes($appointmentDateTime, false) >= 60;
                                    @endphp

                                    @if($appointment->status === 'pendiente' || $appointment->status === 'confirmada')
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            @if($isCancelable)
                                                <form action="{{ route('client.appointments.cancel', $appointment->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta cita?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-semibold flex items-center transition">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Cancelar Cita
                                                    </button>
                                                </form>
                                            @else
                                                <p class="text-xs text-orange-500 flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                    Demasiado tarde para cancelar en línea. Llama al local.
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
