<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Kategori;
use App\Models\StatusAset;
use App\Models\Vendor;
use App\Models\Pegawai;
use App\Models\HistoriPemakaian;
use App\Models\RiwayatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\AsetsExport; 
use Maatwebsite\Excel\Facades\Excel; 



class AsetController extends Controller
{
    /**
     * Menampilkan daftar semua aset dan menyediakan data untuk modal.
     */
    public function index()
    {
        $asets = Aset::with(['kategori', 'statusAset', 'pemegangTerakhir.pegawai'])
            ->where('department_id', auth()->user()->department_id)
            ->latest()
            ->get();
        
        $kategoris = Kategori::orderBy('nama')->get();
        $statusAsets = StatusAset::all();
        $vendors = Vendor::orderBy('nama_vendor')->get();

        // PERBAIKAN: Mengarahkan ke view yang benar (index.blade.php)
        return view('aset.list-aset', compact('asets', 'kategoris', 'statusAsets', 'vendors'));
    }

    /**
     * Menyimpan aset baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'serial_number' => 'nullable|string|max:255|unique:asets,serial_number',
            'merk' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'spesifikasi' => 'nullable|string',
            'tanggal_pembelian' => 'nullable|date',
            'harga_beli' => 'nullable|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string',
            'kategori_id' => 'required|exists:kategoris,id',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);
        
        $request->merge(['form_type' => 'tambah_aset']);

        $statusTersedia = StatusAset::where('nama', 'Tersedia')->first();
        if (!$statusTersedia) {
            return back()->with('error', 'Status default "Tersedia" tidak ditemukan.');
        }
        $validated['status_id'] = $statusTersedia->id;

        $kategori = Kategori::findOrFail($validated['kategori_id']);
        $prefix = $kategori->prefix;
        $lastAsset = Aset::where('kode_tag', 'LIKE', $prefix . '%')->latest('kode_tag')->first();
        $nextNumber = 1;
        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->kode_tag, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        }
        $newKodeTag = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('public/asets');
            $validated['gambar'] = $path;
        }

        $validated['kode_tag'] = $newKodeTag;
        $validated['department_id'] = auth()->user()->department_id;
        Aset::create($validated);

        return redirect()->route('aset.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    /**
     * Memperbarui data aset.
     */
    public function update(Request $request, Aset $aset)
    {
        $validated = $request->validate([
            'serial_number' => 'nullable|string|max:255|unique:asets,serial_number,' . $aset->kode_tag . ',kode_tag',
            'merk' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'spesifikasi' => 'nullable|string',
            'tanggal_pembelian' => 'nullable|date',
            'harga_beli' => 'nullable|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string',
            'status_id' => 'required|exists:status_asets,id',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);
        
        $request->merge(['form_type' => 'edit_aset']);

        if ($request->hasFile('gambar')) {
            if ($aset->gambar) {
                Storage::delete($aset->gambar);
            }
            $path = $request->file('gambar')->store('public/asets');
            $validated['gambar'] = $path;
        }

        $aset->update($validated);

        return redirect()->route('aset.index')->with('success', 'Aset berhasil diperbarui.');
    }

    /**
     * Menampilkan detail aset.
     */
    public function show(Aset $aset)
    {
        $aset->load(['kategori', 'statusAset', 'vendor', 'riwayatServices', 'historiPemakaians.pegawai']);
        
        // PERBAIKAN: Ambil dan kirim data yang dibutuhkan oleh modal
        $vendors = Vendor::orderBy('nama_vendor')->get();
        $kategoris = Kategori::all();
        $statuses = StatusAset::all();

        return view('aset.detail-aset', compact('aset', 'vendors', 'kategoris', 'statuses'));
    }

    /**
     * Menghapus satu aset.
     */
    public function destroy(Aset $aset)
    {
        if ($aset->riwayatServices()->count() > 0 || $aset->historiPemakaians()->count() > 0) {
            return back()->with('error', 'Aset tidak dapat dihapus karena memiliki data riwayat.');
        }

        if ($aset->gambar) {
            Storage::delete($aset->gambar);
        }

        $aset->delete();
        return redirect()->route('aset.index')->with('success', 'Aset berhasil dihapus.');
    }

    /**
     * Menghapus semua aset.
     */
    public function destroyAll()
    {
        $asets = Aset::all();
        foreach ($asets as $aset) {
            if ($aset->gambar) {
                Storage::delete($aset->gambar);
            }
        }
        
        HistoriPemakaian::truncate();
        RiwayatService::truncate();
        
        Aset::truncate();

        return redirect()->route('aset.index')->with('success', 'Semua data aset berhasil dihapus.');
    }
    public function exportExcel()
    {
        // Panggil class AsetsExport dan tentukan nama file yang akan diunduh
        return Excel::download(new AsetsExport, 'laporan-data-aset.xlsx');
    }
    public function cetakLabelMultiple(Request $request)
    {
        $selectedKodeTags = $request->input('kode_tags'); // Ini akan menjadi array kode_tag yang dipilih

        if (empty($selectedKodeTags)) {
            return back()->with('error', 'Tidak ada aset yang dipilih untuk dicetak labelnya.');
        }

        // Ambil data aset berdasarkan kode_tag yang dipilih
        $asetsToPrint = Aset::whereIn('kode_tag', $selectedKodeTags)->get();

        // Contoh sederhana: Mengarahkan ke view yang bisa Anda desain untuk cetak
        return view('aset.cetak-label', compact('asetsToPrint'));
    }
    public function showPublic($kode_tag)
    {
        // Cari aset berdasarkan kode_tag, jika tidak ketemu akan error 404
        $aset = \App\Models\Aset::where('kode_tag', $kode_tag)->firstOrFail();

        // Arahkan ke view baru yang akan kita buat
        return view('aset.detail-publik', compact('aset'));
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'kode_tags' => 'required|array',
            'kode_tags.*' => 'string|exists:asets,kode_tag',
            'status' => 'required|string'
        ]);

        $statusRusak = StatusAset::where('nama', 'rusak')->first();

        if (!$statusRusak) {
            return response()->json(['message' => 'Status "Rusak" tidak ditemukan.'], 404);
        }

        Aset::whereIn('kode_tag', $request->kode_tags)->update(['status_id' => $statusRusak->id]);

        return response()->json(['message' => 'Status aset berhasil diperbarui menjadi Rusak.']);
    }
}
