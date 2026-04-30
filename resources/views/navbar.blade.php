<nav class="cowboy-nav">
    <a class="brand" href="{{ route('home') }}">
        <i class="fas fa-compass"></i>
        {{ $title ?? 'GeoSpasial Web' }}
    </a>

    <button class="nav-toggler" onclick="this.nextElementSibling.classList.toggle('open')">
        <i class="fas fa-bars"></i>
    </button>

    <div class="nav-links">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Beranda
        </a>
        <a href="{{ route('map') }}" class="{{ request()->routeIs('map') ? 'active' : '' }}">
            <i class="fas fa-map"></i> Peta
        </a>
        <a href="{{ route('table') }}" class="{{ request()->routeIs('table') ? 'active' : '' }}">
            <i class="fas fa-table"></i> Tabel
        </a>
    </div>
</nav>
