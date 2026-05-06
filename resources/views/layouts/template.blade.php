<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'GeoSpasial Web' }}</title>

    {{-- Google Fonts: Playfair Display + Lora --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Lora:ital,wght@0,400;0,500;1,400&display=swap"
        rel="stylesheet">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">

    <style>
        :root {
            --saddle: #8B4513;
            --leather: #A0522D;
            --copper: #B87333;
            --tan: #D2B48C;
            --sand: #F5DEB3;
            --cream: #FDF6E3;
            --dust: #E8D5B0;
            --bark: #5C3317;
            --gold: #C8860A;
            --ash: #6B5C4E;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: var(--cream);
            color: var(--bark);
            font-family: 'Lora', Georgia, serif;
        }

        /* ───────────────────────── NAVBAR ───────────────────────── */
        .cowboy-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: linear-gradient(180deg, var(--bark) 0%, var(--saddle) 100%);
            border-bottom: 3px solid var(--gold);
            height: 60px;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            box-shadow: 0 4px 20px rgba(92, 51, 23, 0.45);
        }

        .cowboy-nav .brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--sand);
            letter-spacing: 0.04em;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .cowboy-nav .brand i {
            color: var(--gold);
            font-size: 1rem;
        }

        .cowboy-nav .nav-links {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-left: auto;
        }

        .cowboy-nav .nav-links a {
            font-family: 'Lora', serif;
            font-size: 0.85rem;
            color: var(--tan);
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 4px;
            border: 1px solid transparent;
            transition: all 0.2s ease;
            letter-spacing: 0.03em;
        }

        .cowboy-nav .nav-links a:hover,
        .cowboy-nav .nav-links a.active {
            color: var(--cream);
            border-color: var(--gold);
            background: rgba(200, 134, 10, 0.15);
        }

        .cowboy-nav .nav-links a i {
            margin-right: 5px;
            font-size: 0.75rem;
        }

        /* Hamburger for mobile */
        .nav-toggler {
            display: none;
            background: none;
            border: 1px solid var(--copper);
            color: var(--sand);
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: auto;
        }

        @media (max-width: 640px) {
            .nav-toggler {
                display: block;
            }

            .cowboy-nav .nav-links {
                display: none;
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                flex-direction: column;
                background: var(--bark);
                padding: 12px;
                border-bottom: 2px solid var(--gold);
                gap: 4px;
            }

            .cowboy-nav .nav-links.open {
                display: flex;
            }

            .cowboy-nav .nav-links a {
                width: 100%;
            }
        }

        /* ───────────────────── PAGE BODY OFFSET ───────────────────── */
        .page-content {
            padding-top: 60px;
            min-height: 100vh;
        }

        /* ─────────────────────── TOAST ─────────────────────────────── */
        .toast-cowboy {
            background: var(--saddle) !important;
            color: var(--cream) !important;
            border: 1px solid var(--gold) !important;
            font-family: 'Lora', serif;
        }

        /* ──────────────────── SCROLLBAR ───────────────────────────── */
        ::-webkit-scrollbar {
            width: 7px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dust);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--copper);
            border-radius: 4px;
        }
    </style>

    @yield('styles')
</head>

<body>

    {{-- NAVBAR --}}
    @include('navbar')

    {{-- MAIN CONTENT --}}
    <div class="page-content">
        @yield('content')
    </div>

    {{-- TOAST --}}
    @include('toast')

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Leaflet JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

    {{-- Hamburger toggle --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggler = document.querySelector('.nav-toggler');
            const links = document.querySelector('.cowboy-nav .nav-links');
            if (toggler && links) {
                toggler.addEventListener('click', () => links.classList.toggle('open'));
            }
        });
    </script>

    @yield('script')
</body>

</html>
