<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'AfriSellers') }} - @yield('title', '| Selling With Us is Leading Market')</title>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Google Fonts - Inter (Modern & Professional) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="https://afrisellers.com/public/uploads/all/aAFWziNCGSdUDnBytozBZvp1XwYptPReQm39pDi1.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @production
    <link rel="stylesheet" href="{{ asset('build/assets/app-BXx-PKfp.css') }}">
    <script type="module" src="{{ asset('build/assets/app-CuLzz5ye.js') }}"></script>
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endproduction

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', 'Inter', sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .font-display {
            font-family: 'Poppins', sans-serif;
        }
    </style>

    <!-- Page-specific styles -->
    @stack('styles')
</head>
<body class="bg-gray-50 antialiased">
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Page-specific scripts -->
    @stack('scripts')
</body>
</html>
