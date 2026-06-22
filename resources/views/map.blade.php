@extends('layouts.template')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
    <style>
        body,
        html {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        #map-wrapper {
            display: flex;
            height: calc(100vh - 60px);
        }

        #map-wrapper #map {
            flex: 1;
            height: 100%;
        }

        #edit-panel {
            width: 0;
            overflow: hidden;
            transition: width 0.3s ease;
            background: #FDF6E3;
            border-left: 3px solid #C8860A;
            display: flex;
            flex-direction: column;
        }

        #edit-panel.open {
            width: 320px;
            min-width: 280px;
        }

        #edit-panel-inner {
            padding: 20px 18px;
            overflow-y: auto;
            flex: 1;
            min-width: 280px;
        }

        #edit-panel h5 {
            font-family: 'Playfair Display', serif;
            color: #5C3317;
            font-size: 1.05rem;
            margin-bottom: 14px;
            border-bottom: 1px solid #D2B48C;
            padding-bottom: 8px;
        }

        #edit-panel label {
            color: #6B5C4E;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 3px;
            display: block;
        }

        #edit-panel input[type=text],
        #edit-panel textarea,
        #edit-panel input[type=file] {
            font-family: 'Lora', Georgia, serif;
            font-size: 0.83rem;
            border: 1px solid #D2B48C;
            background-color: #FFF9F0;
            color: #5C3317;
            border-radius: 6px;
            width: 100%;
            padding: 6px 10px;
            margin-bottom: 12px;
            box-sizing: border-box;
        }

        #edit-panel textarea {
            resize: vertical;
        }

        #edit-panel textarea[readonly] {
            background-color: #E8D5B0;
            color: #6B5C4E;
            font-size: 0.75rem;
            height: 58px;
        }

        #edit-panel input:focus,
        #edit-panel textarea:focus {
            border-color: #B87333;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(184, 115, 51, 0.2);
        }

        .edit-hint {
            background: #FFF3CD;
            border: 1px solid #C8860A;
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 0.78rem;
            color: #5C3317;
            margin-bottom: 14px;
            line-height: 1.45;
        }

        .current-img-box {
            width: 100%;
            max-height: 140px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #D2B48C;
            margin-bottom: 10px;
        }

        .ep-img-preview {
            width: 100%;
            max-height: 120px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #D2B48C;
            margin-bottom: 10px;
            display: none;
        }

        .btn-ep-update {
            background: linear-gradient(135deg, #5C3317, #8B4513);
            color: #FDF6E3;
            border: none;
            border-radius: 7px;
            width: 100%;
            padding: 9px;
            font-family: 'Lora', serif;
            font-size: 0.85rem;
            margin-bottom: 8px;
            cursor: pointer;
        }

        .btn-ep-cancel {
            background: transparent;
            color: #8B4513;
            border: 1.5px solid #B87333;
            border-radius: 7px;
            width: 100%;
            padding: 8px;
            font-family: 'Lora', serif;
            font-size: 0.85rem;
            cursor: pointer;
        }

        .modal-content {
            border: none;
            border-radius: 14px;
            overflow: hidden;
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
        }

        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #5C3317, #8B4513);
            border: none;
            border-radius: 7px;
            font-family: 'Lora', serif;
            font-size: 0.82rem;
            padding: 6px 16px;
            color: #FDF6E3;
        }

        .popup-img-box {
            width: 100%;
            max-height: 160px;
            object-fit: cover;
            border-radius: 6px;
            margin-top: 6px;
            border: 1px solid #D2B48C;
        }
    </style>
@endsection

