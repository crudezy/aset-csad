@extends('layouts.app')

@section('title', 'Data Kategori')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
    <link rel="stylesheet" href="{{ asset('library/izitoast/dist/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
    <div class="section-header">
        <h1>Kategori</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">Master Data</a></div>
            <div class="breadcrumb-item">Kategori</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Data Kategori</h4>
                        <div class="card-header-action">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah-kategori">Tambah Baru</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-kategori">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kategori</th>
                                        <th>Prefix</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kategoris as $kategori)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $kategori->nama }}</td>
                                            <td><code>{{ $kategori->prefix }}</code></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm btn-edit"
                                                        data-kategori-id="{{ $kategori->id }}"
                                                        data-kategori-nama="{{ $kategori->nama }}"
                                                        data-kategori-prefix="{{ $kategori->prefix }}">
                                                    Edit
                                                </button>
                                                <!-- <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="d-inline">
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

{{-- PERBAIKAN: Kode modal sekarang di "push" ke stack bernama 'modals' --}}
@push('modals')
    @include('kategori.partials.modals')
@endpush

@push('scripts')
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
    <script src="{{ asset('library/izitoast/dist/js/iziToast.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#table-kategori').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
                }
            });

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
            $('#table-kategori tbody').on('click', '.btn-edit', function() {
                var id = $(this).data('kategori-id');
                var nama = $(this).data('kategori-nama');
                var prefix = $(this).data('kategori-prefix');
                var form = $('#form-edit-kategori');
                var url = "{{ route('kategori.update', ':id') }}";
                form.attr('action', url.replace(':id', id));
                $('#edit-nama').val(nama);
                $('#edit-prefix').val(prefix);
                $('#modal-edit-kategori').modal('show');
            });

            // Event delegation untuk tombol hapus
            $('#table-kategori tbody').on('click', '.confirm-delete', function(e) {
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
                @if (old('_method') === 'PUT')
                    $('#modal-edit-kategori').modal('show');
                @else
                    $('#modal-tambah-kategori').modal('show');
                @endif
            @endif
        });
    </script>
@endpush
