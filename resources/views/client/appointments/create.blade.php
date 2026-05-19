<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agendar Nueva Cita') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    
                    <h3 class="text-2xl font-bold mb-6 text-gray-800 text-center">Reserva tu Espacio</h3>

                    <form action="{{ route('client.appointments.store') }}" method="POST" id="appointmentForm" class="space-y-6">
                        @csrf

                        <!-- 1. Selección de Barbero -->
                        <div>
                            <x-input-label for="barber_id" :value="__('1. Selecciona a tu Barbero')" class="text-lg font-semibold" />
                            <select id="barber_id" name="barber_id" class="mt-2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled selected>Elige un barbero...</option>
                                @foreach($barbers as $barber)
                                    <option value="{{ $barber->id }}">{{ $barber->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. Selección de Servicio -->
                        <div>
                            <x-input-label for="service_id" :value="__('2. ¿Qué servicio deseas?')" class="text-lg font-semibold" />
                            <select id="service_id" name="service_id" class="mt-2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled selected>Elige un servicio...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">
                                        {{ $service->name }} ({{ $service->duration_minutes }} min) - ${{ number_format($service->price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. Selección de Fecha -->
                        <div>
                            <x-input-label for="appointment_date" :value="__('3. ¿Qué día quieres venir?')" class="text-lg font-semibold" />
                            <input type="date" id="appointment_date" name="appointment_date" 
                                   min="{{ \Carbon\Carbon::today()->toDateString() }}" 
                                   class="mt-2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                        </div>

                        <!-- 4. Selección de Hora (Cargada dinámicamente) -->
                        <div>
                            <x-input-label for="start_time" :value="__('4. Horarios Disponibles')" class="text-lg font-semibold" />
                            <div id="loading-slots" class="hidden text-sm text-indigo-600 mt-2">Buscando horarios libres...</div>
                            <div id="no-slots" class="hidden text-sm text-red-600 mt-2 font-medium">No hay horarios disponibles para las opciones seleccionadas.</div>
                            
                            <select id="start_time" name="start_time" class="mt-2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required disabled>
                                <option value="" disabled selected>Primero selecciona barbero, servicio y fecha...</option>
                            </select>
                        </div>

                        <!-- Botón de Envío -->
                        <div class="mt-8 pt-4 border-t border-gray-200">
                            <x-primary-button id="submitBtn" class="w-full justify-center text-lg py-3" disabled>
                                {{ __('Confirmar Cita') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Script para buscar horarios disponibles (AJAX) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const barberSelect = document.getElementById('barber_id');
            const serviceSelect = document.getElementById('service_id');
            const dateInput = document.getElementById('appointment_date');
            const timeSelect = document.getElementById('start_time');
            const loadingMsg = document.getElementById('loading-slots');
            const noSlotsMsg = document.getElementById('no-slots');
            const submitBtn = document.getElementById('submitBtn');

            // Cada vez que cambie alguno de estos 3 campos, intentamos buscar horarios
            const checkSlots = () => {
                const barber_id = barberSelect.value;
                const service_id = serviceSelect.value;
                const date = dateInput.value;

                // Solo buscamos si los tres campos tienen un valor seleccionado
                if(barber_id && service_id && date) {
                    fetchSlots(barber_id, service_id, date);
                } else {
                    timeSelect.innerHTML = '<option value="" disabled selected>Primero selecciona barbero, servicio y fecha...</option>';
                    timeSelect.disabled = true;
                    submitBtn.disabled = true;
                }
            };

            barberSelect.addEventListener('change', checkSlots);
            serviceSelect.addEventListener('change', checkSlots);
            dateInput.addEventListener('change', checkSlots);
            timeSelect.addEventListener('change', () => {
                submitBtn.disabled = !timeSelect.value;
            });

            function fetchSlots(barber_id, service_id, date) {
                // Mostramos mensaje de carga
                loadingMsg.classList.remove('hidden');
                noSlotsMsg.classList.add('hidden');
                timeSelect.disabled = true;
                submitBtn.disabled = true;
                timeSelect.innerHTML = '<option value="" disabled selected>Cargando...</option>';

                // Hacemos la petición a la API
                fetch(`/client/appointments/available-slots?barber_id=${barber_id}&service_id=${service_id}&date=${date}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingMsg.classList.add('hidden');
                        timeSelect.innerHTML = '<option value="" disabled selected>Elige un horario...</option>';
                        
                        if(data.slots && data.slots.length > 0) {
                            timeSelect.disabled = false;
                            data.slots.forEach(slot => {
                                const option = document.createElement('option');
                                option.value = slot;
                                option.textContent = slot;
                                timeSelect.appendChild(option);
                            });
                        } else {
                            noSlotsMsg.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching slots:', error);
                        loadingMsg.classList.add('hidden');
                        timeSelect.innerHTML = '<option value="" disabled selected>Error al cargar horarios</option>';
                    });
            }
        });
    </script>
</x-app-layout>
