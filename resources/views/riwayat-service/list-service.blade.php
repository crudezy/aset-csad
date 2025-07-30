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
                                    @forelse ($riwayatServices as $service)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $service->aset->merk }} {{ $service->aset->type }}</strong><br>
                                                <small><code>{{ $service->aset_kode_tag }}</code></small>
                                            </td>
                                            <td>{{ $service->deskripsi_kerusakan }}</td>
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
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada aset yang sedang dalam service.</td>
                                        </tr>
                                    @endforelse
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
            $('#service-table').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json" }
            });

            @if (session('success'))
                iziToast.success({ title: 'Berhasil!', message: '{{ session('success') }}', position: 'topRight' });
            @endif
            @if (session('error'))
                iziToast.error({ title: 'Gagal!', message: '{{ session('error') }}', position: 'topRight' });
            @endif

            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const formattedToday = `${yyyy}-${mm}-${dd}`;

            $('#btn-tambah-service').on('click', function(e) {
                e.preventDefault();
                $('#modal-tambah-service form')[0].reset();
                $('#aset_kode_tag_tambah').val(null).trigger('change');
                $('#vendor_id_tambah').val(null).trigger('change');
                $('#hidden_aset_kode_tag_tambah').val('');
                $('#tanggal_masuk_service_tambah').val(formattedToday);
                $('#modal-tambah-service').modal('show');
            });

            $('#modal-tambah-service').on('shown.bs.modal', function () {
                var select2El = $(this).find('#aset_kode_tag_tambah');
                
                // Inisialisasi semua select2 biasa dan yang memiliki fitur tagging
                $(this).find('.select2').select2({ dropdownParent: $(this) });
                $(this).find('.select2-tags').select2({
                    tags: true,
                    dropdownParent: $(this)
                });
                
                select2El.select2({
                    dropdownParent: $(this), 
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

                select2El.on('select2:select', function (e) {
                    var data = e.params.data;
                    $('#hidden_aset_kode_tag_tambah').val(data.id);
                });
            });

            $('#modal-tambah-service').on('hidden.bs.modal', function () {
                $('#aset_kode_tag_tambah').val(null).trigger('change');
                $('#hidden_aset_kode_tag_tambah').val('');
            });

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

            @if ($errors->any())
                $('#modal-tambah-service').modal('show');
            @endif
        });
    </script>
@endpush
