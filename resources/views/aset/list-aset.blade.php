@extends('layouts.app')

@section('title', 'Data Aset')

@push('style')
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/izitoast/dist/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
@endpush

@section('content')
    <div class="section-header">
        <h1>Aset</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Aset</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Data Aset</h4>
                        <div class="card-header-action">
                            <button type="submit" class="btn btn-warning mr-2" id="btn-cetak-terpilih" style="display: none;" form="form-cetak-label-multiple">
                                <i class="fas fa-print"></i> Cetak Label
                            </button>
                            <a href="{{ route('aset.export.excel') }}" class="btn btn-success mr-2">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah-aset">
                                <i class="fas fa-plus"></i> Tambah Baru
                            </button>
                            <!-- <form action="{{ route('aset.destroyAll') }}" method="POST" class="d-inline ml-2">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger confirm-delete-all">
                                    <i class="fas fa-trash-alt"></i> Hapus Semua
                                </button>
                            </form> -->
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Form untuk cetak label multiple sekarang hanya membungkus tabel --}}
                        <form id="form-cetak-label-multiple" action="{{ route('aset.cetak.label.multiple') }}" method="POST" target="_blank">
                            @csrf
                            {{-- Bagian d-flex justify-content-between align-items-center mb-3 yang tadinya ada tombol sekarang dihapus --}}
                            
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-aset">
                                    <thead>
                                        <tr>
                                            {{-- Kolom Checkbox untuk Pilih Semua --}}
                                            <th class="text-center" style="width: 5%;"><input type="checkbox" id="checkbox-all-aset"></th>
                                            <th class="text-center">No</th>
                                            <th>Aset</th>
                                            <th>Kategori</th>
                                            <th>Status</th>
                                            <th>Pegawai (PIC)</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($asets as $aset)
                                            <tr>
                                                <td class="text-center">
                                                    {{-- Checkbox individual untuk aset --}}
                                                    <input type="checkbox" class="checkbox-item-aset" name="kode_tags[]" value="{{ $aset->kode_tag }}">
                                                </td>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $aset->merk }} {{ $aset->type }}</strong><br>
                                                    <small><code>{{ $aset->kode_tag }}</code> / <code>{{ $aset->serial_number ?? 'N/A' }}</code></small>
                                                </td>
                                                <td>{{ $aset->kategori->nama }}</td>
                                                <td>
                                                    <x-status-badge :status="$aset->statusAset->nama" />
                                                </td>
                                                <td>
                                                    @if($aset->pemegangTerakhir && !$aset->pemegangTerakhir->tanggal_kembali)
                                                        {{ $aset->pemegangTerakhir->pegawai->nama }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('aset.show', $aset->kode_tag) }}" class="btn btn-secondary btn-sm">Detail</a>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit" data-aset='@json($aset)'>Edit</button>
                                                    @php
                                                        $statusAset = strtolower($aset->statusAset->nama);
                                                    @endphp
                                                    @if ($statusAset != 'rusak' && $statusAset != 'dalam perbaikan')
                                                        <button type="button" class="btn btn-danger btn-sm btn-tandai-rusak" data-kode-tag="{{ $aset->kode_tag }}">
                                                            </i> Rusak
                                                        </button>
                                                    @endif
                                                    <!-- @if (strtolower($aset->statusAset->nama) != 'digunakan')
                                                        <form action="{{ route('aset.destroy', $aset->kode_tag) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button class="btn btn-danger btn-sm confirm-delete">Hapus</button>
                                                        </form>
                                                    @endif -->
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form> {{-- Tutup form cetak label multiple --}}
                    </div>   
                </div>
            </div>
        </div>
    </div>
@endsection


@push('modals')
    @include('aset.partials.modals')
@endpush

