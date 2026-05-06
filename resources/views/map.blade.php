@extends('layouts.template')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Lora:ital,wght@0,400;0,500;1,400&display=swap"
        rel="stylesheet">

    <style>
        body,
        html {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #map {
            height: calc(100vh - 60px);
            width: 100%;
        }

        /* ═══ MODAL ═══ */
        .modal-content {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(92, 51, 23, 0.3);
            font-family: 'Lora', Georgia, serif;
        }

        .modal-header {
            background: linear-gradient(135deg, #5C3317, #8B4513);
            color: #FDF6E3;
            border-bottom: 2px solid #C8860A;
            padding: 1rem 1.4rem;
        }

        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
            opacity: 0.8;
        }

        .modal-body {
            background-color: #FDF6E3;
            padding: 1.3rem 1.4rem;
        }

        .modal-footer {
            background-color: #E8D5B0;
            border-top: 1px solid #D2B48C;
            padding: 0.85rem 1.4rem;
        }

        .modal label {
            color: #6B5C4E;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
        }

        .modal .form-control {
            font-family: 'Lora', Georgia, serif;
            font-size: 0.83rem;
            border-color: #D2B48C;
            background-color: #FDF6E3;
            color: #5C3317;
            border-radius: 6px;
        }

        .modal .form-control:focus {
            border-color: #B87333;
            box-shadow: 0 0 0 0.2rem rgba(184, 115, 51, 0.2);
            background-color: #FDF6E3;
            color: #5C3317;
        }

        #geometry_point,
        #geometry_polyline,
        #geometry_polygon {
            background-color: #E8D5B0 !important;
            color: #6B5C4E !important;
            font-size: 0.75rem;
            resize: none;
            height: 58px;
        }

        .img-preview {
            border-radius: 8px;
            border: 1px solid #D2B48C;
            margin-top: 8px;
            max-height: 180px;
            width: 100%;
            object-fit: cover;
            display: none;
        }

        .modal-footer .btn-secondary {
            background: transparent;
            color: #8B4513;
            border: 1.5px solid #B87333;
            border-radius: 7px;
            font-family: 'Lora', serif;
            font-size: 0.82rem;
            padding: 6px 16px;
            transition: all 0.18s;
        }

        .modal-footer .btn-secondary:hover {
            background: #D2B48C;
            color: #5C3317;
        }

        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #5C3317, #8B4513);
            border: none;
            border-radius: 7px;
            font-family: 'Lora', serif;
            font-size: 0.82rem;
            padding: 6px 16px;
            color: #FDF6E3;
            transition: all 0.2s;
        }

        .modal-footer .btn-primary:hover {
            background: linear-gradient(135deg, #3a1f0a, #5C3317);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(92, 51, 23, 0.35);
        }

        .popup-img-box {
            width: 100%;
            max-height: 160px;
            object-fit: cover;
            border-radius: 6px;
            margin-top: 6px;
            border: 1px solid #D2B48C;
        }

        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px #FDF6E3 inset !important;
            -webkit-text-fill-color: #5C3317 !important;
        }
    </style>
@endsection


