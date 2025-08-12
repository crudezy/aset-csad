<div class="modal fade" tabindex="-1" role="dialog" id="modal-tambah-pegawai">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Karyawan Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pegawai.store') }}" method="POST" class="needs-validation" novalidate="">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Karyawan</label>
                        <input type="text" name="nama" class="form-control" required="">
                        <div class="invalid-feedback">Nama karyawan tidak boleh kosong.</div>
                    </div>
                    <div class="form-group">
                        <label>Email (Opsional)</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>No. Telp (Opsional)</label>
                        <input type="text" name="no_telp" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Departemen</label>
                        {{-- Ganti kelas menjadi select2-tags --}}
                        <select name="department_id" class="form-control select2-tags" required="">
                            <option value="">Pilih atau ketik baru</option>
                            @foreach ($departemens as $departemen)
                                <option value="{{ $departemen->id }}">{{ $departemen->nama }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Silakan pilih departemen.</div>
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                         {{-- Ganti kelas menjadi select2-tags --}}
                        <select name="lokasi_id" class="form-control select2-tags" required="">
                            <option value="">Pilih atau ketik baru</option>
                            @foreach ($lokasis as $lokasi)
                                <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Silakan pilih lokasi.</div>
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

{{-- Modal Edit Pegawai --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-edit-pegawai">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Karyawan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-edit-pegawai" method="POST" class="needs-validation" novalidate="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Karyawan</label>
                        <input type="text" name="nama" id="edit-nama" class="form-control" required="">
                        <div class="invalid-feedback">Nama karyawan tidak boleh kosong.</div>
                    </div>
                    <div class="form-group">
                        <label>Email (Opsional)</label>
                        <input type="email" name="email" id="edit-email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>No. Telp (Opsional)</label>
                        <input type="text" name="no_telp" id="edit-no_telp" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Departemen</label>
                         {{-- Ganti kelas menjadi select2-tags --}}
                        <select name="department_id" id="edit-department_id" class="form-control select2-tags" required="">
                            <option value="">Pilih atau ketik baru</option>
                            @foreach ($departemens as $departemen)
                                <option value="{{ $departemen->id }}">{{ $departemen->nama }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Silakan pilih departemen.</div>
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                         {{-- Ganti kelas menjadi select2-tags --}}
                        <select name="lokasi_id" id="edit-lokasi_id" class="form-control select2-tags" required="">
                            <option value="">Pilih atau ketik baru</option>
                            @foreach ($lokasis as $lokasi)
                                <option value="{{ $lokasi->id }}">{{ $lokasi->nama }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Silakan pilih lokasi.</div>
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
