<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#111] overflow-hidden shadow-2xl sm:rounded-lg relative border border-gray-800">
                <!-- Imagen de fondo -->
                <div class="absolute inset-0 z-0">
                    <img src="{{ asset('images/mutant_barber_hero.png') }}" alt="Fondo Barbería" class="w-full h-full object-cover opacity-30">
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                </div>

                <!-- Contenido -->
                <div class="p-12 md:p-16 relative z-10 flex flex-col justify-end min-h-[350px] md:min-h-[500px]">
                    <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-2 tracking-tight">
                        <span class="text-orange-500">Bienvenido de vuelta,</span> {{ explode(' ', Auth::user()->name)[0] }}
                    </h1>
                    <p class="text-xl md:text-3xl text-gray-400 font-medium">
                        ¿Listo para el próximo corte?
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
