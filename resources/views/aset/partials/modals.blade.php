{{-- Modal Tambah Aset --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-tambah-aset">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Aset Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('aset.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="merk">Merk</label>
                            <input type="text" id="merk" name="merk" class="form-control @error('merk') is-invalid @enderror" value="{{ old('merk') }}">
                            @error('merk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="type">Tipe</label>
                            <input type="text" id="type" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type') }}">
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="serial_number">Serial Number</label>
                            <input type="text" id="serial_number" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror" value="{{ old('serial_number') }}">
                            @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tanggal_pembelian">Tanggal Pembelian</label>
                            <input type="date" id="tanggal_pembelian" name="tanggal_pembelian" class="form-control @error('tanggal_pembelian') is-invalid @enderror" value="{{ old('tanggal_pembelian') }}">
                            @error('tanggal_pembelian')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="kategori_id">Kategori</label>
                            <select id="kategori_id" name="kategori_id" class="form-control select2 @error('kategori_id') is-invalid @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="vendor_id">Vendor</label>
                            <select id="vendor_id" name="vendor_id" class="form-control select2 @error('vendor_id') is-invalid @enderror">
                                <option value="">Pilih Vendor (Opsional)</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->nama_vendor }}</option>
                                @endforeach
                            </select>
                            @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    {{-- PERUBAHAN: Mengganti textarea menjadi input text --}}
                    <div class="form-group">
                        <label for="spesifikasi">Spesifikasi</label>
                        <input type="text" id="spesifikasi" name="spesifikasi" class="form-control @error('spesifikasi') is-invalid @enderror" value="{{ old('spesifikasi') }}">
                        @error('spesifikasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" style="height: 100px;">{{ old('keterangan') }}</textarea>
                        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="gambar">Gambar Aset</label>
                        <input type="file" id="gambar" name="gambar" class="form-control @error('gambar') is-invalid @enderror">
                        <small class="form-text text-muted">Opsional. Tipe file: jpg, jpeg, png. Maks: 2MB.</small>
                        @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

{{-- Modal Edit Aset --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-edit-aset">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-edit-aset" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Merk</label>
                            <input type="text" name="merk" id="edit-merk" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tipe</label>
                            <input type="text" name="type" id="edit-type" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Serial Number</label>
                            <input type="text" name="serial_number" id="edit-serial_number" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tanggal Pembelian</label>
                            <input type="date" name="tanggal_pembelian" id="edit-tanggal_pembelian" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Kategori</label>
                            <input type="text" id="edit-kategori" class="form-control" readonly>
                            <small class="form-text text-muted">Kategori tidak dapat diubah.</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Vendor</label>
                            <select name="vendor_id" id="edit-vendor_id" class="form-control select2">
                                <option value="">Pilih Vendor (Opsional)</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->nama_vendor }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- PERUBAHAN: Mengganti textarea menjadi input text --}}
                    <div class="form-group">
                        <label>Spesifikasi</label>
                        <input type="text" name="spesifikasi" id="edit-spesifikasi" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" id="edit-keterangan" class="form-control" style="height: 100px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Gambar Aset Baru (Opsional)</label>
                        <input type="file" name="gambar" class="form-control">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
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
