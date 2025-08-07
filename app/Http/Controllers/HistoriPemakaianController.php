<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Pegawai;
use App\Models\StatusAset;
use App\Models\HistoriPemakaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;

class HistoriPemakaianController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. MEMULAI QUERY UNTUK DAFTAR PEMAKAIAN
        $historiesQuery = HistoriPemakaian::with(['aset.kategori', 'pegawai'])
                                        ->whereNull('tanggal_kembali')
                                        ->latest('tanggal_serah');

        // 2. TERAPKAN FILTER JIKA BUKAN SUPERADMIN
        if ($user->role !== 'superadmin') {
            $historiesQuery->whereHas('aset', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            });
        }
        
        // 3. AMBIL HASILNYA
        $histories = $historiesQuery->get();

        // --- Logika untuk mengisi dropdown "Lakukan Serah Terima" ---
        $statusTersedia = StatusAset::whereRaw('LOWER(nama) = ?', ['tersedia'])->first();
        
        // 4. MEMULAI QUERY UNTUK ASET YANG TERSEDIA
        $asetsTersediaQuery = Aset::query();
        if ($statusTersedia) {
            $asetsTersediaQuery->where('status_id', $statusTersedia->id);
        }

        // 5. TERAPKAN FILTER DEPARTEMEN PADA ASET TERSEDIA
        if ($user->role !== 'superadmin') {
            $asetsTersediaQuery->where('department_id', $user->department_id);
        }
        
        // 6. AMBIL HASIL ASET TERSEDIA
        $asetsTersedia = $asetsTersediaQuery->get();

        $pegawais = Pegawai::orderBy('nama')->get();

        return view('pemakaian.index', compact('histories', 'asetsTersedia', 'pegawais'));
    }

    public function create()
    {
        $user = auth()->user();
        $statusTersedia = StatusAset::whereRaw('LOWER(nama) = ?', ['tersedia'])->first();

        // MEMULAI QUERY UNTUK ASET YANG TERSEDIA
        $asetsTersediaQuery = Aset::query();
        if ($statusTersedia) {
            $asetsTersediaQuery->where('status_id', $statusTersedia->id);
        }

        // TERAPKAN FILTER DEPARTEMEN PADA ASET TERSEDIA
        if ($user->role !== 'superadmin') {
            $asetsTersediaQuery->where('department_id', $user->department_id);
        }

        $asetsTersedia = $asetsTersediaQuery->get();
        $pegawais = Pegawai::orderBy('nama')->get();

        return view('pemakaian.create', compact('asetsTersedia', 'pegawais'));
    }

    // ... (Sisa method seperti store, kembalikan, dll. TIDAK PERLU DIUBAH) ...
    // Pastikan sisa kode Anda dari file asli tetap ada di sini.
    // Saya hanya menampilkan method yang diubah.
    
    /**
     * Menyimpan data serah terima dan mengubah status aset.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'aset_kode_tag' => 'required|exists:asets,kode_tag',
            'pegawai_id'    => 'required|exists:pegawais,id',
            'tanggal_serah' => 'required|date',
            'keterangan'    => 'nullable|string',
        ]);

        // 2. Buat catatan baru di histori pemakaian
        HistoriPemakaian::create($validated);

        // 3. Ubah status aset menjadi "Digunakan"
        $aset = Aset::find($validated['aset_kode_tag']);
        if ($aset) {
            $statusDigunakan = StatusAset::whereRaw('LOWER(nama) = ?', ['digunakan'])->first();
            if ($statusDigunakan) {
                $aset->status_id = $statusDigunakan->id;
                $aset->save();
            }
        }

        return redirect()->route('aset.index')->with('success', 'Aset berhasil diserahkan.');
    }

    /**
     * Memproses pengembalian aset.
     */
    public function kembalikan(Aset $aset)
    {
        $historiAktif = $aset->historiPemakaians()->whereNull('tanggal_kembali')->first();

        if ($historiAktif) {
            $historiAktif->update(['tanggal_kembali' => now()]);

            $statusTersedia = StatusAset::whereRaw('LOWER(nama) = ?', ['tersedia'])->first();
            if ($statusTersedia) {
                $aset->status_id = $statusTersedia->id;
                $aset->save();
            }

            return redirect()->route('aset.index')->with('success', 'Aset berhasil dikembalikan.');
        }

        return redirect()->route('aset.index')->with('error', 'Tidak ditemukan data pemakaian aktif untuk aset ini.');
    }
    
    public function kembalikanMultiple(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:histori_pemakaians,id',
        ]);

        $historiIds = $request->input('ids');
        $asetKodeTags = HistoriPemakaian::whereIn('id', $historiIds)->pluck('aset_kode_tag');
        $statusTersedia = StatusAset::whereRaw('LOWER(nama) = ?', ['tersedia'])->first();
        if (!$statusTersedia) {
            return back()->with('error', 'Status "Tersedia" tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            HistoriPemakaian::whereIn('id', $historiIds)->update(['tanggal_kembali' => now()]);
            Aset::whereIn('kode_tag', $asetKodeTags)->update(['status_id' => $statusTersedia->id]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mengembalikan aset.');
        }

        return redirect()->route('pemakaian.index')->with('success', count($historiIds) . ' aset berhasil dikembalikan.');
    }
    
    public function cetakManual(Request $request)
    {
        $validated = $request->validate([
            'id'                  => 'required|exists:histori_pemakaians,id',
            'nama_lengkap'        => 'required|string',
            'nik'                 => 'required|string',
            'posisi'              => 'required|string',
            'tanggal_masuk'       => 'required|date_format:Y-m-d',
            'cabang'              => 'required|string',
            'tanggal_penerimaan'  => 'required|date_format:Y-m-d',
        ]);

        $history = HistoriPemakaian::with(['aset.kategori', 'pegawai'])->findOrFail($validated['id']);
        $templatePath = storage_path('app/template_serah_terima.docx');
        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template dokumen serah terima tidak ditemukan.');
        }
        $template = new TemplateProcessor($templatePath);

        $template->setValue('nama', $validated['nama_lengkap']);
        $template->setValue('nik', $validated['nik']);
        $template->setValue('posisi', $validated['posisi']);
        $template->setValue('tanggal_masuk', Carbon::parse($validated['tanggal_masuk'])->format('d F Y'));
        $template->setValue('cabang', $validated['cabang']);
        $template->setValue('nama_aset', $history->aset->merk . ' ' . $history->aset->type);
        $template->setValue('kategori', $history->aset->kategori->nama);
        $template->setValue('kode_tag', $history->aset_kode_tag);
        $template->setValue('tanggal_terima', Carbon::parse($validated['tanggal_penerimaan'])->format('d F Y'));
        
        $fileName = 'Serah_Terima_' . str_replace(' ', '_', $validated['nama_lengkap']) . '.docx';
        $savePath = storage_path('app/public/' . $fileName);
        $template->saveAs($savePath);

        return response()->download($savePath)->deleteFileAfterSend(true);
    }
}