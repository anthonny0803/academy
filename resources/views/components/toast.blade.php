@props([
    'type' => 'success', // success | error | warning | info
    'message' => '',
])

@php
    $colors = [
        'success' => 'bg-green-500',
        'error' => 'bg-red-500',
        'warning' => 'bg-yellow-500',
        'info' => 'bg-blue-500',
    ];
@endphp

<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
    class="fixed top-4 right-4 text-white px-4 py-2 rounded shadow-lg {{ $colors[$type] }}" role="alert">
    {{ $message }}
</div>
