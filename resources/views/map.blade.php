@extends('layouts.template')

@section('styles')
    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
    {{-- Leaflet Draw --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
    {{-- Marker Cluster --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    {{-- Google Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Lora:ital,wght@0,400;0,500;1,400&display=swap"
        rel="stylesheet">

    <style>
        /* ═══ DRAW CONTROL PANEL ═══ */
        .draw-control-panel {
            background: var(--cream);
            border: 1.5px solid var(--copper);
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(92, 51, 23, 0.28);
            overflow: hidden;
            margin-top: 4px;
        }

        .draw-panel-header {
            background: linear-gradient(135deg, var(--bark), var(--saddle));
            color: var(--sand);
            font-family: 'Playfair Display', serif;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            padding: 5px 10px;
            border-bottom: 1px solid var(--gold);
        }

        .draw-panel-buttons {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .draw-panel-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            background: transparent;
            border: none;
            border-bottom: 1px solid var(--tan);
            color: var(--bark);
            font-family: 'Lora', serif;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.18s;
            text-align: left;
            width: 100%;
        }

        .draw-panel-btn:last-child {
            border-bottom: none;
        }

        .draw-panel-btn:hover {
            background: var(--dust);
            color: var(--saddle);
        }

        .draw-panel-btn .btn-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .draw-panel-btn .btn-dot.point {
            background: #C25A00;
        }

        .draw-panel-btn .btn-dot.line {
            background: none;
            border-top: 2.5px dashed #0068A5;
            border-radius: 0;
            width: 12px;
            height: 0;
        }

        .draw-panel-btn .btn-dot.polygon {
            background: rgba(46, 125, 50, 0.25);
            border: 2px solid #2E7D32;
            border-radius: 2px;
        }

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
            margin: 0;
            padding: 0;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: 'Lora', Georgia, serif;
            background: var(--cream);
            color: var(--bark);
        }

        /* ═══ SHELL ═══ */
        .map-shell {
            display: flex;
            width: 100vw;
            height: calc(100vh - 60px);
            overflow: hidden;
            position: relative;
        }

        /* ═══ SIDEBAR ═══ */
        .map-sidebar {
            width: 280px;
            min-width: 280px;
            background: var(--dust);
            border-right: 2px solid var(--copper);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: width 0.3s ease, min-width 0.3s ease;
            z-index: 100;
            flex-shrink: 0;
        }

        .map-sidebar.collapsed {
            width: 0;
            min-width: 0;
            border: none;
        }

        .sidebar-header {
            background: linear-gradient(160deg, var(--bark), var(--saddle));
            color: var(--sand);
            padding: 13px 16px;
            font-family: 'Playfair Display', serif;
            font-size: 0.92rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid var(--gold);
            flex-shrink: 0;
            white-space: nowrap;
        }

        .sidebar-header i {
            color: var(--gold);
        }

        .sidebar-body {
            overflow-y: auto;
            flex: 1;
            padding: 12px;
        }

        .sidebar-body::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-body::-webkit-scrollbar-thumb {
            background: var(--copper);
            border-radius: 4px;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: var(--cream);
            border: 1.5px solid var(--copper);
            border-radius: 6px;
            padding: 6px 10px;
            margin-bottom: 10px;
            gap: 6px;
        }

        .search-box i {
            color: var(--ash);
            font-size: 0.78rem;
            flex-shrink: 0;
        }

        .search-box input {
            border: none;
            background: transparent;
            font-family: 'Lora', serif;
            font-size: 0.8rem;
            color: var(--bark);
            outline: none;
            width: 100%;
        }

        .search-box input::placeholder {
            color: var(--ash);
        }

        .filter-pills {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .pill {
            font-size: 0.7rem;
            font-family: 'Lora', serif;
            padding: 3px 9px;
            border-radius: 20px;
            border: 1.5px solid var(--copper);
            background: transparent;
            color: var(--saddle);
            cursor: pointer;
            transition: all 0.18s;
            line-height: 1.4;
        }

        .pill:hover,
        .pill.active {
            background: var(--saddle);
            color: var(--cream);
            border-color: var(--saddle);
        }

        .pill-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            margin-right: 3px;
            vertical-align: middle;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .feature-card {
            background: var(--cream);
            border: 1px solid var(--tan);
            border-radius: 7px;
            padding: 9px 11px;
            cursor: pointer;
            transition: all 0.18s;
            border-left: 3px solid var(--copper);
        }

        .feature-card:hover {
            border-left-color: var(--gold);
            background: var(--sand);
            transform: translateX(2px);
        }

        .fc-name {
            font-family: 'Playfair Display', serif;
            font-size: 0.83rem;
            font-weight: 700;
            color: var(--bark);
        }

        .fc-meta {
            font-size: 0.7rem;
            color: var(--ash);
            margin-top: 2px;
        }

        .fc-badge {
            font-size: 0.62rem;
            padding: 1px 6px;
            border-radius: 10px;
            border: 1px solid currentColor;
            float: right;
            margin-top: 2px;
        }

        .badge-point {
            color: #C25A00;
        }

        .badge-line {
            color: #0068A5;
        }

        .badge-polygon {
            color: #2E7D32;
        }

        .stat-strip {
            border-top: 1px solid var(--tan);
            padding: 9px 12px;
            display: flex;
            justify-content: space-around;
            background: var(--cream);
            flex-shrink: 0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--saddle);
        }

        .stat-label {
            font-size: 0.62rem;
            color: var(--ash);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        /* ═══ SIDEBAR TOGGLE ═══ */
        #sidebar-toggle {
            position: absolute;
            top: 50%;
            left: 280px;
            transform: translateY(-50%);
            z-index: 500;
            width: 18px;
            height: 44px;
            background: var(--saddle);
            border: none;
            border-radius: 0 7px 7px 0;
            color: var(--cream);
            font-size: 0.6rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: left 0.3s ease, background 0.18s;
            box-shadow: 2px 0 8px rgba(92, 51, 23, 0.3);
        }

        #sidebar-toggle:hover {
            background: var(--bark);
        }

        /* ═══ MAP WRAP ═══ */
        .map-wrap {
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        /* ═══ FLOATING TOOLBAR ═══ */
        .map-toolbar {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .tb-btn {
            width: 34px;
            height: 34px;
            background: var(--cream);
            border: 1.5px solid var(--copper);
            border-radius: 6px;
            color: var(--saddle);
            font-size: 0.82rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.18s;
            box-shadow: 0 2px 6px rgba(92, 51, 23, 0.22);
        }

        .tb-btn:hover {
            background: var(--saddle);
            color: var(--cream);
        }

        .tb-btn.active {
            background: var(--gold);
            color: var(--cream);
            border-color: var(--gold);
        }

        .tb-divider {
            height: 1px;
            background: var(--tan);
            margin: 2px 0;
        }

        /* ═══ COORD BAR ═══ */
        .coord-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(92, 51, 23, 0.88);
            color: var(--sand);
            font-family: 'Courier New', monospace;
            font-size: 0.7rem;
            padding: 4px 12px;
            display: flex;
            align-items: center;
            gap: 16px;
            border-top: 1px solid var(--gold);
        }

        .coord-bar i {
            color: var(--gold);
            margin-right: 3px;
        }

        #measureResult {
            margin-left: auto;
            display: none;
            align-items: center;
            gap: 6px;
        }

        #measureResult button {
            background: none;
            border: none;
            color: var(--gold);
            cursor: pointer;
            font-size: 0.7rem;
        }

        /* ═══ MINIMAP ═══ */
        #minimap {
            position: absolute;
            bottom: 32px;
            right: 10px;
            width: 130px;
            height: 90px;
            border: 2px solid var(--copper);
            border-radius: 8px;
            z-index: 1000;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(92, 51, 23, 0.35);
        }

        #minimap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.82;
            display: block;
        }

        #minimap-label {
            position: absolute;
            top: 4px;
            left: 6px;
            font-size: 0.58rem;
            font-family: 'Playfair Display', serif;
            color: var(--bark);
            background: rgba(253, 246, 227, 0.85);
            padding: 1px 5px;
            border-radius: 3px;
            pointer-events: none;
        }

        /* ═══ LEAFLET OVERRIDES ═══ */
        .leaflet-control-zoom a,
        .leaflet-control-layers-toggle {
            background-color: var(--cream) !important;
            color: var(--saddle) !important;
            border-color: var(--copper) !important;
        }

        .leaflet-control-zoom a:hover,
        .leaflet-control-layers-toggle:hover {
            background-color: var(--saddle) !important;
            color: var(--cream) !important;
        }

        .leaflet-control-layers {
            border: 1.5px solid var(--copper) !important;
            border-radius: 8px !important;
            font-family: 'Lora', serif !important;
            font-size: 0.8rem !important;
            background: var(--cream) !important;
            color: var(--bark) !important;
            box-shadow: 0 4px 14px rgba(92, 51, 23, 0.28) !important;
        }

        .leaflet-control-layers-separator {
            border-top: 1px solid var(--tan) !important;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 12px !important;
            border: 1.5px solid var(--copper) !important;
            box-shadow: 0 6px 20px rgba(92, 51, 23, 0.35) !important;
            font-family: 'Lora', serif !important;
            padding: 4px !important;
        }

        .leaflet-popup-tip {
            background: var(--cream) !important;
        }

        .leaflet-popup-content {
            margin: 12px 14px !important;
            font-size: 13px !important;
        }

        .leaflet-control-attribution {
            font-size: 0.6rem !important;
            background: rgba(253, 246, 227, 0.85) !important;
        }

        /* ═══ POPUP ═══ */
        .popup-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--bark);
            margin-bottom: 4px;
        }

        .popup-desc {
            font-size: 0.78rem;
            color: var(--ash);
            line-height: 1.45;
            margin-bottom: 6px;
        }

        .popup-badge {
            display: inline-block;
            font-size: 0.65rem;
            padding: 2px 8px;
            border-radius: 10px;
            border: 1px solid var(--copper);
            color: var(--saddle);
            margin-top: 2px;
        }

        .popup-img {
            width: 100%;
            margin-top: 8px;
            border-radius: 8px;
            border: 1px solid var(--tan);
            object-fit: cover;
            max-height: 160px;
        }

        /* ═══ MODAL ═══ */
        .modal-content {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(92, 51, 23, 0.3) !important;
            font-family: 'Lora', serif;
        }

        .modal-header-cowboy {
            background: linear-gradient(135deg, var(--bark), var(--saddle));
            color: var(--cream);
            border-bottom: 2px solid var(--gold);
            padding: 1rem 1.4rem;
        }

        .modal-header-cowboy .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .modal-header-cowboy .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
            opacity: 0.8;
        }

        .modal-header-cowboy .btn-close:hover {
            opacity: 1;
        }

        .modal-body {
            padding: 1.3rem 1.4rem;
            background-color: var(--cream);
        }

        .modal-footer {
            background-color: var(--dust);
            border-top: 1px solid var(--tan);
            padding: 0.85rem 1.4rem;
        }

        .form-label {
            color: var(--ash);
            font-size: 0.82rem;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .form-control,
        .input-group-text {
            font-family: 'Lora', serif;
            font-size: 0.83rem;
            border-color: var(--tan);
            background-color: var(--cream);
            color: var(--bark);
        }

        .form-control:focus {
            border-color: var(--copper);
            box-shadow: 0 0 0 0.2rem rgba(184, 115, 51, 0.18);
            background: var(--cream);
            color: var(--bark);
        }

        .input-group-text {
            background: var(--dust) !important;
            color: var(--ash) !important;
        }

        /* Pindahkan Leaflet Draw toolbar ke kanan */
        .leaflet-draw.leaflet-control {
            margin-top: 4px;
        }

        .leaflet-top.leaflet-right .leaflet-draw.leaflet-control {
            display: block !important;
        }

        .leaflet-top.leaflet-left .leaflet-draw.leaflet-control {
            display: none !important;
        }

        .form-control.bg-light {
            background: var(--dust) !important;
            color: var(--ash) !important;
        }

        .btn-cowboy {
            background: linear-gradient(135deg, var(--bark), var(--saddle));
            color: var(--cream);
            border: none;
            border-radius: 7px;
            padding: 7px 18px;
            font-family: 'Lora', serif;
            font-size: 0.82rem;
            transition: all 0.2s ease;
        }

        .btn-cowboy:hover {
            background: linear-gradient(135deg, #3a1f0a, var(--bark));
            color: var(--cream);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(92, 51, 23, 0.35);
        }

        .btn-outline-cowboy {
            background: transparent;
            color: var(--saddle);
            border: 1.5px solid var(--copper);
            border-radius: 7px;
            padding: 7px 18px;
            font-family: 'Lora', serif;
            font-size: 0.82rem;
            transition: all 0.18s;
        }

        .btn-outline-cowboy:hover {
            background: var(--tan);
            color: var(--bark);
        }

        .img-preview-box {
            margin-top: 8px;
            display: none;
        }

        .img-preview-box img {
            border-radius: 8px;
            border: 1px solid var(--tan);
            max-width: 100%;
            max-height: 180px;
            object-fit: contain;
        }
    </style>
@endsection


@section('content')
    <div class="map-shell" id="mapShell">

        {{-- ── SIDEBAR ── --}}
        <aside class="map-sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-layer-group"></i> Inventarisasi
            </div>
            <div class="sidebar-body">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="sideSearch" placeholder="Cari lokasi...">
                </div>
                <div class="filter-pills">
                    <button class="pill active" data-filter="all">Semua</button>
                    <button class="pill" data-filter="Point"><span class="pill-dot"
                            style="background:#C25A00"></span>Titik</button>
                    <button class="pill" data-filter="LineString"><span class="pill-dot"
                            style="background:#0068A5"></span>Garis</button>
                    <button class="pill" data-filter="Polygon"><span class="pill-dot"
                            style="background:#2E7D32"></span>Area</button>
                </div>
                <div class="feature-list" id="featureList">
                    <div style="text-align:center;color:var(--ash);font-size:0.8rem;padding:20px 0">
                        <i class="fas fa-spinner fa-spin"></i><br>Memuat data…
                    </div>
                </div>
            </div>
            <div class="stat-strip">
                <div class="stat-item">
                    <div class="stat-num" id="statPoint">0</div>
                    <div class="stat-label">Titik</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num" id="statLine">0</div>
                    <div class="stat-label">Garis</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num" id="statPoly">0</div>
                    <div class="stat-label">Area</div>
                </div>
            </div>
        </aside>

        {{-- ── SIDEBAR TOGGLE ── --}}
        <button id="sidebar-toggle" title="Sembunyikan/tampilkan panel">
            <i class="fas fa-chevron-left" id="toggleIcon"></i>
        </button>

        {{-- ── MAP WRAP ── --}}
        <div class="map-wrap">
            <div id="map"></div>

            <div class="map-toolbar">
                <button class="tb-btn" id="btnHome" title="Posisi awal"><i class="fas fa-house"></i></button>
                <div class="tb-divider"></div>
                <button class="tb-btn active" id="btnCluster" title="Toggle kluster"><i
                        class="fas fa-circle-nodes"></i></button>
                <button class="tb-btn" id="btnMeasure" title="Ukur jarak"><i class="fas fa-ruler-combined"></i></button>
                <button class="tb-btn" id="btnFitAll" title="Tampilkan semua data"><i class="fas fa-maximize"></i></button>
                <button class="tb-btn" id="btnFullscreen" title="Layar penuh"><i class="fas fa-expand"></i></button>
            </div>

            <div id="minimap" title="Klik untuk kembali ke posisi awal">
                <img src="https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/7/63/107"
                    alt="Overview">
                <div id="minimap-label">Overview</div>
            </div>

            <div class="coord-bar">
                <span><i class="fas fa-crosshairs"></i>Lat: <span id="coordLat">—</span></span>
                <span><i class="fas fa-crosshairs"></i>Lng: <span id="coordLng">—</span></span>
                <span><i class="fas fa-search-plus"></i>Zoom: <span id="coordZoom">—</span></span>
                <span id="measureResult">
                    <i class="fas fa-ruler" style="color:var(--gold)"></i>
                    <span id="measureVal">0 m</span>
                    <button onclick="endMeasure()">✕ Selesai</button>
                </span>
            </div>
        </div>
    </div>


    {{-- ══════════════ MODAL POINT ══════════════ --}}
    <div class="modal fade" id="modalInputPoint" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-cowboy">
                    <h5 class="modal-title"><i class="fas fa-location-dot me-2"></i>Input Data Titik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-tag me-1"></i>Nama Titik</label>
                            <input type="text" class="form-control" name="name"
                                placeholder="E.g., Candi Prambanan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-align-left me-1"></i>Deskripsi</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Tambahkan keterangan..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-map-pin me-1"></i>Geometri (WKT)</label>
                            <input type="text" class="form-control bg-light" id="geometry_point"
                                name="geometry_point" readonly>
                        </div>
                        <div class="mb-2">
                            <label class="form-label"><i class="fas fa-image me-1"></i>Foto Lokasi</label>
                            <input class="form-control" type="file" id="point_image" name="image"
                                accept="image/jpeg,image/png,image/jpg"
                                onchange="previewImage(event,'preview-image-point','point-preview-wrapper')">
                            <div class="img-preview-box" id="point-preview-wrapper">
                                <img id="preview-image-point" src="" alt="Preview">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-cowboy" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-cowboy"><i
                                class="fas fa-floppy-disk me-1"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════ MODAL POLYLINE ══════════════ --}}
    <div class="modal fade" id="modalInputPolylines" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-cowboy">
                    <h5 class="modal-title"><i class="fas fa-route me-2"></i>Input Data Garis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('polylines.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-tag me-1"></i>Nama Garis</label>
                            <input type="text" class="form-control" name="name"
                                placeholder="E.g., Jalan Malioboro">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-align-left me-1"></i>Deskripsi</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Tambahkan keterangan..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-map me-1"></i>Geometri (WKT)</label>
                            <input type="text" class="form-control bg-light" id="geometry_polyline"
                                name="geometry_polyline" readonly>
                        </div>
                        <div class="mb-2">
                            <label class="form-label"><i class="fas fa-image me-1"></i>Foto Lokasi</label>
                            <input class="form-control" type="file" id="polyline_image" name="image"
                                accept="image/jpeg,image/png,image/jpg"
                                onchange="previewImage(event,'preview-image-polyline','polyline-preview-wrapper')">
                            <div class="img-preview-box" id="polyline-preview-wrapper">
                                <img id="preview-image-polyline" src="" alt="Preview">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-cowboy" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-cowboy"><i
                                class="fas fa-floppy-disk me-1"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════ MODAL POLYGON ══════════════ --}}
    <div class="modal fade" id="modalInputPolygons" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-cowboy">
                    <h5 class="modal-title"><i class="fas fa-draw-polygon me-2"></i>Input Data Area</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('polygons.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-tag me-1"></i>Nama Area</label>
                            <input type="text" class="form-control" name="name" placeholder="E.g., Kawasan UGM">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-align-left me-1"></i>Deskripsi</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Tambahkan keterangan..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-map me-1"></i>Geometri (WKT)</label>
                            <input type="text" class="form-control bg-light" id="geometry_polygons"
                                name="geometry_polygons" readonly>
                        </div>
                        <div class="mb-2">
                            <label class="form-label"><i class="fas fa-image me-1"></i>Foto Lokasi</label>
                            <input class="form-control" type="file" id="polygon_image" name="image"
                                accept="image/jpeg,image/png,image/jpg"
                                onchange="previewImage(event,'preview-image-polygon','polygon-preview-wrapper')">
                            <div class="img-preview-box" id="polygon-preview-wrapper">
                                <img id="preview-image-polygon" src="" alt="Preview">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-cowboy" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-cowboy"><i
                                class="fas fa-floppy-disk me-1"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('toast')
@endsection


@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js"></script>
    {{-- Wicket.js sangat berguna untuk mengubah GeoJSON (dari Leaflet Draw) menjadi WKT (untuk PostGIS) --}}

    <script>
        // ==========================================
        // 1. FUNGSI PREVIEW GAMBAR
        // ==========================================
        function previewImage(event, imgId, wrapperId) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById(imgId);
                output.src = reader.result;
                document.getElementById(wrapperId).style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        // ==========================================
        // 2. INISIALISASI PETA
        // ==========================================
        // Set view default ke Yogyakarta (sesuaikan jika perlu)
        var map = L.map('map').setView([-7.7956, 110.3695], 13);

        // Basemap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // ==========================================
        // 3. MEMANGGIL DATA DARI API CONTROLLER
        // ==========================================
        var markers = L.markerClusterGroup(); // Menggunakan MarkerCluster

        fetch("{{ route('api.map.data') }}")
            .then(response => response.json())
            .then(data => {
                var geoJsonLayer = L.geoJSON(data, {
                    style: function(feature) {
                        // Styling berdasarkan tipe geometri mencocokkan warna UI-mu
                        switch (feature.geometry.type) {
                            case 'LineString':
                                return {
                                    color: "#0068A5", weight: 4
                                };
                            case 'Polygon':
                                return {
                                    color: "#2E7D32", fillColor: "#2E7D32", fillOpacity: 0.4
                                };
                        }
                    },
                    onEachFeature: function(feature, layer) {
                        // Menyiapkan konten Popup
                        var imgTag = feature.properties.image ?
                            `<img src="/storage/images/${feature.properties.image}" class="popup-img">` :
                            '';
                        var typeBadge = feature.geometry.type;

                        var popupContent = `
                            <div class="popup-title">${feature.properties.name}</div>
                            <span class="popup-badge badge-${typeBadge.toLowerCase()}">${typeBadge}</span>
                            <div class="popup-desc mt-2">${feature.properties.description || 'Tidak ada deskripsi.'}</div>
                            ${imgTag}
                        `;
                        layer.bindPopup(popupContent);
                    }
                });

                markers.addLayer(geoJsonLayer);
                map.addLayer(markers);
            })
            .catch(error => console.error('Error fetching map data:', error));

        // ==========================================
        // 4. INISIALISASI LEAFLET DRAW
        // ==========================================
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            position: 'topright', // ← UBAH INI
            edit: {
                featureGroup: drawnItems
            },
            draw: {
                circle: false,
                circlemarker: false,
                rectangle: false,
                marker: true,
                polyline: true,
                polygon: true
            }
        });
        map.addControl(drawControl);

        // Event ketika selesai menggambar di peta
        // Fungsi konversi manual GeoJSON → WKT (ganti Wicket)
        function toWKT(layer) {
            var geojson = layer.toGeoJSON().geometry;
            var type = geojson.type;
            var coords = geojson.coordinates;

            if (type === 'Point') {
                return 'POINT (' + coords[0] + ' ' + coords[1] + ')';
            }

            if (type === 'LineString') {
                var pts = coords.map(function(c) {
                    return c[0] + ' ' + c[1];
                }).join(', ');
                return 'LINESTRING (' + pts + ')';
            }

            if (type === 'Polygon') {
                var rings = coords.map(function(ring) {
                    return '(' + ring.map(function(c) {
                        return c[0] + ' ' + c[1];
                    }).join(', ') + ')';
                }).join(', ');
                return 'POLYGON (' + rings + ')';
            }

            return '';
        }

        // Event: selesai menggambar → buka modal
        map.on(L.Draw.Event.CREATED, function(e) {
            var type = e.layerType;
            var layer = e.layer;
            drawnItems.addLayer(layer);

            var wktString = toWKT(layer);

            if (type === 'marker') {
                $('#geometry_point').val(wktString);
                $('#modalInputPoint').modal('show');
            } else if (type === 'polyline') {
                $('#geometry_polyline').val(wktString);
                $('#modalInputPolylines').modal('show');
            } else if (type === 'polygon') {
                $('#geometry_polygons').val(wktString);
                $('#modalInputPolygons').modal('show');
            }
        });

        // Hapus layer gambar dari peta jika modal ditutup (batal simpan)
        $('.modal').on('hidden.bs.modal', function() {
            drawnItems.clearLayers();
        });

        // ==========================================
        // 5. KODE UNTUK MENGAKTIFKAN UI & FITUR KUSTOM
        // ==========================================

        // Pelacak Koordinat & Zoom (Kanan Bawah)
        map.on('mousemove', function(e) {
            $('#coordLat').text(e.latlng.lat.toFixed(5));
            $('#coordLng').text(e.latlng.lng.toFixed(5));
        });
        map.on('zoomend', function() {
            $('#coordZoom').text(map.getZoom());
        });
        $('#coordZoom').text(map.getZoom()); // Set nilai awal saat halaman dimuat

        // Tombol Toggle Sidebar (Kiri)
        $('#sidebar-toggle').click(function() {
            $('#sidebar').toggleClass('collapsed');
            var icon = $('#toggleIcon');

            if ($('#sidebar').hasClass('collapsed')) {
                icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
                $(this).css('left', '0');
            } else {
                icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
                $(this).css('left', '280px');
            }

            // Memberi tahu Leaflet bahwa ukuran container berubah agar peta tidak error
            setTimeout(function() {
                map.invalidateSize();
            }, 300);
        });

        // Tombol Home & Minimap
        var homeCoords = [-7.7956, 110.3695]; // Sesuaikan dengan koordinat awalmu
        var homeZoom = 13;

        $('#btnHome').click(function() {
            map.flyTo(homeCoords, homeZoom);
        });

        $('#minimap').click(function() {
            map.flyTo(homeCoords, homeZoom);
        });

        // Tombol Fullscreen
        $('#btnFullscreen').click(function() {
            var mapContainer = document.getElementById("mapShell");
            if (!document.fullscreenElement) {
                mapContainer.requestFullscreen().catch(err => {
                    console.log(`Error attempting to enable fullscreen: ${err.message}`);
                });
                $(this).html('<i class="fas fa-compress"></i>');
            } else {
                document.exitFullscreen();
                $(this).html('<i class="fas fa-expand"></i>');
            }
        });

        // Tombol Tampilkan Semua Data (Fit All)
        $('#btnFitAll').click(function() {
            // Memastikan data marker sudah ada sebelum melakukan fit bounds
            if (markers.getLayers().length > 0) {
                map.fitBounds(markers.getBounds());
            } else {
                alert("Belum ada data di peta!");
            }
        });
    </script>
@endsection
