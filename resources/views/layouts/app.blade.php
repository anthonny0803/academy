<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Academy') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>

    {{-- Alert Modal para mensajes de sesión --}}
    @php
        $showModal = session('status') || session('error');
    @endphp

    <x-modal name="alertModal" :show="$showModal" maxWidth="md">
        {{-- Contenedor del contenido que controla el tamaño y el centrado --}}
        <div class="p-6 text-center">
            @if (session('status'))
                <h2 class="text-lg font-semibold text-green-700 mb-2">{{ session('status') }}</h2>
            @endif

            @if (session('error'))
                <h2 class="text-lg font-semibold text-red-700 mb-2">{{ session('error') }}</h2>
            @endif

            <button x-on:click="$dispatch('close-modal', 'alertModal')"
                class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cerrar
            </button>
        </div>
    </x-modal>

    {{-- Ajuste del fondo semitransparente más transparente --}}
    <style>
        [x-cloak] {
            display: none !important;
        }

        .bg-gray-500.opacity-75 {
            background-color: rgba(107, 114, 128, 0.3);
            /* mucho menos opaco */
        }
    </style>
</body>

</html>
