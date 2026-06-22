@extends('layouts.template')

@section('page-title', 'Edit Garis')

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
            <h4>Edit Garis</h4>

            <form action="{{ route('polylines.update', $polylines->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="geometry_polylines" id="geometry_polylines"
                       value="{{ $polylines->geom }}">

                <div class="mb-3">
                    <label>Nama Garis</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ $polylines->name }}" required>
                </div>

                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control">{{ $polylines->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label>
                        Posisi Garis
                        <span style="color:#888; font-size:0.8rem;">
                            (gunakan tombol edit di peta untuk mengubah jalur garis)
                        </span>
                    </label>
                    <div id="edit-map"></div>
                    <p class="map-hint">Klik ikon pensil di peta, lalu geser titik-titik garis ke posisi baru.</p>
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

                @if ($polylines->image)
                    <div class="mb-3">
                        <label>Foto Saat Ini</label><br>
                        <img src="{{ asset('storage/images/' . $polylines->image) }}"
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
            var rawGeom = @json($polylines->geom);

            // Normalkan ke object
            var geomObj;
            try {
                geomObj = (typeof rawGeom === 'string') ? JSON.parse(rawGeom) : rawGeom;
                if (typeof geomObj === 'string') geomObj = JSON.parse(geomObj);
            } catch (err) {
                console.error('Gagal parse geom polyline:', err);
                return;
            }

            // Hitung titik tengah LineString untuk setView()
            var coords    = geomObj.coordinates;
            var midIndex  = Math.floor(coords.length / 2);
            var centerLat = coords[midIndex][1];
            var centerLng = coords[midIndex][0];

            var map = L.map('edit-map').setView([centerLat, centerLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Render garis dari GeoJSON
            var geojsonLayer = L.geoJSON(geomObj, {
                style: { color: '#e74c3c', weight: 3 }
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
                    document.getElementById('geometry_polylines').value = updatedGeom;
                    document.getElementById('geom-display').textContent = updatedGeom;
                });
            });

            // Tampilkan geom awal di bawah peta
            document.getElementById('geom-display').textContent = JSON.stringify(geomObj);

            setTimeout(function () { map.invalidateSize(); }, 200);
        });
    </script>
@endpush
