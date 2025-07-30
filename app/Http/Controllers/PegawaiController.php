<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Lokasi;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    public function index()
    {
        // Ambil semua data pegawai dengan relasinya
        $pegawais = Pegawai::with(['departemen', 'lokasi'])->latest()->get();
        
        // Ambil data untuk dropdown di modal
        $lokasis = Lokasi::orderBy('nama')->get();
        $departemens = Departemen::orderBy('nama')->get();
        
        return view('pegawai.list-pegawai', compact('pegawais', 'lokasis', 'departemens'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:pegawais,email',
            'no_telp' => 'nullable|string|max:20',
            'departemen_id' => 'required', // Hapus validasi exists
            'lokasi_id' => 'required',     // Hapus validasi exists
        ]);

        // Logika untuk membuat departemen baru jika diketik
        $departemenId = $request->input('departemen_id');
        if (!is_numeric($departemenId)) {
            $departemen = Departemen::firstOrCreate(['nama' => $departemenId]);
            $validated['departemen_id'] = $departemen->id;
        }

        // Logika untuk membuat lokasi baru jika diketik
        $lokasiId = $request->input('lokasi_id');
        if (!is_numeric($lokasiId)) {
            $lokasi = Lokasi::firstOrCreate(['nama' => $lokasiId]);
            $validated['lokasi_id'] = $lokasi->id;
        }

        Pegawai::create($validated);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('pegawais')->ignore($pegawai->id)],
            'no_telp' => 'nullable|string|max:20',
            'departemen_id' => 'required',
            'lokasi_id' => 'required',
        ]);

        // Logika untuk membuat departemen baru jika diketik
        $departemenId = $request->input('departemen_id');
        if (!is_numeric($departemenId)) {
            $departemen = Departemen::firstOrCreate(['nama' => $departemenId]);
            $validated['departemen_id'] = $departemen->id;
        }

        // Logika untuk membuat lokasi baru jika diketik
        $lokasiId = $request->input('lokasi_id');
        if (!is_numeric($lokasiId)) {
            $lokasi = Lokasi::firstOrCreate(['nama' => $lokasiId]);
            $validated['lokasi_id'] = $lokasi->id;
        }

        $pegawai->update($validated);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        if ($pegawai->asets()->count() > 0) {
            return back()->with('error', 'Pegawai tidak dapat dihapus karena masih menjadi penanggung jawab aset.');
        }
        if ($pegawai->historiPemakaians()->count() > 0) {
            return back()->with('error', 'Pegawai tidak dapat dihapus karena memiliki riwayat pemakaian aset.');
        }

        $pegawai->delete();
        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
    }
}
