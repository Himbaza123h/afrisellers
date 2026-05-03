<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found · {{ \App\Models\SystemSetting::get('site_name', config('app.name')) }}</title>
    <link rel="icon" href="{{ asset('mainlogo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0 }
        body {
            font-family:'Inter', sans-serif;
            background:#F7F7FB;
            color:#0F172A;
            min-height:100vh;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            padding:48px 24px;
            -webkit-font-smoothing:antialiased;
        }
        a { text-decoration:none; color:inherit }

        .logo { display:flex; align-items:center; gap:10px; margin-bottom:24px }

        .main { text-align:center; max-width:420px; width:100% }

        .code {
            font-family:'Plus Jakarta Sans', sans-serif;
            font-size:108px; font-weight:900; line-height:1;
            letter-spacing:-6px; color:#EAEAF2; margin-bottom:4px; user-select:none;
        }
        .title {
            font-family:'Plus Jakarta Sans', sans-serif;
            font-size:21px; font-weight:800; color:#0F172A;
            letter-spacing:-.35px; margin-bottom:10px;
        }
        .desc { font-size:14px; color:#94A3B8; line-height:1.75; margin-bottom:36px }

        .btn-primary {
            display:inline-flex; align-items:center; justify-content:center; gap:8px;
            width:100%; padding:13px 20px;
            background:#ff0808;
            color:white; border:none; border-radius:12px;
            font-family:'Plus Jakarta Sans', sans-serif; font-weight:700; font-size:14px; cursor:pointer;
            box-shadow:0 4px 16px rgba(255,8,8,.25),0 1px 3px rgba(255,8,8,.12);
            transition:all .18s; margin-bottom:10px; text-decoration:none;
        }
        .btn-primary:hover { opacity:.88; box-shadow:0 6px 22px rgba(255,8,8,.35); transform:translateY(-1px) }

        .btn-ghost {
            display:inline-flex; align-items:center; justify-content:center; gap:8px;
            width:100%; padding:13px 20px;
            background:white; color:#475569; border:1.5px solid #E2E8F0; border-radius:12px;
            font-family:'Plus Jakarta Sans', sans-serif; font-weight:700; font-size:14px; cursor:pointer;
            box-shadow:0 1px 3px rgba(0,0,0,.05); transition:all .18s; margin-bottom:32px; text-decoration:none;
        }
        .btn-ghost:hover { border-color:#ff0808; color:#ff0808; background:#FFF5F5; box-shadow:0 2px 10px rgba(255,8,8,.08) }

        .divider { display:flex; align-items:center; gap:12px; margin-bottom:20px }
        .divider-line { flex:1; height:1px; background:#E2E8F0 }
        .divider-text { font-size:11.5px; font-weight:600; color:#CBD5E1; white-space:nowrap }

        .ql-row { display:flex; gap:8px; justify-content:center; flex-wrap:wrap }
        .ql-link {
            display:inline-flex; align-items:center; gap:6px;
            padding:8px 16px; border-radius:100px;
            background:white; border:1.5px solid #E2E8F0;
            font-size:12.5px; font-weight:700; color:#475569;
            box-shadow:0 1px 3px rgba(0,0,0,.04); transition:all .15s; text-decoration:none;
        }
        .ql-link:hover { border-color:#ff0808; color:#ff0808; background:#FFF5F5; box-shadow:0 2px 10px rgba(255,8,8,.08); transform:translateY(-1px) }

        .footer-note { margin-top:48px; font-size:12px; color:#CBD5E1; font-weight:500 }
        .footer-note a { color:#CBD5E1; text-decoration:underline; text-underline-offset:3px }
        .footer-note a:hover { color:#ff0808 }

        @media(max-width:420px) {
            .code { font-size:80px; letter-spacing:-4px }
            .btn-primary, .btn-ghost { font-size:13px; padding:12px 16px }
        }
    </style>
</head>
<body>

    <a href="{{ route('home') }}" class="logo">
        <img src="{{ asset('mainlogo.png') }}" alt="{{ \App\Models\SystemSetting::get('site_name', config('app.name')) }}" style="height:40px; width:auto;">
    </a>

    <div class="main">
        <div class="code">404</div>
        <div class="title">Page not found</div>
        <div class="desc">
            This page doesn't exist or may have been moved.<br>
            Let's get you back on track.
        </div>

        <a href="{{ route('home') }}" class="btn-primary">
            <i class="fas fa-home"></i> Back to Home
        </a>

        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}" class="btn-ghost">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>

        <div class="divider">
            <div class="divider-line"></div>
            <span class="divider-text">or jump to</span>
            <div class="divider-line"></div>
        </div>

        <div class="ql-row">
            <a href="{{ route('home') }}" class="ql-link"><i class="fas fa-store"></i> Marketplace</a>
            @auth
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
                    <a href="{{ route('admin.dashboard.home') }}" class="ql-link"><i class="fas fa-shield-alt"></i> Admin Panel</a>
                @elseif(auth()->user()->hasRole('vendor'))
                    <a href="{{ route('vendor.dashboard.home') }}" class="ql-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                @else
                    <a href="{{ route('buyer.dashboard.home') }}" class="ql-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                @endif
            @else
                <a href="{{ route('auth.signin') }}" class="ql-link"><i class="fas fa-sign-in-alt"></i> Sign In</a>
            @endauth
        </div>
    </div>

    <p class="footer-note">
        Need help? <a href="mailto:{{ \App\Models\SystemSetting::get('site_email', config('mail.from.address')) }}">Contact support</a>
    </p>

</body>
</html>
