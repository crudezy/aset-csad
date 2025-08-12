@extends('layouts.app')

@section('title', 'Data Master')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/izitoast/dist/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
    <div class="section-header">
        <h1>Lokasi & Departemen</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Data Master</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            {{-- KOLOM UNTUK TABEL LOKASI --}}
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Data Lokasi</h4>
                        <div class="card-header-action">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah-lokasi">Tambah Baru</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-striped table" id="table-lokasi">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lokasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($lokasis as $lokasi)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $lokasi->nama }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm btn-edit-lokasi"
                                                        data-id="{{ $lokasi->id }}"
                                                        data-nama="{{ $lokasi->nama }}">
                                                    Edit
                                                </button>
                                                <!-- <form action="{{ route('lokasi.destroy', $lokasi->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm confirm-delete">Hapus</button>
                                                </form> -->
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM UNTUK TABEL DEPARTEMEN --}}
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Data Departemen</h4>
                        <div class="card-header-action">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah-departemen">Tambah Baru</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-striped table" id="table-departemen">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Departemen</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($departemens as $departemen)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $departemen->nama }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm btn-edit-departemen"
                                                        data-id="{{ $departemen->id }}"
                                                        data-nama="{{ $departemen->nama }}">
                                                    Edit
                                                </button>
                                                <!-- <form action="{{ route('departemen.destroy', $departemen->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm confirm-delete">Hapus</button>
                                                </form> -->
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada data</td>
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

{{-- PERBAIKAN: Kode modal sekarang di "push" ke stack bernama 'modals' --}}
@push('modals')
    @include('lokasi.partials.modals')
    @include('departemen.partials.modals')
@endpush

@push('scripts')
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
    <script src="{{ asset('library/izitoast/dist/js/iziToast.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables untuk kedua tabel
            $('#table-lokasi').DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"}});
            $('#table-departemen').DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"}});

            // Script notifikasi
            @if (session('success'))
                iziToast.success({
                    title: 'Berhasil!', message: '{{ session('success') }}', position: 'topRight'
                });
            @endif
            @if (session('error'))
                iziToast.error({
                    title: 'Gagal!', message: '{{ session('error') }}', position: 'topRight'
                });
            @endif

            // Event delegation untuk tombol edit
            $('#table-lokasi tbody').on('click', '.btn-edit-lokasi', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                var form = $('#form-edit-lokasi');
                var url = "{{ route('lokasi.update', ':id') }}";
                form.attr('action', url.replace(':id', id));
                $('#edit-nama-lokasi').val(nama);
                $('#modal-edit-lokasi').modal('show');
            });

            $('#table-departemen tbody').on('click', '.btn-edit-departemen', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                var form = $('#form-edit-departemen');
                var url = "{{ route('departemen.update', ':id') }}";
                form.attr('action', url.replace(':id', id));
                $('#edit-nama-departemen').val(nama);
                $('#modal-edit-departemen').modal('show');
            });

            // Event delegation untuk tombol hapus
            $(document).on('click', '.confirm-delete', function(e) {
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

            // Script validasi
            @if ($errors->any())
                @if (old('form_type') === 'lokasi')
                    @if (old('_method') === 'PUT')
                        $('#modal-edit-lokasi').modal('show');
                    @else
                        $('#modal-tambah-lokasi').modal('show');
                    @endif
                @elseif (old('form_type') === 'departemen')
                    @if (old('_method') === 'PUT')
                        $('#modal-edit-departemen').modal('show');
                    @else
                        $('#modal-tambah-departemen').modal('show');
                    @endif
                @endif
            @endif
        });
    </script>
@endpush
