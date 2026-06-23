@extends('layouts.template')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css">
    <style>
        :root {
            --cream: #FDF6E3;
            --cream2: #FFF9F0;
            --tan: #E8D5B0;
            --border: #D2B48C;
            --brown: #5C3317;
            --mid: #8B4513;
            --muted: #6B5C4E;
            --soft: #A0916A;
            --green-bg: #D4EDDA;
            --green-tx: #2E7D32;
            --blue-bg: #D6EAF8;
            --blue-tx: #1A5276;
        }

        .page-table {
            padding: 2rem 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .table-card {
            background: var(--cream);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(92, 51, 23, .07);
            margin-bottom: 2rem;
        }

        .table-card-header {
            background: linear-gradient(135deg, var(--brown), var(--mid));
            color: var(--cream);
            padding: .9rem 1.4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-card-header h4 {
            font-family: 'Playfair Display', serif;
            font-size: .95rem;
            margin: 0;
        }

        .table-badge {
            font-size: .70rem;
            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .3);
            border-radius: 20px;
            padding: 2px 10px;
        }

        /* DataTables */
        div.dt-container {
            padding: .7rem 1rem;
        }

        div.dt-container .dt-search input,
        div.dt-container .dt-length select {
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 4px 8px;
            font-size: .81rem;
            color: var(--brown);
            background: var(--cream);
            outline: none;
        }

        div.dt-container .dt-search input:focus,
        div.dt-container .dt-length select:focus {
            border-color: var(--mid);
            box-shadow: 0 0 0 2px rgba(139, 69, 19, .12);
        }

        div.dt-container .dt-info,
        div.dt-container .dt-length label,
        div.dt-container .dt-search label {
            font-size: .79rem;
            color: var(--muted);
        }

        div.dt-container .dt-paging .dt-paging-button {
            border: 1px solid var(--border) !important;
            border-radius: 6px !important;
            color: var(--brown) !important;
            background: var(--cream) !important;
            padding: 3px 9px !important;
            font-size: .79rem;
        }

        div.dt-container .dt-paging .dt-paging-button.current {
            background: var(--mid) !important;
            color: var(--cream) !important;
            border-color: var(--mid) !important;
        }

        div.dt-container .dt-paging .dt-paging-button:hover:not(.disabled) {
            background: var(--tan) !important;
            color: var(--brown) !important;
        }

        /* Table */
        .tbl {
            width: 100% !important;
            border-collapse: collapse;
        }

        .tbl thead th {
            background: var(--tan);
            color: var(--brown);
            font-size: .73rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: 10px 14px;
            border-bottom: 2px solid var(--border);
            border-right: 1px solid var(--border);
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
            background: var(--cream2);
        }

        .tbl td {
            padding: 11px 14px;
            font-size: .83rem;
            color: var(--brown);
            vertical-align: middle;
            border-right: 1px solid #EDE0C8;
        }

        .tbl td:last-child {
            border-right: none;
        }

        .tbl td.no {
            color: var(--soft);
            font-size: .77rem;
            width: 40px;
            text-align: center;
        }

        /* Cell helpers */
        .loc-name {
            font-weight: 600;
            color: var(--brown);
        }

        .loc-desc {
            font-size: .77rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .badge-sm {
            display: inline-block;
            font-size: .71rem;
            border-radius: 4px;
            padding: 2px 7px;
            margin-bottom: 3px;
            line-height: 1.5;
        }

        .badge-lat {
            background: var(--tan);
            color: var(--brown);
        }

        .badge-long {
            background: var(--green-bg);
            color: var(--green-tx);
        }

        .badge-node {
            background: var(--tan);
            color: var(--brown);
        }

        .badge-len {
            background: var(--blue-bg);
            color: var(--blue-tx);
        }

        .badge-area {
            background: var(--green-bg);
            color: var(--green-tx);
        }

        .loc-img {
            width: 58px;
            height: 58px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: transform .2s;
        }

        .loc-img:hover {
            transform: scale(1.08);
        }

        .no-img {
            width: 58px;
            height: 58px;
            border-radius: 8px;
            background: var(--tan);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--soft);
            font-size: 1.1rem;
        }

        .empty-row td {
            text-align: center;
            padding: 2rem;
            color: var(--soft);
            font-size: .84rem;
        }

        /* Modal gambar */
        #imgModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .65);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        #imgModal.show {
            display: flex;
        }

        #imgModal img {
            max-width: 88vw;
            max-height: 88vh;
            border-radius: 12px;
            border: 3px solid var(--cream);
            box-shadow: 0 8px 40px rgba(0, 0, 0, .5);
        }

        #imgModal .close-btn {
            position: absolute;
            top: 18px;
            right: 24px;
            color: #fff;
            font-size: 2rem;
            cursor: pointer;
            line-height: 1;
        }
    </style>
@endsection

