@extends('layouts.app')

@section('title', 'Detail Aset - ' . $aset->kode_tag)

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <style>
        .asset-title {
            font-size: 1.8rem;
            font-weight: 700;
        }
        .asset-sub {
            font-size: 1rem;
            color: #6c757d;
        }
        .info-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .info-value {
            font-weight: 600;
            font-size: 1.05rem;
        }
        .section-box {
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
        }
    </style>
@endpush

@section('content')
    <div class="section-header">
        <h1>Detail Aset (<code>{{ $aset->kode_tag }}</code>)</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('aset.index') }}">Aset</a></div>
            <div class="breadcrumb-item">Detail</div>
        </div>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Informasi Aset</h4>
                <div class="card-header-action">
                    @if (strtolower($aset->statusAset->nama) == 'digunakan')
                        <form action="{{ route('pemakaian.kembalikan', $aset->kode_tag) }}" method="POST" onsubmit="return confirm('Anda yakin ingin mengembalikan aset ini?');">
                            @csrf
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-undo"></i> Kembalikan Aset
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">Informasi Utama</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pemakaian-tab" data-toggle="tab" href="#pemakaian" role="tab" aria-controls="pemakaian" aria-selected="false">Riwayat Pemakaian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="service-tab" data-toggle="tab" href="#service" role="tab" aria-controls="service" aria-selected="false">Riwayat Service</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    {{-- Tab 1: Informasi Utama --}}
                    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                        <div class="row mt-4">
                            <div class="col-md-4">
                                @if ($aset->gambar)
                                    <img src="{{ Storage::url($aset->gambar) }}" alt="Gambar Aset" class="img-fluid rounded shadow-sm">
                                @else
                                    <div class="text-center text-muted p-5 border rounded">
                                        <i class="fas fa-image fa-5x"></i>
                                        <p class="mt-2">Tidak ada gambar</p>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-md">
                                        <tr>
                                            <th style="width: 30%">Status</th>
                                            <td><x-status-badge :status="$aset->statusAset->nama" /></td>
                                        </tr>
                                        <tr>
                                            <th>Kategori</th>
                                            <td>{{ $aset->kategori->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>Merk / Tipe</th>
                                            <td>{{ $aset->merk }} / {{ $aset->type }}</td>
                                        </tr>
                                        <tr>
                                            <th>Serial Number</th>
                                            <td><code>{{ $aset->serial_number ?? '-' }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Pembelian</th>
                                            <td>{{ $aset->tanggal_pembelian ? \Carbon\Carbon::parse($aset->tanggal_pembelian)->format('d F Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Vendor</th>
                                            <td>{{ $aset->vendor->nama_vendor ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Spesifikasi</th>
                                            <td>{{ $aset->spesifikasi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>{{ $aset->keterangan ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Tab 2: Riwayat Pemakaian --}}
                    <div class="tab-pane fade" id="pemakaian" role="tabpanel" aria-labelledby="pemakaian-tab">
                        <div class="py-4">
                            @forelse ($aset->historiPemakaians->sortByDesc('tanggal_serah') as $histori)
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="card-title mb-0"><strong>Penanggung Jawab:</strong> {{ $histori->pegawai->nama }}</h6>
                                            <span class="badge badge-{{ $histori->tanggal_kembali ? 'secondary' : 'success' }}">
                                                {{ $histori->tanggal_kembali ? 'Telah Dikembalikan' : 'Sedang Digunakan' }}
                                            </span>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Tanggal Serah:</strong> {{ \Carbon\Carbon::parse($histori->tanggal_serah)->format('d M Y') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Tanggal Kembali:</strong> {{ $histori->tanggal_kembali ? \Carbon\Carbon::parse($histori->tanggal_kembali)->format('d M Y') : '-' }}</p>
                                            </div>
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

                    {{-- Tab 3: Riwayat Service --}}
                    <div class="tab-pane fade" id="service" role="tabpanel" aria-labelledby="service-tab">
                        @php
                            $servisAktif = $aset->riwayatServices->firstWhere('tanggal_selesai_service', null);
                        @endphp
                        <div class="d-flex justify-content-between align-items-center my-4">
                            <h5 class="mb-0">Riwayat Service untuk Aset Ini</h5>
                            @if (!$servisAktif)
                                <button class="btn btn-primary" id="btn-tambah-service-detail">
                                    <i class="fas fa-plus mr-1"></i> Tambah Catatan Service
                                </button>
                            @endif
                        </div>
                        @forelse ($aset->riwayatServices->sortByDesc('tanggal_masuk_service') as $service)
                            <div class="card mb-3 shadow-sm">
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
                                                <p class="text-muted">Aset ini masih dalam proses perbaikan.</p>
                                                <button class="btn btn-outline-success btn-sm mt-2 btn-selesaikan"
                                                        data-service='@json($service)'
                                                        data-aset='@json($service->aset)'>
                                                    <i class="fas fa-check"></i> Selesaikan Service
                                                </button>
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
@endsection

@push('modals')
    @include('riwayat-service.partials.modals')
@endpush

@push('scripts')
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const formattedToday = `${yyyy}-${mm}-${dd}`;

            // --- AWAL BAGIAN BARU ---
            // Inisialisasi Select2 saat modal ditampilkan
            $('#modal-tambah-service').on('shown.bs.modal', function () {
                // Inisialisasi dropdown vendor agar bisa diketik (tagging)
                $(this).find('.select2-tags').select2({
                    tags: true,
                    dropdownParent: $(this) // Penting agar dropdown muncul di atas modal
                });
            });
            // --- AKHIR BAGIAN BARU ---

            $('#btn-tambah-service-detail').on('click', function() {
                var asetData = @json($aset);
                var optionText = `${asetData.merk} ${asetData.type} (${asetData.kode_tag})`;
                var option = new Option(optionText, asetData.kode_tag, true, true);
                
                // Reset form dan isian lainnya
                $('#modal-tambah-service form')[0].reset();
                $('#aset_kode_tag_tambah').empty().append(option).trigger('change');
                $('#hidden_aset_kode_tag_tambah').val(asetData.kode_tag);
                $('#aset_kode_tag_tambah').prop('disabled', true);
                $('#tanggal_masuk_service_tambah').val(formattedToday);
                $('#vendor_id_tambah').val(null).trigger('change'); // Reset vendor

                $('#modal-tambah-service').modal('show');
            });

            $('#modal-tambah-service').on('hidden.bs.modal', function () {
                $('#aset_kode_tag_tambah').prop('disabled', false).empty();
                $('#hidden_aset_kode_tag_tambah').val('');
                
                // Hancurkan instance select2 agar tidak konflik
                if ($('#vendor_id_tambah').hasClass('select2-hidden-accessible')) {
                    $('#vendor_id_tambah').select2('destroy');
                }
            });

            // Kode untuk tombol "Selesaikan Service" (tidak perlu diubah)
            $(document).on('click', '.btn-selesaikan', function() {
                const service = $(this).data('service');
                const aset = $(this).data('aset');
                const modal = $('#modal-selesaikan-service');

                let updateUrl = "{{ route('riwayat-service.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', service.id);
                modal.find('form').attr('action', updateUrl);

                modal.find('#detail_aset').text(aset.merk + ' ' + aset.type + ' (' + service.aset_kode_tag + ')');
                modal.find('#detail_kerusakan').text(service.deskripsi_kerusakan);
                modal.find('#tanggal_selesai_service').val(formattedToday);
                modal.find('#tindakan_perbaikan').val(service.tindakan_perbaikan);
                modal.find('#biaya_service').val(service.biaya_service);

                modal.modal('show');
            });
        });
    </script>
@endpush