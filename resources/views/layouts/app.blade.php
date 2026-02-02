<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'AfriSellers') }} - @yield('title', '| Selling With Us is Leading Market')</title>

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

    @production
    <link rel="stylesheet" href="{{ asset('assets/build/assets/app-BXx-PKfp.css') }}">
    <script type="module" src="{{ asset('assets/build/assets/app-CuLzz5ye.js') }}"></script>
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    <!-- Navigation - Hide on welcome page -->
    @unless(request()->routeIs('home'))
        @include('components.navigation')
    @endunless

    <!-- Main Content -->
    <main>


        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.footer')

    <!-- Page-specific scripts -->
    @stack('scripts')
<!-- Toast Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50"></div>

<script>
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `transform transition-all duration-300 ease-in-out mb-3`;

    const bgColor = type === 'success' ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500';
    const iconColor = type === 'success' ? 'text-green-500' : 'text-red-500';
    const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';

    const icon = type === 'success'
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';

    toast.innerHTML = `
        <div class="flex items-center gap-3 p-4 ${bgColor} rounded-lg shadow-lg border-l-4 min-w-[300px] max-w-md">
            <svg class="w-6 h-6 flex-shrink-0 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${icon}
            </svg>
            <p class="flex-1 text-sm font-medium ${textColor}">${message}</p>
            <button onclick="this.closest('div').parentElement.remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;

    // Slide in animation
    toast.style.transform = 'translateX(400px)';
    toast.style.opacity = '0';

    document.getElementById('toastContainer').appendChild(toast);

    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
        toast.style.opacity = '1';
    }, 10);

    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Check for Laravel session messages
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif

    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInputs = [
        { input: document.getElementById('heroSearchInput'), results: document.getElementById('heroSearchResults') },
        { input: document.getElementById('navSearchInput'), results: document.getElementById('navSearchResults') }
    ];

    searchInputs.forEach(({ input, results }) => {
        if (!input || !results) return;

        let debounceTimer;

        input.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                results.classList.add('hidden');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`/search/suggestions?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            results.innerHTML = '<div class="p-4 text-gray-500 text-sm">No results found</div>';
                            results.classList.remove('hidden');
                            return;
                        }

                        let html = '';
                        data.forEach(item => {
                            html += `
                                <a href="${item.url}" class="block p-3 hover:bg-gray-50 border-b last:border-b-0">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            <span class="text-xs font-semibold text-gray-500 uppercase">${item.type}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-sm text-gray-900 truncate">${item.title}</div>
                                            <div class="text-xs text-gray-600 truncate">${item.description || ''}</div>
                                        </div>
                                    </div>
                                </a>
                            `;
                        });

                        results.innerHTML = html;
                        results.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !results.contains(e.target)) {
                results.classList.add('hidden');
            }
        });
    });
});
</script>
</body>
</html>
