<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])

    {{-- @php
            try {
                echo Vite::asset('resources/js/app.js');
                echo Vite::asset("resources/js/Pages/{$page['component']}.vue");
            } catch (\Exception $e) {
                echo "<script>console.error('Vite assets not found. Please run \"npm run dev\" and refresh the page.')</script>";
            }
        @endphp --}}
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
