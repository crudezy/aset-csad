{{-- resources/views/pemakaian/partials/modals-cetak.blade.php --}}
<div class="modal fade" id="modal-cetak-serah-terima" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        {{-- PERBAIKAN: Pindahkan <form> ke dalam <modal-content> --}}
        <div class="modal-content">
            <form action="{{ route('pemakaian.serah_terima.cetak') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="cetak_histori_id">
                
                <div class="modal-header">
                    <h5 class="modal-title">Form Cetak Serah Terima</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Data yang terisi otomatis dapat Anda ubah jika diperlukan.</p>
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Data Karyawan</h6>
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="cetak_nama_lengkap" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>NIK</label>
                                <input type="text" name="nik" id="cetak_nik" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Posisi Saat Ini</label>
                                <input type="text" name="posisi" id="cetak_posisi" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk" id="cetak_tanggal_masuk" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Kantor Cabang</label>
                                <input type="text" name="cabang" id="cetak_cabang" class="form-control" required>
                            </div>
                        </div>

                        {{-- Kolom untuk Data Aset --}}
                        <div class="col-md-6">
                            <h6>Data Aset</h6>
                            <div class="form-group">
                                <label>Nama / Jenis Aset</label>
                                <input type="text" id="cetak_nama_aset" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Tipe Aset</label>
                                <input type="text" id="cetak_tipe_aset" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>No. Inventaris</label>
                                <input type="text" id="cetak_no_inventaris" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Penerimaan</label>
                                <input type="date" name="tanggal_penerimaan" id="cetak_tanggal_penerimaan" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Cetak Dokumen</button>
                </div>
            </form>
        </div>
    </div>
</div>
