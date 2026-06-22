@extends('layouts.template')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css">

    <style>
        .page-table {
            padding: 2rem 1.5rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        .table-card {
            background: #FDF6E3;
            border: 1px solid #D2B48C;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(92, 51, 23, 0.08);
            margin-bottom: 2rem;
        }

        .table-card-header {
            background: linear-gradient(135deg, #5C3317, #8B4513);
            color: #FDF6E3;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-card-header h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            margin: 0;
        }

        .table-badge {
            font-size: .72rem;
            background: rgba(255, 255, 255, 0.15);
            color: #FDF6E3;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 3px 10px;
        }

        /* Override tampilan DataTables agar sesuai tema */
        div.dt-container {
            padding: 0.75rem 1rem;
        }

        div.dt-container .dt-search input,
        div.dt-container .dt-length select {
            border: 1px solid #D2B48C;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: .82rem;
            color: #5C3317;
            background: #FDF6E3;
            outline: none;
        }

        div.dt-container .dt-search input:focus,
        div.dt-container .dt-length select:focus {
            border-color: #8B4513;
            box-shadow: 0 0 0 2px rgba(139, 69, 19, 0.15);
        }

        div.dt-container .dt-info,
        div.dt-container .dt-length label,
        div.dt-container .dt-search label {
            font-size: .80rem;
            color: #6B5C4E;
        }

        div.dt-container .dt-paging .dt-paging-button {
            border: 1px solid #D2B48C !important;
            border-radius: 6px !important;
            color: #5C3317 !important;
            background: #FDF6E3 !important;
            padding: 3px 9px !important;
            font-size: .80rem;
        }

        div.dt-container .dt-paging .dt-paging-button.current {
            background: #8B4513 !important;
            color: #FDF6E3 !important;
            border-color: #8B4513 !important;
        }

        div.dt-container .dt-paging .dt-paging-button:hover:not(.disabled) {
            background: #E8D5B0 !important;
            color: #5C3317 !important;
        }

        /* Garis tabel */
        .tbl {
            width: 100% !important;
            border-collapse: collapse;
        }

        .tbl thead th {
            background: #E8D5B0;
            color: #5C3317;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: 10px 14px;
            border-bottom: 2px solid #D2B48C;
            border-right: 1px solid #D2B48C;
            font-weight: 600;
        }

        .tbl thead th:last-child {
            border-right: none;
        }

        .tbl tbody tr {
            border-bottom: 1px solid #EDE0C8;
            transition: background .15s;
        }

        .tbl tbody tr:last-child {
            border-bottom: none;
        }

        .tbl tbody tr:hover {
            background: #FFF9F0;
        }

        .tbl td {
            padding: 12px 14px;
            font-size: .84rem;
            color: #5C3317;
            vertical-align: middle;
            border-right: 1px solid #EDE0C8;
        }

        .tbl td:last-child {
            border-right: none;
        }

        .tbl td.no {
            color: #A0916A;
            font-size: .78rem;
            width: 40px;
            text-align: center;
        }

        .loc-name {
            font-weight: 600;
            color: #5C3317;
        }

        .loc-desc {
            font-size: .78rem;
            color: #6B5C4E;
            margin-top: 2px;
        }

        .coord-badge {
            display: inline-block;
            font-size: .72rem;
            border-radius: 4px;
            padding: 2px 7px;
            margin-bottom: 3px;
        }

        .coord-lat {
            background: #E8D5B0;
            color: #5C3317;
        }

        .coord-long {
            background: #D4EDDA;
            color: #2E7D32;
        }

        .loc-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #D2B48C;
        }

        .no-img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            background: #E8D5B0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #A0916A;
            font-size: 1.2rem;
        }

        .titik-badge {
            display: inline-block;
            font-size: .72rem;
            border-radius: 4px;
            padding: 2px 8px;
            background: #E8D5B0;
            color: #5C3317;
        }

        .color-swatch {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: .78rem;
            color: #5C3317;
        }

        .swatch-box {
            width: 20px;
            height: 14px;
            border-radius: 3px;
            border: 1px solid #D2B48C;
            display: inline-block;
        }

        .empty-row td {
            text-align: center;
            padding: 2rem;
            color: #A0916A;
            font-size: .85rem;
        }
    </style>
