<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Lokasi;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

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
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:pegawais,email',
                'no_telp' => 'nullable|string|max:20',
                'department_id' => 'required',
                'lokasi_id' => 'required',
            ]);

            $departmentId = $request->input('department_id');
            if (!is_numeric($departmentId)) {
                $departemen = Departemen::firstOrCreate(['nama' => $departmentId]);
                $validated['department_id'] = $departemen->id;
            }

            $lokasiId = $request->input('lokasi_id');
            if (!is_numeric($lokasiId)) {
                $lokasi = Lokasi::firstOrCreate(['nama' => $lokasiId]);
                $validated['lokasi_id'] = $lokasi->id;
            }

            Pegawai::create($validated);

            return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');

        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return back()->with('error', 'Gagal menambahkan pegawai. Pastikan data yang dimasukkan valid.');
        }
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'email' => ['nullable', 'email', 'max:255', Rule::unique('pegawais')->ignore($pegawai->id)],
                'no_telp' => 'nullable|string|max:20',
                'department_id' => 'required',
                'lokasi_id' => 'required',
            ]);

            $departmentId = $request->input('department_id');
            if (!is_numeric($departmentId)) {
                $departemen = Departemen::firstOrCreate(['nama' => $departmentId]);
                $validated['department_id'] = $departemen->id;
            }

            $lokasiId = $request->input('lokasi_id');
            if (!is_numeric($lokasiId)) {
                $lokasi = Lokasi::firstOrCreate(['nama' => $lokasiId]);
                $validated['lokasi_id'] = $lokasi->id;
            }

            $pegawai->update($validated);

            return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil diperbarui.');

        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return back()->with('error', 'Gagal memperbarui pegawai. Pastikan data yang dimasukkan valid.');
        }
    }

    public function destroy(Pegawai $pegawai)
    {
        try {
            // Tambahkan logging untuk melihat data terkait sebelum penghapusan
            \Log::info("Mencoba menghapus pegawai dengan ID: " . $pegawai->id);
            \Log::info("Jumlah aset terkait: " . $pegawai->asets()->count());
            \Log::info("Jumlah histori pemakaian terkait: " . $pegawai->historiPemakaians()->count());

            if ($pegawai->asets()->count() > 0) {
                return back()->with('error', 'Pegawai tidak dapat dihapus karena masih menjadi penanggung jawab aset.');
            }
            if ($pegawai->historiPemakaians()->count() > 0) {
                return back()->with('error', 'Pegawai tidak dapat dihapus karena memiliki riwayat pemakaian aset.');
            }

            $pegawai->delete();
            return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            return back()->with('error', 'Gagal menghapus pegawai. Pastikan tidak ada data terkait lainnya.');
        }
    }
}
