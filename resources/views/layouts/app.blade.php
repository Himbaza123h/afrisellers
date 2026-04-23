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


    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        maxWidth: {
          '8xl': '1536px',
        }
      },
      container: {
        center: true,
        padding: '1rem',
        screens: {
          sm: '640px',
          md: '768px',
          lg: '1024px',
          xl: '1280px',
          '2xl': '1536px',
        }
      }
    }
  }
</script>
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
    <style>
/* ========================================
   CRITICAL: Prevent Horizontal Scroll
   ======================================== */

/* Prevent any horizontal overflow */
html, body {
    max-width: 100vw;
    overflow-x: hidden;
}

/* Ensure all containers respect viewport width */
* {
    box-sizing: border-box;
}

/* Container constraints */
.container {
    max-width: 100%;
    padding-left: 1rem;
    padding-right: 1rem;
}

/* Fix ad banner overflow */
#ad-banner {
    max-width: 100vw;
    overflow: hidden;
}

.ad-ticker-container {
    max-width: 100%;
    overflow: hidden;
}

.ad-ticker {
    will-change: transform;
    display: inline-flex;
}

/* Fix hero section overflow */
.hero-section {
    max-width: 100vw;
    overflow: hidden;
}

/* Constrain girl image properly */
.hero-section .xl\:block {
    right: 0;
    max-width: 380px;
}

@media (max-width: 1536px) {
    .hero-section .xl\:block {
        max-width: 320px;
    }
}

@media (max-width: 1280px) {
    .hero-section .xl\:block {
        display: none !important;
    }
}

/* Fix category subdropdown */
#navBar {
    max-width: 100vw;
    overflow: visible;
}

@media (max-width: 1279px) {
    #categorySubDropdown {
        margin-left: 0 !important;
        left: 0 !important;
        right: 0 !important;
    }
}

/* Fix main header overflow */
#mainHeader {
    max-width: 100vw;
    overflow: hidden;
}

/* Fix nav bar */
#navBar {
    max-width: 100vw;
    overflow: visible;
}

/* Fix top bar */
#topBar {
    max-width: 100vw;
    overflow: hidden;
}

/* Ensure search inputs don't overflow */
input[type="text"],
input[type="search"],
select {
    max-width: 100%;
}

/* Fix category sidebar positioning on mobile */
@media (max-width: 1023px) {
    #categorySidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
    }

    #categorySidebar > div {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 280px;
        max-width: 80%;
    }
}

/* Fix mobile menu toggle button */
#categoryToggle {
    position: fixed;
    z-index: 60;
}

/* Responsive container adjustments */
@media (max-width: 640px) {
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}

/* Fix flex containers that might overflow */
.flex {
    min-width: 0;
}

/* Images should not overflow */
img {
    max-width: 100%;
    height: auto;
}

/* Fix absolute positioning issues */
.absolute {
    max-width: 100%;
}

/* Ensure grid layouts don't overflow */
.grid {
    width: 100%;
}

/* Fix the hero search bar */
.hero-section form {
    max-width: 100%;
}

/* Additional mobile fixes */
@media (max-width: 768px) {
    /* Reduce padding on mobile */
    #mainHeader .container,
    #navBar .container,
    .hero-section .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    /* Stack elements vertically on small screens */
    .hero-section .flex {
        flex-direction: column;
    }

    /* Adjust hero content */
    .hero-section .flex-1 {
        width: 100%;
        padding: 1rem;
    }
}

/* Fix sticky positioning */
.sticky {
    width: 100%;
    max-width: 100vw;
}

/* Prevent text from causing overflow */
.whitespace-nowrap {
    overflow: hidden;
}

/* Fix for subdropdown content grid */
#subDropdownContent {
    width: 100%;
    overflow: hidden;
}

/* Ensure buttons don't overflow */
button, .btn, a.button {
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Root Font Configuration */
:root {
    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
    --font-display: 'Plus Jakarta Sans', 'Inter', sans-serif;
}

/* Body Text - Inter for Superior Readability */
body {
    font-family: var(--font-body);
    font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
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
    font-feature-settings: 'tnum';
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
       @if(request()->routeIs('featured-suppliers'))
        @include('frontend.home.sections.hero')
       @endif
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

@php
    $waNumber = \App\Models\SystemSetting::get('whatsapp_number', '250782179022');
    $waMsg = urlencode("Hello! I found you on AfriSellers and I'd like to get more information. Can you help me?");
@endphp
<a href="https://wa.me/{{ $waNumber }}?text={{ $waMsg }}"
   target="_blank"
   title="Chat with us on WhatsApp"
 class="fixed bottom-6 right-6 z-50 w-16 h-16 bg-[#25D366] hover:bg-[#1ebe5d] rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110">
    <svg class="w-9 h-9 text-white" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
        <span class="w-2 h-2 bg-white rounded-full animate-ping absolute"></span>
        <span class="w-1.5 h-1.5 bg-white rounded-full relative"></span>
    </span>
</a>

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