@endsection

@section('content')
    <div class="page-table">

        {{-- ===== TABEL POINT ===== --}}
        <div class="table-card">
            <div class="table-card-header">
                <h4><i class="fa-solid fa-location-dot me-2"></i> Inventarisasi Titik Lokasi</h4>
                <span class="table-badge">{{ count($points) }} Data</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="tbl" id="tabeldata">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Lokasi</th>
                            <th>Koordinat</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($points as $index => $point)
                            <tr>
                                <td class="no">{{ $index + 1 }}</td>
                                <td>
                                    <div class="loc-name">{{ $point->name }}</div>
                                    <div class="loc-desc">{{ $point->description ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="coord-badge coord-lat">
                                        <i class="fa-solid fa-location-dot" style="font-size:.65rem;"></i>
                                        {{ number_format($point->latitude, 6) }}
                                    </span><br>
                                    <span class="coord-badge coord-long">
                                        <i class="fa-solid fa-compass" style="font-size:.65rem;"></i>
                                        {{ number_format($point->longitude, 6) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($point->image)
                                        <img src="{{ asset('storage/images/' . $point->image) }}" class="loc-img"
                                            alt="{{ $point->name }}">
                                    @else
                                        <div class="no-img">
                                            <i class="fa-solid fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="4">Belum ada data titik lokasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== TABEL POLYLINE ===== --}}
        <div class="table-card">
            <div class="table-card-header">
                <h4><i class="fa-solid fa-draw-polygon me-2"></i> Inventarisasi Polyline</h4>
                <span class="table-badge">{{ count($polylines) }} Data</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="tbl" id="tabelpolyline">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Titik</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($polylines as $index => $polyline)
                            <tr>
                                <td class="no">{{ $index + 1 }}</td>
                                <td>
                                    <div class="loc-name">{{ $polyline->name }}</div>
                                </td>
                                <td>
                                    <div class="loc-desc">{{ $polyline->description ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="titik-badge">
                                        <i class="fa-solid fa-circle-nodes" style="font-size:.65rem;"></i>
                                        {{ $polyline->jumlah_titik }} titik
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="4">Belum ada data polyline.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== TABEL POLYGON ===== --}}
        <div class="table-card">
            <div class="table-card-header">
                <h4><i class="fa-solid fa-vector-square me-2"></i> Inventarisasi Polygon</h4>
                <span class="table-badge">{{ count($polygons) }} Data</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="tbl" id="tabelpolygon">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Titik</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($polygons as $index => $polygon)
                            <tr>
                                <td class="no">{{ $index + 1 }}</td>
                                <td>
                                    <div class="loc-name">{{ $polygon->name }}</div>
                                </td>
                                <td>
                                    <div class="loc-desc">{{ $polygon->description ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="titik-badge">
                                        <i class="fa-solid fa-circle-nodes" style="font-size:.65rem;"></i>
                                        {{ $polygon->jumlah_titik }} titik
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="4">Belum ada data polygon.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            const dtConfig = {
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikut",
                        previous: "Sebelum"
                    }
                },
                pageLength: 10
            };

            $('#tabeldata').DataTable({
                ...dtConfig,
                columnDefs: [{
                    orderable: false,
                    targets: [3]
                }]
            });

            $('#tabelpolyline').DataTable({
                ...dtConfig,
                columnDefs: [{
                    orderable: false,
                    targets: [3]
                }]
            });

            $('#tabelpolygon').DataTable({
                ...dtConfig,
                columnDefs: [{
                    orderable: false,
                    targets: [3]
                }]
            });
        });
    </script>
@endpush
