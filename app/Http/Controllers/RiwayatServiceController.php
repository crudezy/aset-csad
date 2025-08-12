<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\StatusAset;
use App\Models\RiwayatService;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

class RiwayatServiceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. MEMULAI QUERY UNTUK RIWAYAT SERVICE
        $riwayatServicesQuery = RiwayatService::with(['aset', 'vendor'])
                                               ->whereNull('tanggal_selesai_service')
                                               ->latest('tanggal_masuk_service');
        
        // 2. TERAPKAN FILTER JIKA BUKAN SUPERADMIN
        if ($user->role !== 'superadmin') {
            $riwayatServicesQuery->whereHas('aset', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            });
        }
        
        // 3. AMBIL HASILNYA
        $riwayatServices = $riwayatServicesQuery->get();

        // PERBAIKAN: Mengembalikan nama kolom yang benar di tabel vendors
        $vendors = Vendor::orderBy('nama_vendor')->get(); 

        return view('riwayat-service.list-service', compact('riwayatServices', 'vendors'));
    }
    
    public function store(Request $request)
    {
        // PENTING: Ubah validasi vendor_id agar bisa menerima string (untuk nama vendor baru)
        $validated = $request->validate([
            'hidden_aset_kode_tag'  => 'required|exists:asets,kode_tag',
            'deskripsi_kerusakan'   => 'required|string',
            'tanggal_masuk_service' => 'required|date',
            'perkiraan_selesai'     => 'nullable|date|after_or_equal:tanggal_masuk_service',
            'vendor_id'             => 'required', // Hapus exists:vendors,id
        ]);

        // LOGIKA BARU: Tangani input vendor_id yang bisa berupa ID atau nama baru
        $vendorId = $request->input('vendor_id');
        if (!is_numeric($vendorId)) {
            // Jika input bukan angka, buat vendor baru
            $vendor = Vendor::firstOrCreate(['nama_vendor' => $vendorId]); // PERBAIKAN: Gunakan 'nama_vendor'
            $validated['vendor_id'] = $vendor->id;
        }

        $aset = Aset::findOrFail($validated['hidden_aset_kode_tag']);
        $statusRusak = StatusAset::whereRaw('LOWER(nama) = ?', ['rusak'])->first();
        $statusPerbaikan = StatusAset::whereRaw('LOWER(nama) = ?', ['dalam perbaikan'])->first();

        if (!$statusRusak || !$statusPerbaikan) {
            return back()->with('error', 'Status Aset "Rusak" atau "Dalam Perbaikan" tidak ditemukan.');
        }

        $aset->status_id = $statusPerbaikan->id;
        $aset->save();

        RiwayatService::create([
            'aset_kode_tag'         => $aset->kode_tag,
            'deskripsi_kerusakan'   => $validated['deskripsi_kerusakan'],
            'tanggal_masuk_service' => $validated['tanggal_masuk_service'],
            'perkiraan_selesai'     => $validated['perkiraan_selesai'],
            'vendor_id'             => $validated['vendor_id'],
        ]);

        return redirect()->route('riwayat-service.index')->with('success', 'Catatan service berhasil ditambahkan.');
    }

    public function update(Request $request, RiwayatService $riwayatService)
    {
        $validated = $request->validate([
            'tanggal_selesai_service' => 'required|date|after_or_equal:tanggal_masuk_service',
            'tindakan_perbaikan'      => 'nullable|string',
            'biaya_service'           => 'nullable|numeric|min:0',
        ]);

        $riwayatService->update($validated);

        $historiAktif = $riwayatService->aset->historiPemakaians()
                                             ->whereNull('tanggal_kembali')
                                             ->first();

        if ($historiAktif) {
            $statusDigunakan = StatusAset::whereRaw('LOWER(nama) = ?', ['digunakan'])->first();
            if ($statusDigunakan) {
                $riwayatService->aset->update(['status_id' => $statusDigunakan->id]);
            }
        } else {
            $statusTersedia = StatusAset::whereRaw('LOWER(nama) = ?', ['tersedia'])->first();
            if ($statusTersedia) {
                $riwayatService->aset->update(['status_id' => $statusTersedia->id]);
            }
        }

        return redirect()->back()->with('success', 'Service telah diselesaikan.');
    }

    public function searchAset(Request $request)
    {
        $user = auth()->user();
        $searchTerm = $request->query('q');

        $statusRusak = StatusAset::whereRaw('LOWER(nama) = ?', ['rusak'])->first();
        if (!$statusRusak) {
            return response()->json(['results' => []]);
        }

        $query = Aset::query();
        $query->where('status_id', $statusRusak->id);

        if ($user->role !== 'superadmin') {
            $query->where('department_id', $user->department_id);
        }

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode_tag', 'like', "%{$searchTerm}%")
                ->orWhere('merk', 'like', "%{$searchTerm}%")
                ->orWhere('type', 'like', "%{$searchTerm}%");
            });
        }

        $asets = $query->take(10)->get();

        $results = $asets->map(function ($aset) use ($statusRusak) {
            return [
                'id' => $aset->kode_tag,
                'text' => "{$aset->merk} {$aset->type} ({$aset->kode_tag})",
                'is_rusak' => $aset->status_id === $statusRusak->id,
            ];
        });

        return response()->json(['results' => $results]);
    }
}
