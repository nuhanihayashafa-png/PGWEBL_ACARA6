@extends('layouts.template')

@section('page-title', 'Edit Titik')

@push('styles')
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
            <h4>Edit Titik</h4>

            <form action="{{ route('points.update', $point->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="geometry_point" id="geometry_point" value="{{ $point->geom }}">

                <div class="mb-3">
                    <label>Nama Titik</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ $point->name }}" required>
                </div>

                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control">{{ $point->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label>
                        Posisi Titik
                        <span style="color:#888; font-size:0.8rem;">(geser penanda untuk ubah lokasi)</span>
                    </label>
                    <div id="edit-map"></div>
                    <p class="map-hint">Klik dan tahan penanda, lalu geser ke posisi baru.</p>
                    <div class="geom-display" id="geom-display">{{ $point->geom }}</div>
                </div>

                <div class="mb-3">
                    <label>
                        Foto Baru
                        <span style="color:#888; font-size:0.8rem;">(opsional)</span>
                    </label>
                    <input type="file" name="image" class="form-control"
                           accept="image/jpeg,image/png,image/jpg">
                </div>

                @if ($point->image)
                    <div class="mb-3">
                        <label>Foto Saat Ini</label><br>
                        <img src="{{ asset('storage/images/' . $point->image) }}"
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
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Ambil WKT dari controller: format POINT(lng lat)
        var rawWkt = "{{ $point->geom }}";

        var match = rawWkt.match(/POINT\s*\(\s*([\d.\-]+)\s+([\d.\-]+)\s*\)/i);
        if (!match) {
            console.error('Format WKT tidak dikenali:', rawWkt);
            return;
        }
        var lng = parseFloat(match[1]);
        var lat = parseFloat(match[2]);

        var map = L.map('edit-map').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        marker.on('drag', function (e) {
            var pos    = e.target.getLatLng();
            var newWkt = 'POINT(' + pos.lng.toFixed(7) + ' ' + pos.lat.toFixed(7) + ')';

            document.getElementById('geometry_point').value = newWkt;
            document.getElementById('geom-display').textContent = newWkt;
        });

        setTimeout(function () { map.invalidateSize(); }, 200);
    });
</script>
@endpush
