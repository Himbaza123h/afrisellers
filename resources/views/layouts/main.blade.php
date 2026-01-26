<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'AfriSellers') }} - @yield('title', 'User Account')</title>

    <!-- Favicon -->
       <link rel="icon" href="{{ asset('logofavicon.png') }}" type="image/png">



    <!-- Google Fonts - Inter (Modern & Professional) -->
    <!-- Google Fonts - Professional E-commerce Pairing -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('js/dashboard-switch.js') }}" defer></script>




    <!-- Tailwind CSS -->
    @production
    <link rel="stylesheet" href="{{ asset('assets/build/assets/app-BXx-PKfp.css') }}">
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        @vite(['resources/css/app.css'])
    @endproduction

    <style>
        /* Root Font Configuration */
        :root {
            --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            --font-display: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }

        /* Body Text - Inter for Superior Readability */
        body {
            font-family: var(--font-body);
            font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11'; /* Inter's beautiful alternates */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        /* Headings - Plus Jakarta Sans for Modern Warmth */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-display);
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.2;
        }

        /* Specific Heading Weights */
        h1 {
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        h2 {
            font-weight: 700;
            letter-spacing: -0.03em;
        }

        h3, h4 {
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        h5, h6 {
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        /* Display Font Utility Class */
        .font-display {
            font-family: var(--font-display);
        }

        /* Body Font Utility Class */
        .font-body {
            font-family: var(--font-body);
        }

        /* Enhanced Typography Utilities */
        .text-balance {
            text-wrap: balance;
        }

        /* Button & Interactive Elements */
        button, .btn, a.button {
            font-family: var(--font-display);
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        /* Form Elements */
        input, textarea, select {
            font-family: var(--font-body);
            font-weight: 400;
        }

        /* Labels */
        label {
            font-family: var(--font-display);
            font-weight: 500;
            letter-spacing: -0.01em;
        }

        /* Number & Stats Display */
        .stat-number, .price, .metric {
            font-family: var(--font-display);
            font-weight: 700;
            font-feature-settings: 'tnum'; /* Tabular numbers */
        }

        /* Navigation */
        nav, .nav-link {
            font-family: var(--font-display);
            font-weight: 500;
            letter-spacing: -0.01em;
        }

        /* Card Titles */
        .card-title {
            font-family: var(--font-display);
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        /* Badge & Tags */
        .badge, .tag {
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        /* Smooth Transitions */
        * {
            transition: font-weight 0.2s ease;
        }
    </style>

    <!-- Page-specific styles -->
    @stack('styles')
</head>
<body class="antialiased bg-gray-50">
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- JavaScript -->
    @production
    <script type="module" src="{{ asset('assets/build/assets/app-CuLzz5ye.js') }}"></script>
    @else
        @vite(['resources/js/app.js'])
    @endproduction

    <!-- Page-specific scripts -->
    @stack('scripts')
</body>
</html>
