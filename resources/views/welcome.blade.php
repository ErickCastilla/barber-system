<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erick's Barber</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #050505; /* Deep black background */
        }
    </style>
</head>
<body class="text-white antialiased min-h-screen flex flex-col">
    <!-- Header -->
    <header class="w-full pt-8 pb-4 px-8 lg:px-16 flex justify-between items-center z-10">
        <div class="text-2xl font-bold tracking-wider uppercase text-white">
            ERICK'S BARBER
        </div>
    </header>

    <!-- Main Content (Hero Image) -->
    <main class="flex-grow flex items-center justify-center px-4 sm:px-8 lg:px-16 py-4 lg:py-8 z-0">
        <div class="w-full max-w-[1200px] relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-gray-800 to-gray-600 rounded-sm blur opacity-25 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
            <img src="{{ asset('images/barbershop_hero.png') }}" alt="Erick's Barber Shop" class="relative w-full h-[40vh] md:h-[60vh] object-cover shadow-2xl rounded-sm opacity-90 transition-opacity duration-700 ease-in-out group-hover:opacity-100 border border-gray-900">
        </div>
    </main>

    <!-- Footer Actions -->
    <footer class="w-full pb-16 pt-8 flex flex-col sm:flex-row justify-center items-center gap-8 sm:gap-12 z-10">
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="text-sm font-semibold tracking-[0.2em] text-gray-400 hover:text-white transition-colors uppercase">
                    DASHBOARD
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm font-semibold tracking-[0.2em] text-gray-400 hover:text-white transition-colors uppercase">
                    Iniciar Sesión
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-sm font-semibold tracking-[0.2em] text-gray-400 hover:text-white transition-colors uppercase">
                        Registrarse
                    </a>
                @endif
            @endauth
        @endif
    </footer>
</body>
</html>
