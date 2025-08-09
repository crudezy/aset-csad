@extends('layouts.app')

@section('title', 'Aset Sedang Digunakan')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/izitoast/dist/css/iziToast.min.css') }}">
@endpush

@section('content')
    <div class="section-header">
        <h1>Pemakaian Aset</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Aset Sedang Digunakan</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Daftar Pemakaian Aset</h4>
                        <div class="card-header-action">
                            <button class="btn btn-primary mr-2" id="btn-kembalikan-terpilih" style="display: none;">
                                <i class="fas fa-undo"></i> Kembalikan yang Terpilih
                            </button>
                            <button class="btn btn-success" data-toggle="modal" data-target="#modal-serah-terima-aset">
                                <i class="fas fa-exchange-alt"></i> Lakukan Serah Terima
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="form-kembalikan-multiple" action="{{ route('pemakaian.kembalikan.multiple') }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-striped" id="pemakaian-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%;"><input type="checkbox" id="checkbox-all"></th>
                                            <th>Aset</th>
                                            <th>Karyawan (PIC)</th>
                                            <th>Tanggal Serah</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($histories as $history)
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" class="checkbox-item" name="ids[]" value="{{ $history->id }}">
                                                </td>
                                                <td>
                                                    <strong>{{ $history->aset->merk }} {{ $history->aset->type }}</strong><br>
                                                    <small><code>{{ $history->aset_kode_tag }}</code></small>
                                                </td>
                                                <td>{{ $history->pegawai->nama }}</td>
                                                <td>{{ \Carbon\Carbon::parse($history->tanggal_serah)->format('d M Y') }}</td>
                                                <td>
                                                <!-- <button type="button" class="btn btn-info btn-sm btn-kembalikan-single" 
                                                        data-url="{{ route('pemakaian.kembalikan', $history->id) }}"
                                                        data-aset-nama="{{ $history->aset->merk }} {{ $history->aset->type }}">
                                                    Kembalikan
                                                </button> -->
                                                    <button type="button" class="btn btn-warning btn-sm btn-cetak" 
                                                            data-toggle="modal" 
                                                            data-target="#modal-cetak-serah-terima"
                                                            data-history='@json($history)'>
                                                        Cetak 
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- PERBAIKAN: Kode modal sekarang di "push" ke stack bernama 'modals' --}}
@push('modals')
    @include('pemakaian.partials.modals-serah-terima')
    @include('pemakaian.partials.modals-cetak')
@endpush

@push('scripts')
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/izitoast/dist/js/iziToast.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
         $(document).ready(function() {
            // Inisialisasi DataTable
            $("#pemakaian-table").DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json" },
                "columnDefs": [ { "targets": 0, "orderable": false } ]
            });

            // Notifikasi
            @if(session('success')) iziToast.success({ title: 'Berhasil!', message: '{{ session("success") }}', position: 'topRight' }); @endif
            @if(session('error')) iziToast.error({ title: 'Gagal!', message: '{{ session("error") }}', position: 'topRight' }); @endif
            
            // Modal Serah Terima
            $('#modal-serah-terima-aset').on('shown.bs.modal', function() { $(this).find('.select2').select2({ dropdownParent: $(this) }); });
            @if ($errors->any()) $('#modal-serah-terima-aset').modal('show'); @endif

            // Tombol Kembalikan Terpilih (Show/Hide)
            function toggleKembalikanButton() {
                var checkedCount = $('.checkbox-item:checked').length;
                if (checkedCount > 0) {
                    $('#btn-kembalikan-terpilih').fadeIn('fast');
                } else {
                    $('#btn-kembalikan-terpilih').fadeOut('fast');
                }
            }
            $('#checkbox-all').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('.checkbox-item').prop('checked', isChecked).trigger('change');
            });
            $('#pemakaian-table tbody').on('change', '.checkbox-item', function() {
                toggleKembalikanButton();
            });

            // Tombol Kembalikan Terpilih (SweetAlert)
            $('#btn-kembalikan-terpilih').on('click', function(e) {
                e.preventDefault();
                var form = $('#form-kembalikan-multiple');
                var checkedCount = form.find('input[name="ids[]"]:checked').length;
                if (checkedCount === 0) {
                    Swal.fire('Perhatian!', 'Pilih setidaknya satu aset untuk dikembalikan.', 'warning');
                    return;
                }
                Swal.fire({
                    title: 'Apakah Anda yakin?', text: "Anda akan mengembalikan " + checkedCount + " aset yang dipilih.",
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33', confirmButtonText: 'Ya, kembalikan!', cancelButtonText: 'Batal'
                }).then((result) => { if (result.isConfirmed) { form.submit(); } });
            });

            // Modal Cetak Manual
            $('#pemakaian-table tbody').on('click', '.btn-cetak', function() {
                var history = $(this).data('history');
                var modal = $('#modal-cetak-serah-terima');
                
                // Mengisi data yang sudah ada (otomatis)
                modal.find('#cetak_histori_id').val(history.id);
                modal.find('#cetak_nama_lengkap').val(history.pegawai.nama);
                modal.find('#cetak_nama_aset').val(history.aset.merk + ' ' + history.aset.type);
                modal.find('#cetak_tipe_aset').val(history.aset.kategori.nama);
                modal.find('#cetak_no_inventaris').val(history.aset_kode_tag);
                modal.find('#cetak_tanggal_penerimaan').val(history.tanggal_serah.substring(0, 10));

                // Mengisi field manual dengan data dari pegawai jika ada, sekaligus mengatur placeholder
                modal.find('#cetak_nik').val(history.pegawai.nik || '').attr('placeholder', history.pegawai.nik || 'Isi NIK Pegawai...');
                modal.find('#cetak_posisi').val(history.pegawai.jabatan || '').attr('placeholder', history.pegawai.jabatan || 'Isi Posisi Saat Ini...');
                modal.find('#cetak_tanggal_masuk').val(history.pegawai.tanggal_masuk || '');
                modal.find('#cetak_cabang').val(history.pegawai.cabang || '').attr('placeholder', history.pegawai.cabang || 'Isi Kantor Cabang...');
            });
         });
    </script>
@endpush
