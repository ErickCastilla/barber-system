<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Servicio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Formulario que apunta al método store del ServiceController -->
                    <form method="POST" action="{{ route('admin.services.store') }}">
                        @csrf <!-- Token de seguridad obligatorio en Laravel -->

                        <!-- Nombre del Servicio -->
                        <div>
                            <x-input-label for="name" value="Nombre del Servicio" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Descripción (Opcional) -->
                        <div class="mt-4">
                            <x-input-label for="description" value="Descripción (Opcional)" />
                            <textarea id="description" name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <!-- Duración en minutos -->
                            <div>
                                <x-input-label for="duration_minutes" value="Duración Estimada (Minutos)" />
                                <!-- El step="5" obliga a que sean múltiplos de 5 minutos -->
                                <x-text-input id="duration_minutes" class="block mt-1 w-full" type="number" step="5" min="5" name="duration_minutes" :value="old('duration_minutes', 30)" required />
                                <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
                            </div>

                            <!-- Precio -->
                            <div>
                                <x-input-label for="price" value="Precio ($)" />
                                <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" min="0" name="price" :value="old('price')" required />
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-end mt-6">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md mr-4" href="{{ route('admin.services.index') }}">
                                Cancelar
                            </a>

                            <x-primary-button>
                                Guardar Servicio
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
