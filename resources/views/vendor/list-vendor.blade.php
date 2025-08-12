@extends('layouts.app')

@section('title', 'Data Vendor')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/izitoast/dist/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
    <div class="section-header">
        <h1>Vendor</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">Master Data</a></div>
            <div class="breadcrumb-item">Vendor</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Data Vendor</h4>
                        <div class="card-header-action">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah-vendor">Tambah Baru</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-vendor">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Vendor</th>
                                        <th>Kontak</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($vendors as $vendor)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $vendor->nama_vendor }}</td>
                                            <td>{{ $vendor->kontak ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm btn-edit"
                                                        data-vendor-id="{{ $vendor->id }}"
                                                        data-vendor-nama="{{ $vendor->nama_vendor }}"
                                                        data-vendor-kontak="{{ $vendor->kontak }}">
                                                    Edit
                                                </button>
                                                <!-- <form action="{{ route('vendor.destroy', $vendor->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm confirm-delete">Hapus</button>
                                                </form> -->
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data</td>
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
    {{-- Memanggil file modal terpisah --}}
    @include('vendor.partials.modals')
@endpush

@push('scripts')
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
    <script src="{{ asset('library/izitoast/dist/js/iziToast.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#table-vendor').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
                }
            });

            // Notifikasi untuk session 'success' (Hijau)
            @if (session('success'))
                iziToast.success({
                    title: 'Berhasil!',
                    message: '{{ session('success') }}',
                    position: 'topRight'
                });
            @endif

            // =================================================================
            // PENAMBAHAN DI SINI: Notifikasi untuk session 'error' (Merah)
            @if (session('error'))
                iziToast.error({
                    title: 'Gagal!',
                    message: '{{ session('error') }}',
                    position: 'topRight'
                });
            @endif
            // =================================================================

            // Event delegation untuk tombol edit
            $('#table-vendor tbody').on('click', '.btn-edit', function() {
                var id = $(this).data('vendor-id');
                var nama = $(this).data('vendor-nama');
                var kontak = $(this).data('vendor-kontak');
                var form = $('#form-edit-vendor');
                var url = "{{ route('vendor.update', ':id') }}";
                form.attr('action', url.replace(':id', id));
                $('#edit-nama_vendor').val(nama);
                $('#edit-kontak').val(kontak);
                $('#modal-edit-vendor').modal('show');
            });

            // Event delegation untuk tombol hapus
            $('#table-vendor tbody').on('click', '.confirm-delete', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            });

            // Script untuk membuka kembali modal jika ada error validasi
            @if ($errors->any())
                @if (old('_method') === 'PUT')
                    $('#modal-edit-vendor').modal('show');
                @else
                    $('#modal-tambah-vendor').modal('show');
                @endif
            @endif
        });
    </script>
@endpush