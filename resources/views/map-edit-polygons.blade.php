@extends('layouts.template')

@section('page-title', 'Edit Area')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <style>
        #edit-map {
            height: 350px;
            width: 100%;
            border-radius: 10px;
            border: 1px solid #D2B48C;
            z-index: 0;
        }
        .map-hint {
            font-size: 0.78rem;
            color: #888;
            margin-top: 5px;
        }
        .geom-display {
            font-size: 0.75rem;
            background: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px 10px;
            color: #555;
            word-break: break-all;
            margin-top: 6px;
        }
    </style>
@endpush

@section('content')
    <div style="display:flex; height:100vh;">
        <div style="width:100%; max-width:520px; padding:20px; overflow:auto; margin:auto;">
            <h4>Edit Area</h4>

            <form action="{{ route('polygons.update', $polygon->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="geometry_polygon" id="geometry_polygon"
                       value="{{ $polygon->geom }}">

                <div class="mb-3">
                    <label>Nama Area</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ $polygon->name }}" required>
                </div>

                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control">{{ $polygon->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label>
                        Batas Area
                        <span style="color:#888; font-size:0.8rem;">
                            (gunakan tombol edit di peta untuk mengubah bentuk area)
                        </span>
                    </label>
                    <div id="edit-map"></div>
                    <p class="map-hint">Klik ikon pensil di peta, lalu geser titik-titik sudut area ke posisi baru.</p>
                    <div class="geom-display" id="geom-display">Memuat koordinat...</div>
                </div>

                <div class="mb-3">
                    <label>
                        Foto Baru
                        <span style="color:#888; font-size:0.8rem;">(opsional)</span>
                    </label>
                    <input type="file" name="image" class="form-control"
                           accept="image/jpeg,image/png,image/jpg">
                </div>

                @if ($polygon->image)
                    <div class="mb-3">
                        <label>Foto Saat Ini</label><br>
                        <img src="{{ asset('storage/images/' . $polygon->image) }}"
                             style="max-height:180px; border-radius:8px; border:1px solid #D2B48C;">
                    </div>
                @endif

                <button type="submit" class="btn btn-primary w-100">Simpan</button>
                <a href="{{ route('map') }}" class="btn btn-secondary w-100 mt-2">Kembali</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Ambil data geom dari blade
            var rawGeom = @json($polygon->geom);

            // Normalkan ke object, tangani kemungkinan double-encode
            var geomObj;
            try {
                geomObj = (typeof rawGeom === 'string') ? JSON.parse(rawGeom) : rawGeom;
                if (typeof geomObj === 'string') geomObj = JSON.parse(geomObj);
            } catch (err) {
                console.error('Gagal parse geom polygon:', err);
                return;
            }

            // Hitung titik tengah dari koordinat pertama ring luar
            var coords    = geomObj.coordinates[0];
            var midIndex  = Math.floor(coords.length / 2);
            var centerLat = coords[midIndex][1];
            var centerLng = coords[midIndex][0];

            var map = L.map('edit-map').setView([centerLat, centerLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Render area dari GeoJSON
            var geojsonLayer = L.geoJSON(geomObj, {
                style: {
                    color: '#2E7D32',
                    fillColor: '#66BB6A',
                    fillOpacity: 0.3,
                    weight: 2
                }
            }).addTo(map);

            map.fitBounds(geojsonLayer.getBounds());

            // Leaflet.draw untuk edit vertex
            var drawnItems = new L.FeatureGroup();
            geojsonLayer.eachLayer(function (layer) {
                drawnItems.addLayer(layer);
            });
            map.addLayer(drawnItems);

            var drawControl = new L.Control.Draw({
                edit: {
                    featureGroup: drawnItems,
                    remove: false
                },
                draw: false
            });
            map.addControl(drawControl);

            // Setiap simpan edit, perbarui input hidden
            map.on(L.Draw.Event.EDITED, function (e) {
                e.layers.eachLayer(function (layer) {
                    var updatedGeom = JSON.stringify(layer.toGeoJSON().geometry);
                    document.getElementById('geometry_polygon').value = updatedGeom;
                    document.getElementById('geom-display').textContent = updatedGeom;
                });
            });

            // Tampilkan geom awal di bawah peta
            document.getElementById('geom-display').textContent = JSON.stringify(geomObj);

            setTimeout(function () { map.invalidateSize(); }, 200);
        });
    </script>
@endpush
