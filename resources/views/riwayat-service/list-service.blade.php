@extends('layouts.app')

@section('title', 'Service Aktif')

@push('style')
    {{-- CSS Libraries --}}
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/izitoast/dist/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
    <div class="section-header">
        <h1>Service Aset</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Service Aktif</div>
        </div>
    </div>
    
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Daftar Service Aset</h4>
                        <div class="card-header-action">
                            <button class="btn btn-primary" id="btn-tambah-service">
                                <i class="fas fa-plus"></i> Tambah Catatan Service
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="service-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Aset</th>
                                        <th>Kerusakan</th>
                                        <th>Vendor Service</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Perkiraan Selesai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($riwayatServices as $service)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $service->aset->merk }} {{ $service->aset->type }}</strong><br>
                                                <small><code>{{ $service->aset_kode_tag }}</code></small>
                                            </td>
                                            <td>{{ $service->deskripsi_kerusakan }}</td>
                                            {{-- Baris di bawah ini akan menampilkan nama vendor jika ada, atau '-' jika tidak ada --}}
                                            <td>{{ $service->vendor->nama_vendor ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($service->tanggal_masuk_service)->format('d F Y') }}</td>
                                            <td>
                                                @if ($service->perkiraan_selesai)
                                                    <span class="badge badge-light">
                                                        {{ \Carbon\Carbon::parse($service->perkiraan_selesai)->format('d M Y') }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-success btn-sm btn-selesaikan"
                                                        data-service='@json($service)'
                                                        data-aset='@json($service->aset)'>
                                                    <i class="fas fa-check"></i> Selesaikan
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
    {{-- JS Libraries --}}
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('library/izitoast/dist/js/iziToast.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            $('#service-table').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json" }
            });

            // Logika untuk notifikasi (iziToast)
            @if (session('success'))
                iziToast.success({ title: 'Berhasil!', message: '{{ session('success') }}', position: 'topRight' });
            @endif
            @if (session('error'))
                iziToast.error({ title: 'Gagal!', message: '{{ session('error') }}', position: 'topRight' });
            @endif

            // Menyiapkan tanggal hari ini untuk default value
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const formattedToday = `${yyyy}-${mm}-${dd}`;

            // Handler untuk tombol "Tambah Catatan Service"
            // Tugasnya HANYA membuka modal.
            $('#btn-tambah-service').on('click', function(e) {
                e.preventDefault();
                $('#modal-tambah-service').modal('show');
            });

            // Handler ini berjalan SETELAH modal "Tambah Service" benar-benar muncul
            // Semua persiapan isi modal dilakukan di sini.
            $('#modal-tambah-service').on('shown.bs.modal', function () {
                var modal = $(this);
                var select2Asset = modal.find('#aset_kode_tag_tambah');
                
                // 1. Reset semua isian form
                modal.find('form')[0].reset();
                modal.find('#hidden_aset_kode_tag_tambah').val('');
                modal.find('#tanggal_masuk_service_tambah').val(formattedToday);
                
                // 2. Hancurkan instance Select2 yang mungkin ada untuk menghindari konflik
                if (modal.find('.select2-hidden-accessible').length) {
                    modal.find('.select2, .select2-tags').select2('destroy');
                }

                // 3. Inisialisasi Select2 untuk Vendor
                modal.find('.select2-tags').select2({
                    tags: true,
                    dropdownParent: modal
                });
                
                // 4. Inisialisasi Select2 untuk Aset dengan pencarian AJAX
                select2Asset.select2({
                    dropdownParent: modal, 
                    placeholder: 'Ketik untuk mencari aset...',
                    ajax: {
                        url: "{{ route('riwayat-service.searchAset') }}",
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) { return { results: data.results }; },
                        cache: true
                    },
                    templateResult: function (data) {
                        if (data.loading) return data.text;
                        if (data.is_rusak) {
                            return $('<span class="text-danger"><i class="fas fa-exclamation-triangle mr-2"></i> ' + data.text + '</span>');
                        }
                        return data.text;
                    }
                });

                // 5. Setelah diinisialisasi, baru setel nilainya ke kosong
                select2Asset.val(null).trigger('change');
                modal.find('#vendor_id_tambah').val(null).trigger('change');
                
                // 6. Siapkan event listener untuk saat aset dipilih
                select2Asset.on('select2:select', function (e) {
                    var data = e.params.data;
                    modal.find('#hidden_aset_kode_tag_tambah').val(data.id);
                });
            });

            // Handler untuk membersihkan modal SETELAH ditutup
            $('#modal-tambah-service').on('hidden.bs.modal', function () {
                // Hapus event listener 'select' agar tidak menumpuk dan menyebabkan panggilan ganda
                $(this).find('#aset_kode_tag_tambah').off('select2:select');
            });

            // Handler untuk tombol "Selesaikan Service" di setiap baris tabel
            $('#service-table tbody').on('click', '.btn-selesaikan', function() {
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

            // Handler untuk membuka kembali modal jika ada error validasi dari server
            @if ($errors->any())
                $('#modal-tambah-service').modal('show');
            @endif
        });
    </script>
@endpush
