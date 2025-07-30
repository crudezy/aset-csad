<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\RiwayatService;
use App\Models\StatusAset;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatServiceController extends Controller
{
    /**
     * Menampilkan daftar service yang sedang aktif.
     */
    public function index()
    {
        $riwayatServices = RiwayatService::whereNull('tanggal_selesai_service')
            ->with(['aset', 'vendor'])
            ->latest('tanggal_masuk_service')
            ->get();
            
        $vendors = Vendor::orderBy('nama_vendor')->get();

        return view('riwayat-service.list-service', compact('riwayatServices', 'vendors'));
    }

    /**
     * Menyimpan catatan service baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'aset_kode_tag'         => 'required|exists:asets,kode_tag',
            'deskripsi_kerusakan'   => 'required|string',
            'tanggal_masuk_service' => 'required|date',
            'perkiraan_selesai'     => 'nullable|date|after_or_equal:tanggal_masuk_service',
            'vendor_id'             => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $vendorId = $request->vendor_id;
            if ($vendorId && !is_numeric($vendorId)) {
                $newVendor = Vendor::firstOrCreate(['nama_vendor' => $vendorId]);
                $vendorId = $newVendor->id;
            }

            $aset = Aset::where('kode_tag', $validated['aset_kode_tag'])->firstOrFail();
            $statusPerbaikan = StatusAset::whereRaw('LOWER(nama) = ?', ['dalam perbaikan'])->first();
            
            if ($statusPerbaikan) {
                $aset->status_id = $statusPerbaikan->id;
                $aset->save();
            }
            
            RiwayatService::create([
                'aset_kode_tag' => $validated['aset_kode_tag'],
                'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
                'tanggal_masuk_service' => $validated['tanggal_masuk_service'],
                'perkiraan_selesai' => $validated['perkiraan_selesai'],
                'vendor_id' => $vendorId,
            ]);

            DB::commit();
            return redirect()->route('riwayat-service.index')->with('success', 'Catatan service baru berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui catatan service menjadi selesai.
     */
    public function update(Request $request, RiwayatService $riwayatService)
    {
        $validated = $request->validate([
            'tindakan_perbaikan'      => 'nullable|string',
            'biaya_service'           => 'nullable|numeric',
            'tanggal_selesai_service' => 'required|date|after_or_equal:tanggal_masuk_service',
        ]);

        DB::beginTransaction();
        try {
            $riwayatService->update($validated);
            
            $aset = $riwayatService->aset;
            if ($aset) {
                $historiAktif = $aset->historiPemakaians()->whereNull('tanggal_kembali')->latest('tanggal_serah')->first();
                $targetStatus = $historiAktif ? 'digunakan' : 'tersedia';
                $statusAset = StatusAset::whereRaw('LOWER(nama) = ?', [$targetStatus])->first();
                
                if ($statusAset) {
                    $aset->status_id = $statusAset->id;
                    $aset->save();
                }
            }

            DB::commit();
            return redirect()->route('riwayat-service.index')->with('success', 'Riwayat service berhasil diselesaikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Menyediakan data aset untuk pencarian AJAX di Select2.
     */
    public function searchAset(Request $request)
    {
        // PERBAIKAN: Mengembalikan logika lama sesuai permintaan
        // 1. Cari ID untuk status 'rusak'
        $statusRusak = StatusAset::whereRaw('LOWER(nama) = ?', ['rusak'])->first();
    
        if (!$statusRusak) {
            return response()->json(['results' => []]);
        }
    
        // 2. Mulai query dengan filter HANYA untuk aset yang rusak
        $query = Aset::where('status_id', $statusRusak->id);
    
        // 3. Tambahkan filter pencarian berdasarkan ketikan user
        $search = $request->input('q');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_tag', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }
    
        // 4. Ambil data
        $asets = $query->limit(10)->get();
    
        // 5. Format hasilnya
        $results = $asets->map(function($aset) {
            return [
                'id'       => $aset->kode_tag,
                'text'     => "{$aset->merk} {$aset->type} ({$aset->kode_tag})",
                'is_rusak' => true // Karena kita hanya mencari yang rusak, ini selalu true
            ];
        });
    
        return response()->json(['results' => $results]);
    }
}