@push('scripts')
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('library/izitoast/dist/js/iziToast.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#table-aset').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
                },
                // Tambahkan columnDefs untuk menghilangkan sorting pada 
                "columnDefs": [
                    { "targets": 0, "orderable": false }
                ]
            });

            $('.modal').on('shown.bs.modal', function () {
                $(this).find('.select2').select2({
                    dropdownParent: $(this)
                });
            });

            @if (session('success')) iziToast.success({ title: 'Berhasil!', message: '{{ session('success') }}', position: 'topRight' }); @endif
            @if (session('error')) iziToast.error({ title: 'Gagal!', message: '{{ session('error') }}', position: 'topRight' }); @endif

            // Logic untuk tombol Edit Aset (sama seperti sebelumnya)
            $('#table-aset tbody').on('click', '.btn-edit', function() {
                var aset = $(this).data('aset');
                var form = $('#form-edit-aset');
                var url = "{{ route('aset.update', ':id') }}";
                form.attr('action', url.replace(':id', aset.kode_tag));
                form.find('#edit-merk').val(aset.merk);
                form.find('#edit-type').val(aset.type);
                form.find('#edit-serial_number').val(aset.serial_number);
                form.find('#edit-tanggal_pembelian').val(aset.tanggal_pembelian);
                form.find('#edit-kategori_id').val(aset.kategori_id).trigger('change');
                // form.find('#edit-status_id').val(aset.status_id).trigger('change');
                form.find('#edit-vendor_id').val(aset.vendor_id).trigger('change');
                form.find('#edit-spesifikasi').val(aset.spesifikasi);
                form.find('#edit-keterangan').val(aset.keterangan);
                // Jika ada gambar, tampilkan preview atau isi field gambar lama
                // form.find('#gambar-preview-edit').attr('src', aset.gambar ? '{{ asset('storage') }}/' + aset.gambar : 'https://placehold.co/100x100');
                $('#modal-edit-aset').modal('show');
            });

            // Logic untuk konfirmasi hapus individual
            $('#table-aset tbody').on('click', '.confirm-delete', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?', text: "Data yang dihapus tidak dapat dikembalikan!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
                }).then((result) => { if (result.isConfirmed) { form.submit(); } });
            });

            // Logic untuk konfirmasi hapus semua
            $('.confirm-delete-all').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Anda akan menghapus SEMUA data aset secara permanen! Aksi ini tidak dapat dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Handle errors for modals
            @if ($errors->any())
                @if (old('form_type') === 'edit_aset')
                    $('#modal-edit-aset').modal('show');
                @elseif (old('form_type') === 'tambah_aset')
                    $('#modal-tambah-aset').modal('show');
                @endif
            @endif

            // --- Logika Checkbox dan Tombol Cetak Label Terpilih ---
            function toggleActionButtons() {
                var checkedCount = $('.checkbox-item-aset:checked').length;
                if (checkedCount > 0) {
                    $('#btn-cetak-terpilih').fadeIn('fast');
                    $('#btn-tandai-rusak').fadeIn('fast'); // Tampilkan tombol Tandai Rusak
                } else {
                    $('#btn-cetak-terpilih').fadeOut('fast');
                    $('#btn-tandai-rusak').fadeOut('fast'); // Sembunyikan tombol Tandai Rusak
                }
            }

            // Pilih Semua Checkbox
            $('#checkbox-all-aset').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('.checkbox-item-aset').prop('checked', isChecked).trigger('change');
            });

            // Checkbox Individual
            $('#table-aset tbody').on('change', '.checkbox-item-aset', function() {
                toggleActionButtons();
                // Jika ada item yang tidak terpilih, uncheck 'pilih semua'
                if (!$(this).is(':checked')) {
                    $('#checkbox-all-aset').prop('checked', false);
                } else {
                    // Jika semua item terpilih, check 'pilih semua'
                    if ($('.checkbox-item-aset:checked').length === $('.checkbox-item-aset').length) {
                        $('#checkbox-all-aset').prop('checked', true);
                    }
                }
            });

            // Tombol Cetak Label Terpilih (mengirimkan form)
            $('#btn-cetak-terpilih').on('click', function(e) {
                e.preventDefault();
                var form = $('#form-cetak-label-multiple');
                var checkedCount = form.find('input[name="kode_tags[]"]:checked').length;
                if (checkedCount === 0) {
                    Swal.fire('Perhatian!', 'Pilih setidaknya satu aset untuk dicetak labelnya.', 'warning');
                    return;
                }
                
                // Memicu submit form. Karena target="_blank", akan membuka di tab baru.
                form.submit();

                // Optional: reset checkboxes setelah cetak
                Swal.fire({
                    title: 'Pencetakan Dimulai',
                    text: 'Silakan cek tab baru untuk hasil cetak.',
                    icon: 'info',
                    showConfirmButton: false,
                    timer: 3000 // Tutup otomatis setelah 3 detik
                });
                $('.checkbox-item-aset').prop('checked', false);
                $('#checkbox-all-aset').prop('checked', false);
                toggleActionButtons(); // Sembunyikan tombol setelah aksi
            });
            $('#btn-tandai-rusak').on('click', function(e) {
                e.preventDefault();
                var selectedAset = [];
                $('.checkbox-item-aset:checked').each(function() {
                    selectedAset.push($(this).val());
                });

                if (selectedAset.length === 0) {
                    Swal.fire('Perhatian!', 'Pilih setidaknya satu aset untuk ditandai rusak.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Status aset yang dipilih akan diubah menjadi 'Rusak'!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Tandai Rusak!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("aset.update.status") }}', // Anda perlu membuat rute ini
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                kode_tags: selectedAset,
                                status: 'rusak' // atau ID status 'rusak'
                            },
                            success: function(response) {
                                iziToast.success({ title: 'Berhasil!', message: response.message, position: 'topRight' });
                                // Opsional: Reload halaman atau tabel data
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            },
                            error: function(xhr) {
                                iziToast.error({ title: 'Gagal!', message: 'Terjadi kesalahan saat memperbarui status.', position: 'topRight' });
                            }
                        });
                    }
                });
            });
            $('#table-aset').on('click', '.btn-tandai-rusak', function(e) {
                e.preventDefault();
                var kodeTag = $(this).data('kode-tag');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Status aset ini akan diubah menjadi 'Rusak'!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Tandai Rusak!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("aset.update.status") }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                kode_tags: [kodeTag], // Kirim dalam bentuk array
                                status: 'rusak'
                            },
                            success: function(response) {
                                iziToast.success({ title: 'Berhasil!', message: response.message, position: 'topRight' });
                                // Opsional: Muat ulang halaman setelah berhasil
                                setTimeout(function() {
                                    location.reload();
                                }, 1500);
                            },
                            error: function(xhr) {
                                iziToast.error({ title: 'Gagal!', message: 'Terjadi kesalahan saat memperbarui status.', position: 'topRight' });
                            }
                        });
                    }
                });
            });
            // Inisialisasi awal tombol
            toggleActionButtons();
        });
    </script>
@endpush