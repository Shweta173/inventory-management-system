<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Login') — Inventory System</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" />

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: .875rem;
            min-height: 100vh;
            display: flex;
            background: #f3f4f6;
        }

        /* ── Left panel ─────────────────────────────────────────────── */
        .auth-left {
            width: 420px;
            flex-shrink: 0;
            background: #111827;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            top: -80px; left: -80px;
            width: 300px; height: 300px;
            background: rgba(92,68,32,.35);
            border-radius: 50%;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -60px; right: -60px;
            width: 220px; height: 220px;
            background: rgba(92,68,32,.25);
            border-radius: 50%;
        }
        .auth-brand {
            position: relative; z-index: 1;
            display: flex; align-items: center; gap: .75rem;
            margin-bottom: 2.5rem;
        }
        .auth-brand .brand-badge {
            width: 42px; height: 42px; border-radius: 10px;
            background: #5c4420;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: .95rem; color: #fff;
        }
        .auth-brand .brand-name { color: #fff; font-weight: 700; font-size: 1.1rem; letter-spacing: .02em; }
        .auth-tagline { position: relative; z-index: 1; }
        .auth-tagline h2 { color: #fff; font-size: 1.6rem; font-weight: 700; margin-bottom: .5rem; line-height: 1.25; }
        .auth-tagline p { color: rgba(255,255,255,.55); font-size: .85rem; line-height: 1.6; margin: 0; }
        .auth-dots { position: relative; z-index: 1; margin-top: 3rem; display: flex; gap: .4rem; }
        .auth-dots span { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,.2); }
        .auth-dots span:first-child { background: #5c4420; width: 24px; border-radius: 4px; }

        /* ── Right panel (form) ─────────────────────────────────────── */
        .auth-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 12px;
            padding: 2.5rem 2.25rem;
            box-shadow: 0 4px 24px rgba(0,0,0,.07);
        }
        .auth-card-title { font-size: 1.4rem; font-weight: 700; color: #111827; margin-bottom: .3rem; }
        .auth-card-sub { font-size: .82rem; color: #6b7280; margin-bottom: 1.75rem; }

        /* ── Form elements ──────────────────────────────────────────── */
        .form-label { font-size: .82rem; font-weight: 600; color: #111827; margin-bottom: .35rem; }
        .form-control {
            border: 1px solid #e5e7eb; border-radius: 7px;
            font-size: .84rem; padding: .55rem .8rem; color: #111827;
            transition: border-color .15s;
        }
        .form-control:focus { border-color: #9ca3af; box-shadow: none; outline: none; }
        .form-control::placeholder { color: #d1d5db; }
        .form-control.is-invalid { border-color: #dc2626; }
        .invalid-feedback { font-size: .78rem; color: #dc2626; margin-top: .3rem; }

        /* ── Password toggle ────────────────────────────────────────── */
        .pw-wrap { position: relative; }
        .pw-wrap .form-control { padding-right: 2.5rem; }
        .pw-toggle {
            position: absolute; right: .75rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; padding: 0; color: #9ca3af;
            cursor: pointer; font-size: 1rem; line-height: 1;
        }
        .pw-toggle:hover { color: #111827; }

        /* ── Submit button ──────────────────────────────────────────── */
        .btn-auth {
            width: 100%; padding: .65rem 1rem;
            background: #111827; color: #fff; border: none;
            border-radius: 7px; font-size: .88rem; font-weight: 600;
            cursor: pointer; transition: background .15s; margin-top: .25rem;
        }
        .btn-auth:hover { background: #1f2937; }

        /* ── Link row ───────────────────────────────────────────────── */
        .auth-link-row { text-align: center; font-size: .82rem; color: #6b7280; margin-top: 1.25rem; }
        .auth-link-row a { color: #111827; font-weight: 600; text-decoration: none; }
        .auth-link-row a:hover { text-decoration: underline; }

        /* ── Alert error ────────────────────────────────────────────── */
        .auth-alert {
            background: #fef2f2; border: 1px solid #fecaca; border-radius: 7px;
            padding: .65rem .9rem; font-size: .82rem; color: #dc2626;
            margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: .5rem;
        }

        @media (max-width: 640px) {
            .auth-left { display: none; }
            .auth-right { padding: 1.25rem; }
        }
    </style>
</head>
<body>

    {{-- Left decorative panel --}}
    <div class="auth-left">
        <div class="auth-brand">
            <div class="brand-badge">INV</div>
            <span class="brand-name">Inventory System</span>
        </div>
        <div class="auth-tagline">
            <h2>Manage your<br>inventory with ease.</h2>
            <p>Track materials, record inward &amp; outward movements, and monitor stock levels — all in one place.</p>
        </div>
        <div class="auth-dots">
            <span></span><span></span><span></span><span></span>
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="auth-right">
        <div class="auth-card">
            @yield('form')
        </div>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
    // Password visibility toggle
    document.querySelectorAll('.pw-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = this.closest('.pw-wrap').querySelector('input');
            var icon  = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });
</script>
</body>
</html>