@section('content')
    <div id="map"></div>

    {{-- ══════ MODAL POINT ══════ --}}
    <div class="modal fade" tabindex="-1" id="modalInputPoint">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📍 Input Data Titik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('points.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Titik</label>
                            <input type="text" class="form-control" name="name" placeholder="E.g., Tugu Jogja"
                                required>
                        </div>
                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Tambahkan keterangan..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Geometri (WKT)</label>
                            <textarea class="form-control" id="geometry_point" name="geometry_point" readonly></textarea>
                        </div>
                        <div class="mb-2">
                            <label>Foto Lokasi</label>
                            <input type="file" class="form-control" name="image"
                                accept="image/jpeg,image/png,image/jpg" onchange="showPreview(this, 'prev-point')">
                            <img id="prev-point" class="img-preview" src="" alt="Preview">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">💾 Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════ MODAL POLYLINE ══════ --}}
    <div class="modal fade" tabindex="-1" id="modalInputPolyline">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📏 Input Data Garis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('polylines.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Garis</label>
                            <input type="text" class="form-control" name="name" placeholder="E.g., Jalan Malioboro"
                                required>
                        </div>
                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Tambahkan keterangan..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Geometri (WKT)</label>
                            <textarea class="form-control" id="geometry_polyline" name="geometry_polyline" readonly></textarea>
                        </div>
                        <div class="mb-2">
                            <label>Foto Lokasi</label>
                            <input type="file" class="form-control" name="image"
                                accept="image/jpeg,image/png,image/jpg" onchange="showPreview(this, 'prev-polyline')">
                            <img id="prev-polyline" class="img-preview" src="" alt="Preview">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">💾 Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════ MODAL POLYGON ══════ --}}
    <div class="modal fade" tabindex="-1" id="modalInputPolygon">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🟦 Input Data Area</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('polygons.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Area</label>
                            <input type="text" class="form-control" name="name" placeholder="E.g., Kawasan UGM"
                                required>
                        </div>
                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Tambahkan keterangan..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Geometri (WKT)</label>
                            <textarea class="form-control" id="geometry_polygon" name="geometry_polygon" readonly></textarea>
                        </div>
                        <div class="mb-2">
                            <label>Foto Lokasi</label>
                            <input type="file" class="form-control" name="image"
                                accept="image/jpeg,image/png,image/jpg" onchange="showPreview(this, 'prev-polygon')">
                            <img id="prev-polygon" class="img-preview" src="" alt="Preview">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">💾 Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://unpkg.com/@terraformer/wkt"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        // ══════════════════════════════════════
        // PREVIEW GAMBAR
        // ══════════════════════════════════════
        function showPreview(input, previewId) {
            var file = input.files[0];
            if (!file) return;
            var img = document.getElementById(previewId);
            img.src = window.URL.createObjectURL(file);
            img.style.display = 'block';
        }

        // Reset preview & file input saat modal ditutup
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                modal.querySelectorAll('.img-preview').forEach(function(img) {
                    img.src = '';
                    img.style.display = 'none';
                });
                modal.querySelectorAll('input[type=file]').forEach(function(inp) {
                    inp.value = '';
                });
            });
        });

        // ══════════════════════════════════════
        // INISIALISASI PETA
        // ══════════════════════════════════════
        var map = L.map('map').setView([-7.7956, 110.3695], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // ══════════════════════════════════════
        // LEAFLET DRAW
        // ══════════════════════════════════════
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            draw: {
                polyline: true,
                polygon: true,
                rectangle: false,
                circle: false,
                marker: true,
                circlemarker: false
            },
            edit: false
        });
        map.addControl(drawControl);

        map.on('draw:created', function(e) {
            var layer = e.layer;
            var type = e.layerType;
            var wkt = Terraformer.geojsonToWKT(layer.toGeoJSON().geometry);

            drawnItems.addLayer(layer);

            if (type === 'marker') {
                $('#geometry_point').val(wkt);
                new bootstrap.Modal(document.getElementById('modalInputPoint')).show();
            } else if (type === 'polyline') {
                $('#geometry_polyline').val(wkt);
                new bootstrap.Modal(document.getElementById('modalInputPolyline')).show();
            } else if (type === 'polygon') {
                $('#geometry_polygon').val(wkt);
                new bootstrap.Modal(document.getElementById('modalInputPolygon')).show();
            }
        });

        // Bersihkan layer gambar saat modal ditutup tanpa simpan
        $('#modalInputPoint, #modalInputPolyline, #modalInputPolygon').on('hidden.bs.modal', function() {
            drawnItems.clearLayers();
        });

        // ══════════════════════════════════════
        // BUILD POPUP
        // ══════════════════════════════════════
        function buildPopup(feature, type) {
            // 1. Siapkan elemen gambar
            var img = feature.properties.image ?
                "<img src='{{ asset('storage/images') }}/" + feature.properties.image + "' class='popup-img-box'>" :
                '';

            // 2. Siapkan rute delete
            var routeDel = '';
            if (type === 'point') routeDel = "{{ route('points.delete', ':id') }}".replace(':id', feature.properties.id);
            if (type === 'polyline') routeDel = "{{ route('polylines.delete', ':id') }}".replace(':id', feature.properties
                .id);
            if (type === 'polygon') routeDel = "{{ route('polygons.delete', ':id') }}".replace(':id', feature.properties
            .id);

            // 3. Format waktu dibuat (created_at) menjadi lebih rapi
            var waktuDibuat = 'Tidak diketahui';
            if (feature.properties.created_at) {
                var dateObj = new Date(feature.properties.created_at);
                waktuDibuat = dateObj.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // 4. Rakit seluruh konten HTML
            return "<div style='min-width:210px; font-family:Lora,serif'>" +
                // Judul
                "<div style='text-align:center; border-bottom:1px solid #D2B48C; padding-bottom:6px; margin-bottom:8px;'>" +
                "<b style='font-family:Playfair Display,serif; font-size:1.05rem; color:#5C3317'>" +
                (feature.properties.name || 'Tanpa Nama') +
                "</b>" +
                "</div>" +
                // Gambar
                img +
                // Deskripsi
                "<div style='margin-top:8px; color:#6B5C4E; font-size:0.85rem; line-height:1.4;'>" +
                (feature.properties.description || '<i>Tidak ada deskripsi</i>') +
                "</div>" +
                // Waktu Dibuat
                "<div style='margin-top:8px; color:#8B4513; font-size:0.75rem;'>" +
                "🕒 <small>Dibuat: " + waktuDibuat + "</small>" +
                "</div>" +
                // Tombol Delete
                "<form action='" + routeDel + "' method='post' style='margin-top:12px; text-align:center;'>" +
                "<input type='hidden' name='_token' value='{{ csrf_token() }}'>" +
                "<input type='hidden' name='_method' value='DELETE'>" +
                "<button type='submit' class='btn btn-sm' style='background:#8B4513; color:#FDF6E3; width:100%; font-size:0.8rem; border-radius:6px;' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">" +
                "🗑 Hapus Data" +
                "</button>" +
                "</form>" +
                "</div>";
        }

        // ══════════════════════════════════════
        // LOAD GEOJSON
        // ══════════════════════════════════════
        var points = L.geoJSON(null, {
            pointToLayer: function(feature, latlng) {
                return L.circleMarker(latlng, {
                    radius: 7,
                    color: '#C25A00',
                    fillColor: '#FF8C42',
                    fillOpacity: 0.9,
                    weight: 2
                });
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup(buildPopup(feature, 'point'));
            }
        });
        $.getJSON("{{ route('geojson.points') }}", function(data) {
            points.addData(data);
            map.addLayer(points);
        });

        var polylines = L.geoJSON(null, {
            style: {
                color: '#0068A5',
                weight: 3,
                opacity: 0.9,
                dashArray: '8 4'
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup(buildPopup(feature, 'polyline'));
            }
        });
        $.getJSON("{{ route('geojson.polylines') }}", function(data) {
            polylines.addData(data);
            map.addLayer(polylines);
        });

        var polygons = L.geoJSON(null, {
            style: {
                color: '#2E7D32',
                fillColor: '#66BB6A',
                fillOpacity: 0.25,
                weight: 2
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup(buildPopup(feature, 'polygon'));
            }
        });
        $.getJSON("{{ route('geojson.polygons') }}", function(data) {
            polygons.addData(data);
            map.addLayer(polygons);
        });

        // ══════════════════════════════════════
        // CONTROL LAYER
        // ══════════════════════════════════════
        L.control.layers({}, {
            'Titik (Points)': points,
            'Garis (Polylines)': polylines,
            'Area (Polygons)': polygons,
        }).addTo(map);
    </script>
@endsection
