{{-- Modal Tambah Vendor --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-tambah-vendor">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Vendor Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('vendor.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Vendor</label>
                        <input type="text" name="nama_vendor" class="form-control @error('nama_vendor') is-invalid @enderror" value="{{ old('nama_vendor') }}" required>
                        @error('nama_vendor')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Kontak (Opsional)</label>
                        <input type="text" name="kontak" class="form-control" value="{{ old('kontak') }}">
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

{{-- Modal Edit Vendor --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-edit-vendor">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Vendor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-edit-vendor" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                      <div class="form-group">
                        <label>Nama Vendor</label>
                        <input type="text" name="nama_vendor" id="edit-nama_vendor" class="form-control @error('nama_vendor') is-invalid @enderror" required>
                        @error('nama_vendor')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Kontak (Opsional)</label>
                        <input type="text" name="kontak" id="edit-kontak" class="form-control">
                    </div>
                    {{-- Hidden input untuk membantu deteksi modal mana yang harus dibuka saat validasi gagal --}}
                    <input type="hidden" name="_method" value="PUT">
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>