<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    /**
     * Metode ini sudah tidak terpakai dan bisa dihapus.
     */
    // public function index() { ... }

    /**
     * Menyimpan data baru.
     */
    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255|unique:lokasis,nama']);
        Lokasi::create($request->all());

        // PASTIKAN REDIRECT MENGARAH KE 'master-data.index'
        return redirect()->route('master-data.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    /**
     * Memperbarui data.
     */
    public function update(Request $request, Lokasi $lokasi)
    {
        $request->validate(['nama' => 'required|string|max:255|unique:lokasis,nama,' . $lokasi->id]);
        $lokasi->update($request->all());

        // PASTIKAN REDIRECT MENGARAH KE 'master-data.index'
        return redirect()->route('master-data.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    /**
     * Menghapus data.
     */
    public function destroy(Lokasi $lokasi)
    {
        // PERBAIKAN: Periksa apakah ada pegawai yang masih terhubung dengan lokasi ini
        // Menggunakan relasi 'pegawais()' yang sudah Anda definisikan.
        if ($lokasi->pegawais()->count() > 0) {
            // Tampilkan pesan error ke pengguna
            return redirect()->route('master-data.index')->with('error', 'Tidak bisa menghapus lokasi karena masih ada pegawai yang terhubung.');
        }

        // Jika tidak ada pegawai yang terhubung, hapus lokasinya
        $lokasi->delete();
        return redirect()->route('master-data.index')->with('success', 'Lokasi berhasil dihapus.');
    }
}