{{-- resources/views/pemakaian/partials/modals-serah-terima.blade.php --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modal-serah-terima-aset">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Serah Terima Aset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pemakaian.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="aset_kode_tag_modal">Pilih Aset (Hanya yang Tersedia)</label>
                        <select name="aset_kode_tag" id="aset_kode_tag_modal" class="form-control select2 @error('aset_kode_tag') is-invalid @enderror" required style="width: 100%;">
                            <option value="">-- Pilih Aset --</option>
                            @foreach ($asetsTersedia as $aset)
                                <option value="{{ $aset->kode_tag }}" {{ old('aset_kode_tag') == $aset->kode_tag ? 'selected' : '' }}>
                                    {{ $aset->merk }} {{ $aset->type }} ({{ $aset->kode_tag }})
                                </option>
                            @endforeach
                        </select>
                        @error('aset_kode_tag')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pegawai_id_modal">Serahkan Kepada</label>
                        <select name="pegawai_id" id="pegawai_id_modal" class="form-control select2 @error('pegawai_id') is-invalid @enderror" required style="width: 100%;">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach ($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}" {{ old('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                    {{ $pegawai->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('pegawai_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
    
                    <div class="form-group">
                        <label for="tanggal_serah">Tanggal Serah</label>
                        <input type="date" name="tanggal_serah" id="tanggal_serah" 
                                class="form-control @error('tanggal_serah') is-invalid @enderror" 
                                value="{{ old('tanggal_serah', date('Y-m-d')) }}" required>
                        @error('tanggal_serah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Serahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>