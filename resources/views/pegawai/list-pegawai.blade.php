@extends('layouts.app')

@section('title', 'Data Pegawai')

@push('style')
    {{-- CSS Libraries --}}
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/izitoast/dist/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
    <div class="section-header">
        <h1>Karyawan</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Karyawan</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Data Karyawan</h4>
                        <div class="card-header-action">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah-pegawai">
                                <i class="fas fa-plus"></i> Tambah Baru
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-pegawai">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Karyawan</th>
                                        <th>Email / No. Telp</th>
                                        <th>Departemen</th>
                                        <th>Lokasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pegawais as $pegawai)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $pegawai->nama }}</td>
                                            <td>
                                                {{ $pegawai->email ?? '-' }}<br>
                                                <small>{{ $pegawai->no_telp ?? '-' }}</small>
                                            </td>
                                            <td>{{ $pegawai->departemen->nama ?? '-' }}</td>
                                            <td>{{ $pegawai->lokasi->nama ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm btn-edit"
                                                        data-pegawai='@json($pegawai)'>
                                                    Edit
                                                </button>
                                                <form action="{{ route('pegawai.destroy', $pegawai->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm confirm-delete">Hapus</button>
                                                </form>
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
    @include('pegawai.partials.modals')
@endpush


@push('scripts')
    {{-- JS Libraries --}}
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/izitoast/dist/js/iziToast.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#table-pegawai').DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"}});

            function initializeSelect2WithTags(modal) {
                modal.find('.select2-tags').select2({
                    tags: true, // Ini akan mengaktifkan fitur "ketik baru"
                    dropdownParent: modal
                });
            }

            // Panggil fungsi saat modal pertama kali ditampilkan
            $('#modal-tambah-pegawai, #modal-edit-pegawai').on('shown.bs.modal', function () {
                initializeSelect2WithTags($(this));
            });
            // =================================================================

            @if (session('success')) iziToast.success({ title: 'Berhasil!', message: '{{ session('success') }}', position: 'topRight' }); @endif
            @if (session('error')) iziToast.error({ title: 'Gagal!', message: '{{ session('error') }}', position: 'topRight' }); @endif

            $('#table-pegawai tbody').on('click', '.btn-edit', function() {
                var pegawai = $(this).data('pegawai');
                var form = $('#form-edit-pegawai');
                var url = "{{ route('pegawai.update', ':id') }}";
                form.attr('action', url.replace(':id', pegawai.id));
                form.find('#edit-nama').val(pegawai.nama);
                form.find('#edit-email').val(pegawai.email);
                form.find('#edit-no_telp').val(pegawai.no_telp);
                form.find('#edit-departemen_id').val(pegawai.departemen_id).trigger('change');
                form.find('#edit-lokasi_id').val(pegawai.lokasi_id).trigger('change');
                $('#modal-edit-pegawai').modal('show');
            });

            $('#table-pegawai tbody').on('click', '.confirm-delete', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?', text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
                }).then((result) => { if (result.isConfirmed) { form.submit(); } });
            });

            @if ($errors->any())
                @if (old('_method') === 'PUT')
                    $('#modal-edit-pegawai').modal('show');
                @else
                    $('#modal-tambah-pegawai').modal('show');
                @endif
            @endif
        });
    </script>
@endpush
