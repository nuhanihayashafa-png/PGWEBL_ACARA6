@extends('layouts.template')

@section('styles')
    <style>
        .page-home {
            padding: 2rem 1.5rem;
            max-width: 960px;
            margin: 0 auto;
        }

        .about-card {
            background: #FDF6E3;
            border: 1px solid #D2B48C;
            border-left: 4px solid #8B4513;
            border-radius: 10px;
            padding: 1.5rem 1.75rem;
            margin-bottom: 2rem;
        }

        .about-title {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 700;
            color: #5C3317;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: .75rem;
        }

        .about-body {
            font-size: .875rem;
            line-height: 1.8;
            color: #6B5C4E;
            margin: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #FDF6E3;
            border-radius: 12px;
            padding: 1.25rem 1rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .stat-point {
            border: 1.5px solid #E6C97A;
        }

        .stat-polyline {
            border: 1.5px solid #7AB8DB;
        }

        .stat-polygon {
            border: 1.5px solid #7DC87F;
        }

        .stat-user {
            border: 1.5px solid #C39DD8;
        }

        .stat-point::before {
            background: #C8860A;
        }

        .stat-polyline::before {
            background: #0068A5;
        }

        .stat-polygon::before {
            background: #2E7D32;
        }

        .stat-user::before {
            background: #6A0DAD;
        }

        .stat-icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto .75rem;
            font-size: 1.1rem;
        }

        .icon-point {
            background: #FEF3D7;
            color: #C8860A;
        }

        .icon-polyline {
            background: #D6ECFA;
            color: #0068A5;
        }

        .icon-polygon {
            background: #D4EDDA;
            color: #2E7D32;
        }

        .icon-user {
            background: #F0E4FA;
            color: #6A0DAD;
        }

        .stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: .35rem;
        }

        .num-point {
            color: #C8860A;
        }

        .num-polyline {
            color: #0068A5;
        }

        .num-polygon {
            color: #2E7D32;
        }

        .num-user {
            color: #6A0DAD;
        }

        .stat-label {
            font-size: .72rem;
            color: #6B5C4E;
            letter-spacing: .04em;
            text-transform: uppercase;
            font-weight: 500;
        }

        .home-divider {
            border: none;
            border-top: 1px solid #E8D5B0;
            margin: 1.5rem 0;
        }

        .home-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .home-footer-text {
            font-size: .72rem;
            color: #A0916A;
        }

        .badge-stack {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .tech-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .72rem;
            background: #F0E9DC;
            color: #8B4513;
            border: 1px solid #D2B48C;
            border-radius: 20px;
            padding: 4px 10px;
        }
    </style>
@endsection

@section('content')
    <div class="page-home">

        <div class="about-card">
            <div class="about-title">
                <i class="fa-solid fa-circle-info"></i>
                Tentang Aplikasi
            </div>
            <p class="about-body">
                Aplikasi ini dibuat untuk memenuhi tugas mata kuliah Pemrograman Geospasial Web Lanjut.
                Tersedia fitur peta interaktif dengan geometri titik, garis, dan area yang dapat ditambah,
                ditampilkan, diubah, dan dihapus. Dibangun menggunakan <em>Laravel</em> dan <em>PostgreSQL/PostGIS</em>.
            </p>
        </div>

        <div class="stats-grid">
            <div class="stat-card stat-point">
                <div class="stat-icon-wrap icon-point">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div class="stat-number num-point">{{ $jumlahPoint }}</div>
                <div class="stat-label">Total Titik</div>
            </div>
            <div class="stat-card stat-polyline">
                <div class="stat-icon-wrap icon-polyline">
                    <i class="fa-solid fa-route"></i>
                </div>
                <div class="stat-number num-polyline">{{ $jumlahPolyline }}</div>
                <div class="stat-label">Total Garis</div>
            </div>
            <div class="stat-card stat-polygon">
                <div class="stat-icon-wrap icon-polygon">
                    <i class="fa-solid fa-draw-polygon"></i>
                </div>
                <div class="stat-number num-polygon">{{ $jumlahPolygon }}</div>
                <div class="stat-label">Total Area</div>
            </div>
            <div class="stat-card stat-user">
                <div class="stat-icon-wrap icon-user">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-number num-user">{{ $jumlahUser }}</div>
                <div class="stat-label">Total Pengguna</div>
            </div>
        </div>

        <hr class="home-divider">
        <div class="home-footer">
            <span class="home-footer-text">Pemrograman Geospasial Web Lanjut &middot; 2024</span>
            <div class="badge-stack">
                <span class="tech-badge"><i class="fa-brands fa-laravel"></i> Laravel</span>
                <span class="tech-badge"><i class="fa-solid fa-database"></i> PostGIS</span>
                <span class="tech-badge"><i class="fa-solid fa-map"></i> Leaflet</span>
            </div>
        </div>

    </div>
@endsection
