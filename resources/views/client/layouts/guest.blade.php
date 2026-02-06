<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-gray-100 dark:bg-neutral-900 text-gray-900 antialiased">

    <div class="grid grid-cols-2 h-screen">
        <div
            class="bg-gradient-to-r relative from-[#ff0] to-[#ff0] dark:opacity-60 flex items-center justify-center text-center">
            <ul class="circles">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
            <div>
                <h1
                    class="text-4xl p-4 font-extrabold tracking-tight text-white sm:text-5xl md:text-6xl bg-clip-text text-transparent bg-gradient-to-r from-indigo-300 via-blue-500 to-cyan-400 drop-shadow-lg">
                    Vinify Ai
                </h1>

                <p class="mt-3 text-white">
                    Scanner intelligent de plagiat
                </p>

            </div>
        </div>

        <div class="flex items-center justify-center px-8">
            <div class="w-full">
                {{ $slot }}
            </div>
        </div>
    </div>


</body>

</html>
