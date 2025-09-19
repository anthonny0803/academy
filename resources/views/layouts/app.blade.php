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

        {{-- Page Heading --}}
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Page Content --}}
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>

    {{-- Alert modal for sessions --}}
    @php
        $showModal = session('status') || session('error') || session('warning');
    @endphp

    <x-modal name="alertModal" :show="$showModal" maxWidth="md">
        <div class="p-6 text-center">
            @if (session('status'))
                <h3 class="text-xl font-semibold text-green-600 mb-2">¡Éxito!</h3>
                <p class="text-gray-800">{{ session('status') }}</p>
            @endif

            @if (session('error'))
                <h3 class="text-xl font-semibold text-red-600 mb-2">¡Error!</h3>
                <p class="text-gray-800">{{ session('error') }}</p>
            @endif

            @if (session('warning'))
                <h3 class="text-xl font-semibold text-yellow-600 mb-2">¡Advertencia!</h3>
                <p class="text-gray-800">{{ session('warning') }}</p>
            @endif

            <button x-on:click="$dispatch('close-modal', 'alertModal')"
                class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cerrar
            </button>
        </div>
    </x-modal>


    {{-- Set background --}}
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
