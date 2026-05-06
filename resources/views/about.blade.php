@extends('layouts.template')

@section('content')
<div class="container" style="max-width:700px;margin:60px auto;padding:40px 20px">
    <h2 style="font-family:'Playfair Display',serif;color:#5C3317;border-bottom:2px solid #C8860A;padding-bottom:10px">
        <i class="fas fa-circle-info" style="color:#C8860A"></i> Tentang Aplikasi
    </h2>
    <p style="color:#6B5C4E;line-height:1.8;margin-top:16px">
        Aplikasi ini merupakan sistem inventarisasi data geospasial berbasis web yang dibangun menggunakan
        framework Laravel dan library Leaflet JS. Sistem ini memungkinkan pengguna untuk menambahkan,
        menampilkan, dan mengelola data titik, garis, serta area pada peta interaktif.
    </p>
    <hr style="border-color:#D2B48C;margin:24px 0">
    <h5 style="font-family:'Playfair Display',serif;color:#8B4513">👤 Dibuat oleh</h5>
    <p style="color:#6B5C4E;line-height:1.8">
        <strong>Nuha Nihaya Shafa</strong><br>
        NIM: 24/545410/SV/25677<br>
        Sarjana Terapan Sistem Informasi Geografis<br>
        Departemen Teknologi Kebumian — Sekolah Vokasi<br>
        Universitas Gadjah Mada, Yogyakarta
    </p>
    <hr style="border-color:#D2B48C;margin:24px 0">
    <h5 style="font-family:'Playfair Display',serif;color:#8B4513">🛠 Teknologi</h5>
    <ul style="color:#6B5C4E;line-height:2">
        <li>Laravel 11 — Backend & Routing</li>
        <li>PostgreSQL + PostGIS — Basis data spasial</li>
        <li>Leaflet JS — Peta interaktif</li>
        <li>Leaflet Draw — Input geometri</li>
        <li>Bootstrap 5 — UI Framework</li>
    </ul>
</div>
@endsection
