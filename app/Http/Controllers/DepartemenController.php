<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;
use Exception;

class DepartemenController extends Controller
{
    public function index()
    {
        $departemens = Departemen::latest()->paginate(10);
        return view('master-data.index', compact('departemens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:departemens,nama'
        ]);
        Departemen::create($validated);
        return redirect()->route('master-data.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function update(Request $request, Departemen $departemen)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:departemens,nama,' . $departemen->id
        ]);
        $departemen->update($validated);
        return redirect()->route('master-data.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Departemen $departemen)
    {
        try {
            // Pengecekan relasi dengan tabel 'pegawais'
            if ($departemen->pegawais()->count() > 0) {
                return back()->with('error', 'Departemen tidak dapat dihapus karena masih digunakan oleh pegawai.');
            }

            // Pengecekan relasi dengan tabel 'users'
            if ($departemen->users()->count() > 0) {
                return back()->with('error', 'Departemen tidak dapat dihapus karena masih digunakan oleh pengguna.');
            }

            // Pengecekan relasi dengan tabel 'asets' (jika ada)
            // if ($departemen->asets()->count() > 0) {
            //     return back()->with('error', 'Departemen tidak dapat dihapus karena masih terkait dengan aset.');
            // }

            // Ganti $departemen->delete() dengan metode destroy untuk keandalan yang lebih baik
            $deletedRows = Departemen::destroy($departemen->id);

            // Perbaikan: Tambahkan pengecekan apakah ada baris yang dihapus
            if ($deletedRows > 0) {
                 return redirect()->route('master-data.index')->with('success', 'Departemen berhasil dihapus.');
            } else {
                 return back()->with('error', 'Gagal menghapus departemen.');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus departemen. Pastikan tidak ada data terkait lainnya.');
        }
    }
}
