<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="{{ asset('logofavicon.png') }}" type="image/png">
    <title>Under Maintenance — {{ \App\Models\SystemSetting::get('site_name', config('app.name')) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Plus Jakarta Sans', sans-serif; }

        :root {
            --main: #ff0808;
            --main-light: #fff0f0;
            --main-mid: #ffd0d0;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-12px); }
        }
        .float { animation: float 3s ease-in-out infinite; }

        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        .spin-slow { animation: spin-slow 10s linear infinite; }

        @keyframes pulse-ring {
            0%   { transform: scale(1);   opacity: 0.6; }
            100% { transform: scale(1.5); opacity: 0; }
        }
        .pulse-ring {
            animation: pulse-ring 2s ease-out infinite;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4"
      style="background: linear-gradient(135deg, #fff0f0 0%, #ffffff 50%, #fff5f5 100%);">

    <div class="text-center max-w-lg mx-auto">

        <!-- Animated Icon -->
        <div class="relative inline-flex items-center justify-center mb-8">

            <!-- Pulse rings behind icon -->
            <span class="absolute w-32 h-32 rounded-full pulse-ring"
                  style="background: rgba(255,8,8,0.12);"></span>
            <span class="absolute w-32 h-32 rounded-full pulse-ring"
                  style="background: rgba(255,8,8,0.08); animation-delay: 0.6s;"></span>

            <!-- Icon circle -->
            <div class="relative w-32 h-32 rounded-full flex items-center justify-center float"
                 style="background: #fff0f0; border: 3px solid #ffd0d0;">
                <svg class="w-16 h-16" fill="none" stroke="#ff0808" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>

            <!-- Spinning dashed ring -->
            <div class="absolute inset-0 w-32 h-32 rounded-full spin-slow"
                 style="border: 3px dashed rgba(255,8,8,0.3);"></div>
        </div>

        <!-- Site Name -->
        <p class="font-semibold text-sm uppercase tracking-widest mb-3"
           style="color: #ff0808;">
            {{ \App\Models\SystemSetting::get('site_name', config('app.name')) }}
        </p>

        <!-- Heading -->
        <h1 class="text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
            We're Under<br>
            <span style="color: #ff0808;">Maintenance</span>
        </h1>

        <!-- Description -->
        <p class="text-gray-500 text-base leading-relaxed mb-8">
            We're currently performing scheduled maintenance to improve your experience.
            We'll be back online shortly. Thank you for your patience!
        </p>

        <!-- Status Badge -->
        <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-semibold mb-8"
             style="background: #fff0f0; border: 1px solid #ffd0d0; color: #cc0000;">
            <span class="w-2 h-2 rounded-full animate-pulse" style="background: #ff0808;"></span>
            Maintenance in progress
        </div>

        <!-- Divider -->
        <div class="w-16 h-1 rounded-full mx-auto mb-8" style="background: #ff0808; opacity: 0.3;"></div>

        <!-- Contact -->
        @php $email = \App\Models\SystemSetting::get('site_email', ''); @endphp
        @if($email)
        <p class="text-gray-400 text-sm">
            Need urgent help?
            <a href="mailto:{{ $email }}"
               class="font-semibold hover:underline"
               style="color: #ff0808;">{{ $email }}</a>
        </p>
        @endif

    </div>

    <script>
    (function () {
        const SECRET = '{{ \App\Models\SystemSetting::get('maintenance_key', 'AFRISELLERS!TOPKEY') }}';
        let typed = '';

        document.addEventListener('keypress', function (e) {
            typed += e.key;

            if (typed.length > SECRET.length) {
                typed = typed.slice(-SECRET.length);
            }

            if (typed === SECRET) {
                typed = '';
                window.location.href = '{{ route('auth.signin') }}';
            }
        });
    })();
    </script>

</body>
</html>