@section('content')
    <div class="page-table">

        {{-- Modal preview gambar --}}
        <div id="imgModal" onclick="closeImg()">
            <span class="close-btn">&times;</span>
            <img id="imgModalSrc" src="" alt="Preview">
        </div>

        {{-- ===== TITIK ===== --}}
        <div class="table-card">
            <div class="table-card-header">
                <h4><i class="fa-solid fa-location-dot me-2"></i> Inventarisasi Titik Lokasi</h4>
                <span class="table-badge">{{ count($points) }} Data</span>
            </div>
            <div style="overflow-x:auto">
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
                        @forelse ($points as $i => $p)
                            <tr>
                                <td class="no">{{ $i + 1 }}</td>
                                <td>
                                    <div class="loc-name">{{ $p->name }}</div>
                                    <div class="loc-desc">{{ $p->description ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="badge-sm badge-lat"><i class="fa-solid fa-location-dot"
                                            style="font-size:.63rem"></i> {{ number_format($p->latitude, 6) }}</span><br>
                                    <span class="badge-sm badge-long"><i class="fa-solid fa-compass"
                                            style="font-size:.63rem"></i> {{ number_format($p->longitude, 6) }}</span>
                                </td>
                                <td>
                                    @if ($p->image)
                                        <img src="{{ asset('storage/images/' . $p->image) }}" class="loc-img"
                                            alt="{{ $p->name }}"
                                            onclick="openImg('{{ asset('storage/images/' . $p->image) }}')">
                                    @else
                                        <div class="no-img"><i class="fa-solid fa-image"></i></div>
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

        {{-- ===== GARIS ===== --}}
        <div class="table-card">
            <div class="table-card-header">
                <h4><i class="fa-solid fa-draw-polygon me-2"></i> Inventarisasi Garis</h4>
                <span class="table-badge">{{ count($polylines) }} Data</span>
            </div>
            <div style="overflow-x:auto">
                <table class="tbl" id="tabelpolyline">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Panjang</th>
                            <th>Jumlah Titik</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($polylines as $i => $pl)
                            <tr>
                                <td class="no">{{ $i + 1 }}</td>
                                <td>
                                    <div class="loc-name">{{ $pl->name }}</div>
                                </td>
                                <td>
                                    <div class="loc-desc">{{ $pl->description ?? '-' }}</div>
                                </td>
                                <td><span class="badge-sm badge-len"><i class="fa-solid fa-ruler"
                                            style="font-size:.63rem"></i>
                                        {{ number_format($pl->panjang_meter, 0, ',', '.') }} m</span></td>
                                <td><span class="badge-sm badge-node"><i class="fa-solid fa-circle-nodes"
                                            style="font-size:.63rem"></i> {{ $pl->jumlah_titik }} titik</span></td>
                                <td>
                                    @if ($pl->image)
                                        <img src="{{ asset('storage/images/' . $pl->image) }}" class="loc-img"
                                            alt="{{ $pl->name }}"
                                            onclick="openImg('{{ asset('storage/images/' . $pl->image) }}')">
                                    @else
                                        <div class="no-img"><i class="fa-solid fa-image"></i></div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="6">Belum ada data garis.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== AREA ===== --}}
        <div class="table-card">
            <div class="table-card-header">
                <h4><i class="fa-solid fa-vector-square me-2"></i> Inventarisasi Area</h4>
                <span class="table-badge">{{ count($polygons) }} Data</span>
            </div>
            <div style="overflow-x:auto">
                <table class="tbl" id="tabelpolygon">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Luas</th>
                            <th>Jumlah Titik</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($polygons as $i => $pg)
                            <tr>
                                <td class="no">{{ $i + 1 }}</td>
                                <td>
                                    <div class="loc-name">{{ $pg->name }}</div>
                                </td>
                                <td>
                                    <div class="loc-desc">{{ $pg->description ?? '-' }}</div>
                                </td>
                                <td><span class="badge-sm badge-area"><i class="fa-solid fa-expand"
                                            style="font-size:.63rem"></i>
                                        {{ number_format($pg->area_hektar, 2, ',', '.') }} Ha</span></td>
                                <td><span class="badge-sm badge-node"><i class="fa-solid fa-circle-nodes"
                                            style="font-size:.63rem"></i> {{ $pg->jumlah_titik }} titik</span></td>
                                <td>
                                    @if ($pg->image)
                                        <img src="{{ asset('storage/images/' . $pg->image) }}" class="loc-img"
                                            alt="{{ $pg->name }}"
                                            onclick="openImg('{{ asset('storage/images/' . $pg->image) }}')">
                                    @else
                                        <div class="no-img"><i class="fa-solid fa-image"></i></div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="6">Belum ada data area.</td>
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
        $(function() {
            const lang = {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikut",
                    previous: "Sebelum"
                }
            };

            $('#tabeldata').DataTable({
                language: lang,
                pageLength: 10,
                columnDefs: [{
                    orderable: false,
                    targets: [0, 3]
                }]
            });
            $('#tabelpolyline').DataTable({
                language: lang,
                pageLength: 10,
                columnDefs: [{
                    orderable: false,
                    targets: [0, 5]
                }]
            });
            $('#tabelpolygon').DataTable({
                language: lang,
                pageLength: 10,
                columnDefs: [{
                    orderable: false,
                    targets: [0, 5]
                }]
            });
        });

        function openImg(src) {
            document.getElementById('imgModalSrc').src = src;
            document.getElementById('imgModal').classList.add('show');
            event.stopPropagation();
        }

        function closeImg() {
            document.getElementById('imgModal').classList.remove('show');
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeImg();
        });
    </script>
@endpush
