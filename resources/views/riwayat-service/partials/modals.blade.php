{{-- Modal untuk Tambah Catatan Service --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-tambah-service">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Catatan Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('riwayat-service.store') }}" method="POST">
                @csrf
                <input type="hidden" name="hidden_aset_kode_tag" id="hidden_aset_kode_tag_tambah">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="aset_kode_tag_tambah">Pilih Aset <span class="text-danger">*</span></label>
                        <select class="form-control" id="aset_kode_tag_tambah" name="aset_kode_tag_select" required style="width: 100%;"></select>
                    </div>
                    
                    <div class="form-group">
                        <label for="vendor_id_tambah">Vendor Service (Opsional)</label>
                        {{-- PERBAIKAN: Tambahkan kelas select2-tags --}}
                        <select class="select2-tags" id="vendor_id_tambah" name="vendor_id" style="width: 100%;">
                            <option value="">Pilih atau ketik baru</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->nama_vendor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi_kerusakan_tambah">Deskripsi Kerusakan <span class="text-danger">*</span></label>
                        <textarea id="deskripsi_kerusakan_tambah" name="deskripsi_kerusakan" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_masuk_service_tambah">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input id="tanggal_masuk_service_tambah" type="date" name="tanggal_masuk_service" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="perkiraan_selesai_tambah">Perkiraan Selesai (Opsional)</label>
                                <input id="perkiraan_selesai_tambah" type="date" name="perkiraan_selesai" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Modal untuk Selesaikan Service --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-selesaikan-service">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selesaikan Catatan Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="form-selesaikan-service">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <h5>Detail Aset & Kerusakan</h5>
                    <div class="form-group">
                        <label>Aset</label>
                        <p class="form-control-plaintext" id="detail_aset"></p>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Kerusakan Awal</label>
                        <p class="form-control-plaintext" id="detail_kerusakan"></p>
                    </div>
                    <hr>
                    <h5>Form Penyelesaian</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_selesai_service">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input id="tanggal_selesai_service" type="date" name="tanggal_selesai_service" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label for="biaya_service">Biaya Service (Opsional)</label>
                                <input id="biaya_service" type="number" name="biaya_service" class="form-control" placeholder="Contoh: 50000">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tindakan_perbaikan">Tindakan Perbaikan yang Dilakukan (Opsional)</label>
                        <textarea id="tindakan_perbaikan" name="tindakan_perbaikan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Selesaikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
