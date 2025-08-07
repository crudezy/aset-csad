<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Aset - {{ $aset->kode_tag }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            padding: 2rem 0;
        }
        .card {
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 0.75rem;
        }
        .card-header {
            background-color: #343a40; /* Warna abu-abu gelap */
            color: #ffffff;
            border-bottom: none;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        .card-title {
            font-weight: 600;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .table td, .table th {
            padding: 0.9rem 0.75rem;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
        }
        .table-borderless td, .table-borderless th {
             border-top: none;
        }
        .no-image-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            height: 100%;
            min-height: 200px;
            color: #6c757d;
        }
        .no-image-placeholder svg {
            width: 50px;
            height: 50px;
            margin-bottom: 1rem;
        }
        /* Style untuk Tab Riwayat */
        .history-tabs {
            margin-top: 2rem;
            border-top: 1px solid #e9ecef;
            padding-top: 1.5rem;
        }
        .history-tabs .nav-link {
            color: #495057;
            font-weight: 600;
        }
        .history-tabs .nav-link.active {
            color: #007bff;
            border-bottom: 3px solid #007bff;
            background-color: transparent;
        }
        .history-card {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Detail Aset</h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        @if ($aset->gambar)
                            <img src="{{ Storage::url($aset->gambar) }}" alt="Gambar Aset" class="img-fluid rounded shadow-sm">
                        @else
                            <div class="no-image-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16">
                                  <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                  <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                                </svg>
                                <p class="mb-0">Tidak ada gambar</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h5 class="card-title">{{ $aset->merk }} {{ $aset->type }}</h5>
                        <p class="card-text text-muted mb-4"><code>{{ $aset->kode_tag }}</code></p>

                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="info-label">Status</td>
                                <td><x-status-badge :status="$aset->statusAset->nama" /></td>
                            </tr>
                            <tr>
                                <td class="info-label">Kategori</td>
                                <td>{{ $aset->kategori->nama }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Serial Number</td>
                                <td><code>{{ $aset->serial_number ?? '-' }}</code></td>
                            </tr>
                            <tr>
                                <td class="info-label">Tgl. Pembelian</td>
                                <td>{{ $aset->tanggal_pembelian ? \Carbon\Carbon::parse($aset->tanggal_pembelian)->format('d F Y') : '-' }}</td>
                            </tr>
                             <tr>
                                <td class="info-label">Vendor</td>
                                <td>{{ $aset->vendor->nama_vendor ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Spesifikasi</td>
                                <td style="white-space: pre-wrap;">{{ $aset->spesifikasi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Keterangan</td>
                                <td style="white-space: pre-wrap;">{{ $aset->keterangan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="history-tabs">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pemakaian-tab" data-toggle="tab" href="#pemakaian" role="tab" aria-controls="pemakaian" aria-selected="true">Riwayat Pemakaian</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="service-tab" data-toggle="tab" href="#service" role="tab" aria-controls="service" aria-selected="false">Riwayat Service</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        {{-- Tab 1: Riwayat Pemakaian --}}
                        <div class="tab-pane fade show active" id="pemakaian" role="tabpanel" aria-labelledby="pemakaian-tab">
                            <div class="py-4">
                                @forelse ($aset->historiPemakaians->sortByDesc('tanggal_serah') as $histori)
                                    <div class="card mb-3 history-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="card-title mb-0"><strong>Penanggung Jawab:</strong> {{ $histori->pegawai->nama }}</h6>
                                                <span class="badge badge-{{ $histori->tanggal_kembali ? 'secondary' : 'success' }}">
                                                    {{ $histori->tanggal_kembali ? 'Telah Dikembalikan' : 'Sedang Digunakan' }}
                                                </span>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-6"><p class="mb-1"><strong>Tanggal Serah:</strong> {{ \Carbon\Carbon::parse($histori->tanggal_serah)->format('d M Y') }}</p></div>
                                                <div class="col-md-6"><p class="mb-1"><strong>Tanggal Kembali:</strong> {{ $histori->tanggal_kembali ? \Carbon\Carbon::parse($histori->tanggal_kembali)->format('d M Y') : '-' }}</p></div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center p-5 text-muted">
                                        <i class="fas fa-people-carry fa-2x mb-3"></i>
                                        <p class="mb-0">Belum ada riwayat pemakaian untuk aset ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Tab 2: Riwayat Service --}}
                        <div class="tab-pane fade" id="service" role="tabpanel" aria-labelledby="service-tab">
                             <div class="py-4">
                                @forelse ($aset->riwayatServices->sortByDesc('tanggal_masuk_service') as $service)
                                    <div class="card mb-3 history-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="card-title mb-0"><strong>Kerusakan:</strong> {{ $service->deskripsi_kerusakan }}</h6>
                                                <span class="badge badge-{{ $service->tanggal_selesai_service ? 'success' : 'warning' }}">
                                                    {{ $service->tanggal_selesai_service ? 'Selesai' : 'Dalam Perbaikan' }}
                                                </span>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>Tanggal Masuk:</strong> {{ \Carbon\Carbon::parse($service->tanggal_masuk_service)->format('d M Y') }}</p>
                                                    @if ($service->tanggal_selesai_service)
                                                        <p class="mb-1"><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($service->tanggal_selesai_service)->format('d M Y') }}</p>
                                                    @endif
                                                </div>
                                                <div class="col-md-6">
                                                    @if ($service->tanggal_selesai_service)
                                                        <p class="mb-1"><strong>Tindakan:</strong> {{ $service->tindakan_perbaikan ?: '-' }}</p>
                                                        <p class="mb-0"><strong>Biaya:</strong> Rp {{ number_format($service->biaya_service, 0, ',', '.') }}</p>
                                                    @else
                                                        <p class="text-muted mb-0">Aset masih dalam proses perbaikan.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center p-5 text-muted">
                                        <i class="fas fa-tools fa-2x mb-3"></i>
                                        <p class="mb-0">Belum ada riwayat service untuk aset ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center text-muted py-3">
                Aset CSAD
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>