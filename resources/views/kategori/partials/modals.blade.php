{{-- Modal Tambah Kategori --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-tambah-kategori">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('kategori.store') }}" method="POST" class="needs-validation" novalidate="">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama" class="form-control" required="">
                        <div class="invalid-feedback">
                            Nama kategori tidak boleh kosong.
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Prefix</label>
                        <input type="text" name="prefix" class="form-control" required="">
                        <div class="invalid-feedback">
                            Prefix tidak boleh kosong.
                        </div>
                        <small class="form-text text-muted">Contoh: CSADLP untuk Laptop. Maksimal 10 karakter.</small>
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

{{-- Modal Edit Kategori --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-edit-kategori">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-edit-kategori" method="POST" class="needs-validation" novalidate="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                     <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama" id="edit-nama" class="form-control" required="">
                        <div class="invalid-feedback">
                            Nama kategori tidak boleh kosong.
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Prefix</label>
                        <input type="text" name="prefix" id="edit-prefix" class="form-control" required="">
                        <div class="invalid-feedback">
                            Prefix tidak boleh kosong.
                        </div>
                        <small class="form-text text-muted">Contoh: CSADLP untuk Laptop. Maksimal 10 karakter.</small>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Hapus Kategori sudah dihapus dari sini --}}