@section('content')
    <div id="map-wrapper">
        <div id="map"></div>

        <div id="edit-panel">
            <div id="edit-panel-inner">
                <h5 id="ep-title">Edit Data</h5>
                <div class="edit-hint" id="ep-hint-drag" style="display:none;">
                    Geser <b>penanda</b> di peta untuk ubah posisi koordinat, lalu klik <b>Simpan</b>.
                </div>
                <form id="formEditInline" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <label>Nama</label>
                    <input type="text" id="ep_name" name="name" required>
                    <label>Deskripsi</label>
                    <textarea id="ep_description" name="description" rows="3"></textarea>
                    <label>Koordinat / Geometri (WKT)</label>
                    <textarea id="ep_geometry" name="geometry_point" readonly></textarea>
                    <div id="ep_img_wrap" style="display:none;">
                        <label>Foto Saat Ini</label>
                        <img id="ep_current_img" src="" class="current-img-box" alt="Foto saat ini">
                    </div>
                    <label>Ganti Foto <small style="font-weight:400;">(opsional)</small></label>
                    <input type="file" id="ep_image" name="image" accept="image/jpeg,image/png,image/jpg"
                        onchange="previewEditImg(this)">
                    <img id="ep_img_preview" class="ep-img-preview" src="" alt="Preview baru">
                    <button type="submit" class="btn-ep-update">Simpan</button>
                    <button type="button" class="btn-ep-cancel" onclick="closeEditPanel()">Batal</button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL POINT --}}
    <div class="modal fade" tabindex="-1" id="modalInputPoint">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Data Titik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('points.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3"><label>Nama Titik</label><input type="text" class="form-control"
                                name="name" required></div>
                        <div class="mb-3"><label>Deskripsi</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-3"><label>Geometri (WKT)</label>
                            <textarea class="form-control" id="geometry_point" name="geometry_point" readonly></textarea>
                        </div>
                        <div class="mb-2"><label>Foto Lokasi</label><input type="file" class="form-control"
                                name="image" accept="image/jpeg,image/png,image/jpg"
                                onchange="showPreview(this,'prev-point')"><img id="prev-point" class="img-preview"
                                src="" alt="Preview"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL POLYLINE --}}
    <div class="modal fade" tabindex="-1" id="modalInputPolyline">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Data Garis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('polylines.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3"><label>Nama Garis</label><input type="text" class="form-control"
                                name="name" required></div>
                        <div class="mb-3"><label>Deskripsi</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-3"><label>Geometri (WKT)</label>
                            <textarea class="form-control" id="geometry_polyline" name="geometry_polylines" readonly></textarea>
                        </div>
                        <div class="mb-2"><label>Foto Lokasi</label><input type="file" class="form-control"
                                name="image" accept="image/jpeg,image/png,image/jpg"
                                onchange="showPreview(this,'prev-polyline')"><img id="prev-polyline" class="img-preview"
                                src="" alt="Preview"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL POLYGON --}}
    <div class="modal fade" tabindex="-1" id="modalInputPolygon">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Data Area</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('polygons.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3"><label>Nama Area</label><input type="text" class="form-control"
                                name="name" required></div>
                        <div class="mb-3"><label>Deskripsi</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                        <div class="mb-3"><label>Geometri (WKT)</label>
                            <textarea class="form-control" id="geometry_polygon" name="geometry_polygon" readonly></textarea>
                        </div>
                        <div class="mb-2"><label>Foto Lokasi</label><input type="file" class="form-control"
                                name="image" accept="image/jpeg,image/png,image/jpg"
                                onchange="showPreview(this,'prev-polygon')"><img id="prev-polygon" class="img-preview"
                                src="" alt="Preview"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="https://unpkg.com/@terraformer/wkt"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        var editMarker = null,
            editingType = null;
        var pointsData = {},
            pointsGeom = {};
        var polylinesData = {},
            polylinesGeom = {};
        var polygonsData = {},
            polygonsGeom = {};
        var editDrawnItems = null,
            editDrawControl = null;

        function showPreview(input, previewId) {
            var file = input.files[0];
            if (!file) return;
            var img = document.getElementById(previewId);
            img.src = URL.createObjectURL(file);
            img.style.display = 'block';
        }

        function previewEditImg(input) {
            var file = input.files[0];
            if (!file) return;
            var img = document.getElementById('ep_img_preview');
            img.src = URL.createObjectURL(file);
            img.style.display = 'block';
        }
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

        var map = L.map('map').setView([-7.7956, 110.3695], 13);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap &copy; CartoDB'
        }).addTo(map);

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
            var layer = e.layer,
                type = e.layerType;
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
        $('#modalInputPoint, #modalInputPolyline, #modalInputPolygon').on('hidden.bs.modal', function() {
            drawnItems.clearLayers();
        });

        function buildPopup(feature, type) {
            var id = feature.properties.id;
            var name = feature.properties.name || 'Tanpa Nama';
            var description = feature.properties.description || '<i>Tidak ada deskripsi</i>';
            var imageName = feature.properties.image;
            var imgHtml = imageName ? '<img src="{{ asset('storage/images') }}/' + imageName + '" class="popup-img-box">' :
                '';
            var routeDel = '',
                editFn = '';
            if (type === 'marker') {
                routeDel = "{{ route('points.destroy', ':id') }}".replace(':id', id);
                editFn = 'editPoint(' + id + ', event)';
            } else if (type === 'polyline') {
                routeDel = "{{ route('polylines.destroy', ':id') }}".replace(':id', id);
                editFn = 'editPolyline(' + id + ', event)';
            } else if (type === 'polygon') {
                routeDel = "{{ route('polygons.destroy', ':id') }}".replace(':id', id);
                editFn = 'editPolygon(' + id + ', event)';
            }
            return '<div style="min-width:210px;font-family:\'Lora\',serif;">' +
                '<div style="text-align:center;border-bottom:1px solid #D2B48C;padding-bottom:6px;margin-bottom:8px;">' +
                '<b style="font-family:\'Playfair Display\',serif;font-size:1.05rem;color:#5C3317">' + name + '</b></div>' +
                imgHtml +
                '<div style="margin-top:8px;color:#6B5C4E;font-size:0.85rem;line-height:1.4;">' + description + '</div>' +
                '<div style="margin-top:10px;">' +
                '<button onclick="' + editFn +
                '" class="btn btn-sm mb-2 w-100" style="background:#D2B48C;color:#5C3317;border:none;">Edit Data</button></div>' +
                '<form action="' + routeDel + '" method="POST" onsubmit="return confirm(\'Yakin hapus data ini?\')">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '<input type="hidden" name="_method" value="DELETE">' +
                '<button type="submit" class="btn btn-sm" style="background:#8B4513;color:#fff;width:100%;">Hapus Data</button></form></div>';
        }

        // ── Points ──
        var pointsLayer = L.geoJSON(null, {
            pointToLayer: function(feature, latlng) {
                return L.marker(latlng);
            },
            onEachFeature: function(feature, layer) {
                pointsData[feature.properties.id] = feature.properties;
                pointsGeom[feature.properties.id] = feature.geometry;
                layer.bindPopup(buildPopup(feature, 'marker'));
            }
        });
        $.getJSON("{{ route('geojson.points') }}", function(data) {
            pointsLayer.addData(data);
            map.addLayer(pointsLayer);
        }).fail(function(xhr) {
            console.error('Gagal load points:', xhr.status);
        });

        // ── Polylines ──
        var polylinesLayer = L.geoJSON(null, {
            style: {
                color: '#0068A5',
                weight: 3,
                opacity: 0.9,
                dashArray: '8 4'
            },
            onEachFeature: function(feature, layer) {
                polylinesData[feature.properties.id] = feature.properties;
                polylinesGeom[feature.properties.id] = feature.geometry;
                layer.bindPopup(buildPopup(feature, 'polyline'));
            }
        });
        $.getJSON("{{ route('geojson.polylines') }}", function(data) {
            polylinesLayer.addData(data);
            map.addLayer(polylinesLayer);
        }).fail(function(xhr) {
            console.error('Gagal load polylines:', xhr.status);
        });

        // ── Polygons ──
        var polygonsLayer = L.geoJSON(null, {
            style: {
                color: '#2E7D32',
                fillColor: '#66BB6A',
                fillOpacity: 0.25,
                weight: 2
            },
            onEachFeature: function(feature, layer) {
                polygonsData[feature.properties.id] = feature.properties;
                polygonsGeom[feature.properties.id] = feature.geometry;
                layer.bindPopup(buildPopup(feature, 'polygon'));
            }
        });
        $.getJSON("{{ route('geojson.polygons') }}", function(data) {
            polygonsLayer.addData(data);
            map.addLayer(polygonsLayer);
        }).fail(function(xhr) {
            console.error('Gagal load polygons:', xhr.status);
        });

        function clearEditLayers() {
            if (editDrawControl) {
                map.removeControl(editDrawControl);
                editDrawControl = null;
            }
            if (editDrawnItems) {
                map.removeLayer(editDrawnItems);
                editDrawnItems = null;
            }
            if (editMarker) {
                map.removeLayer(editMarker);
                editMarker = null;
            }
        }

        function openEditPanel(type, props, geomObj) {
            editingType = type;
            var titles = {
                point: 'Edit Titik',
                polyline: 'Edit Garis',
                polygon: 'Edit Area'
            };
            $('#ep-title').text(titles[type]);
            $('#ep_name').val(props.name || '');
            $('#ep_description').val(props.description || '');
            var geomFieldName = {
                point: 'geometry_point',
                polyline: 'geometry_polylines',
                polygon: 'geometry_polygon'
            } [type];
            var wkt = Terraformer.geojsonToWKT(geomObj);
            $('#ep_geometry').attr('name', geomFieldName).val(wkt);
            var actions = {
                point: '/points/',
                polyline: '/polylines/',
                polygon: '/polygons/'
            };
            $('#formEditInline').attr('action', actions[type] + props.id);
            if (props.image) {
                $('#ep_current_img').attr('src', '{{ asset('storage/images') }}/' + props.image);
                $('#ep_img_wrap').show();
            } else {
                $('#ep_img_wrap').hide();
            }
            $('#ep_img_preview').hide().attr('src', '');
            $('#ep_image').val('');
            clearEditLayers();
            if (type === 'point') {
                var lng = geomObj.coordinates[0],
                    lat = geomObj.coordinates[1];
                editMarker = L.marker([lat, lng], {
                    draggable: true,
                    zIndexOffset: 1000
                }).addTo(map);
                editMarker.bindTooltip('Geser untuk pindah posisi', {
                    permanent: true,
                    direction: 'right',
                    offset: [18, 0]
                });
                editMarker.on('drag', function(ev) {
                    var pos = ev.target.getLatLng();
                    $('#ep_geometry').val('POINT(' + pos.lng.toFixed(7) + ' ' + pos.lat.toFixed(7) + ')');
                });
                map.setView([lat, lng], 16);
                $('#ep-hint-drag').show();
            } else {
                $('#ep-hint-drag').hide();
                editDrawnItems = new L.FeatureGroup();
                var editLayer = L.geoJSON(geomObj, {
                    style: type === 'polyline' ? {
                        color: '#0068A5',
                        weight: 3
                    } : {
                        color: '#2E7D32',
                        fillColor: '#66BB6A',
                        fillOpacity: 0.3,
                        weight: 2
                    }
                });
                editLayer.eachLayer(function(l) {
                    editDrawnItems.addLayer(l);
                });
                map.addLayer(editDrawnItems);
                editDrawControl = new L.Control.Draw({
                    edit: {
                        featureGroup: editDrawnItems,
                        remove: false
                    },
                    draw: false
                });
                map.addControl(editDrawControl);
                map.on(L.Draw.Event.EDITED, function(e) {
                    e.layers.eachLayer(function(l) {
                        $('#ep_geometry').val(Terraformer.geojsonToWKT(l.toGeoJSON().geometry));
                    });
                });
                map.fitBounds(editDrawnItems.getBounds());
            }
            $('#edit-panel').addClass('open');
        }

        function closeEditPanel() {
            $('#edit-panel').removeClass('open');
            clearEditLayers();
            map.off(L.Draw.Event.EDITED);
            editingType = null;
        }

        function editPoint(id, e) {
            if (e) e.stopPropagation();
            map.closePopup();
            var props = pointsData[id],
                geomObj = pointsGeom[id];
            if (!props || !geomObj) {
                alert('Data belum termuat.');
                return;
            }
            openEditPanel('point', props, geomObj);
        }

        function editPolyline(id, e) {
            if (e) e.stopPropagation();
            map.closePopup();
            var props = polylinesData[id],
                geomObj = polylinesGeom[id];
            if (!props || !geomObj) {
                alert('Data belum termuat.');
                return;
            }
            openEditPanel('polyline', props, geomObj);
        }

        function editPolygon(id, e) {
            if (e) e.stopPropagation();
            map.closePopup();
            var props = polygonsData[id],
                geomObj = polygonsGeom[id];
            if (!props || !geomObj) {
                alert('Data belum termuat.');
                return;
            }
            openEditPanel('polygon', props, geomObj);
        }

        // --- MEMANGGIL LAYER WMS GEOSERVER ---
        var wmsPolygons = L.tileLayer.wms("http://localhost:8080/geoserver/pgwl_2026/wms", {
            layers: 'pgwl_2026:polygons_tables',
            format: 'image/png',
            transparent: true,
            version: '1.1.0',
            attribution: "GeoServer Polygons"
        });

        var wmsPoints = L.tileLayer.wms("http://localhost:8080/geoserver/pgwl_2026/wms", {
            layers: 'pgwl_2026:points',
            format: 'image/png',
            transparent: true,
            version: '1.1.0',
            attribution: "GeoServer Points"
        });

        var wmsPolylines = L.tileLayer.wms("http://localhost:8080/geoserver/pgwl_2026/wms", {
            layers: 'pgwl_2026:polylines_tables',
            format: 'image/png',
            transparent: true,
            version: '1.1.0',
            attribution: "GeoServer Polylines"
        });

        // --- MENGGABUNGKAN SEMUA LAYER KE DALAM KONTROL PETA ---
        L.control.layers({}, {
            'Titik (Bisa Diedit)': pointsLayer,
            'Garis (Bisa Diedit)': polylinesLayer,
            'Area (Bisa Diedit)': polygonsLayer,
            'WMS GeoServer - Titik': wmsPoints,
            'WMS GeoServer - Garis': wmsPolylines,
            'WMS GeoServer - Area': wmsPolygons
        }).addTo(map);

        function reloadAllLayers() {
            pointsLayer.clearLayers();
            polylinesLayer.clearLayers();
            polygonsLayer.clearLayers();
            $.getJSON("{{ route('geojson.points') }}", function(data) {
                pointsLayer.addData(data);
            });
            $.getJSON("{{ route('geojson.polylines') }}", function(data) {
                polylinesLayer.addData(data);
            });
            $.getJSON("{{ route('geojson.polygons') }}", function(data) {
                polygonsLayer.addData(data);
            });
        }

        @if (session('success'))
            reloadAllLayers();
        @endif
    </script>
@endsection
