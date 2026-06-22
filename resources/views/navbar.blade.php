<nav class="cowboy-nav">
    <a href="{{ route('home') }}" class="brand">
        <i class="fa-solid fa-compass"></i>
        <span id="navbar-brand-text">@yield('page-title', 'Peta Interaktif')</span>
    </a>

    <button class="nav-toggler" aria-label="Toggle nav">☰</button>

    <div class="nav-links">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> Beranda
        </a>
        <a href="{{ route('map') }}" class="{{ request()->routeIs('map') ? 'active' : '' }}">
            <i class="fa-solid fa-map"></i> Peta
        </a>
        <a href="{{ route('table') }}" class="{{ request()->routeIs('table') ? 'active' : '' }}">
            <i class="fa-solid fa-table"></i> Tabel
        </a>
        <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">
            <i class="fa-solid fa-circle-info"></i> Tentang
        </a>

        {{-- Tombol Login --}}
        <a href="{{ route('login') }}" class="bg-primary text-white rounded px-3 py-1">
            <i class="fa-solid fa-right-to-bracket"></i> Login
        </a>
    </div>
</nav>
