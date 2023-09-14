<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>News Update</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon.svg') }} ">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireStyles
</head>

<body class="bg-white">
<header class="bg-gray-100 text-white py-6">
    <div class="container mx-auto">
        <img src= "{{ asset('img/logo.svg') }}" class="w-96 mx-auto" alt="Logo">
    </div>
</header>

<main class="container mx-auto">

    <livewire:news-controller/>

</main>
@livewireScripts
</body>
</html>
