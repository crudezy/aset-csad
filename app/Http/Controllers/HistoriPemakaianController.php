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
    /**
     * Menampilkan form untuk membuat catatan serah terima baru.
     */

     public function index()
     {
        // MODIFIKASI: Tambahkan whereNull untuk hanya mengambil data yang belum dikembalikan.
        $histories = HistoriPemakaian::with(['aset.kategori', 'pegawai'])
                                    ->whereNull('tanggal_kembali') 
                                    ->latest('tanggal_serah')
                                    ->get();
    
        $statusTersedia = StatusAset::whereRaw('LOWER(nama) = ?', ['tersedia'])->first();
        $asetsTersedia = $statusTersedia ? Aset::where('status_id', $statusTersedia->id)->get() : collect();
        $pegawais = Pegawai::orderBy('nama')->get();

        return view('pemakaian.index', compact('histories', 'asetsTersedia', 'pegawais'));
    }

    public function create()
    {
        // 1. Cari ID untuk status "Tersedia"
        $statusTersedia = StatusAset::whereRaw('LOWER(nama) = ?', ['tersedia'])->first();

        // 2. Ambil HANYA aset yang statusnya "Tersedia"
        $asetsTersedia = Aset::where('status_id', $statusTersedia->id)->get();

        // 3. Ambil semua data pegawai
        $pegawais = Pegawai::orderBy('nama')->get();

        // 4. Kirim data ke view
        return view('pemakaian.create', compact('asetsTersedia', 'pegawais'));
    }

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
            // Pastikan status "Digunakan" ada di database Anda
            $statusDigunakan = StatusAset::whereRaw('LOWER(nama) = ?', ['digunakan'])->first();
            if ($statusDigunakan) {
                $aset->status_id = $statusDigunakan->id;
                $aset->save();
            }
        }

        // 4. Redirect ke halaman daftar aset dengan pesan sukses
        return redirect()->route('aset.index')->with('success', 'Aset berhasil diserahkan.');
    }

    /**
     * Memproses pengembalian aset.
     */
    public function kembalikan(Aset $aset)
    {
        // 1. Cari histori pemakaian yang sedang aktif (tanggal kembali masih kosong)
        $historiAktif = $aset->historiPemakaians()->whereNull('tanggal_kembali')->first();

        if ($historiAktif) {
            // 2. Update tanggal kembali menjadi hari ini
            $historiAktif->update(['tanggal_kembali' => now()]);

            // 3. Ubah status aset kembali menjadi "Tersedia"
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
        // 1. Validasi: Pastikan ada data yang dipilih
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:histori_pemakaians,id', // Pastikan setiap id ada di tabel
        ]);

        $historiIds = $request->input('ids');
        $tanggalKembali = now();

        // Ambil kode_tag dari histori yang dipilih
        $asetKodeTags = HistoriPemakaian::whereIn('id', $historiIds)->pluck('aset_kode_tag');

        // Ambil ID untuk status "Tersedia"
        $statusTersedia = StatusAset::whereRaw('LOWER(nama) = ?', ['tersedia'])->first();
        if (!$statusTersedia) {
            return back()->with('error', 'Status "Tersedia" tidak ditemukan.');
        }

        // Gunakan transaction untuk memastikan semua query berhasil
        DB::beginTransaction();
        try {
            // 2. Update tanggal_kembali di tabel histori
            HistoriPemakaian::whereIn('id', $historiIds)->update(['tanggal_kembali' => $tanggalKembali]);

            // 3. Update status aset menjadi "Tersedia"
            Aset::whereIn('kode_tag', $asetKodeTags)->update(['status_id' => $statusTersedia->id]);

            DB::commit(); // Jika semua berhasil, simpan perubahan
        } catch (\Exception $e) {
            DB::rollBack(); // Jika ada error, batalkan semua perubahan
            return back()->with('error', 'Terjadi kesalahan saat mengembalikan aset.');
        }

        return redirect()->route('pemakaian.index')->with('success', count($historiIds) . ' aset berhasil dikembalikan.');
    }
    // public function downloadSerahTerima($id)
    // {
    //     $history = HistoriPemakaian::with(['aset', 'pegawai'])->findOrFail($id);
    
    //     $template = new TemplateProcessor(storage_path('app/template_serah_terima.docx'));
    
    //     // GUNAKAN OBJEK, BUKAN ARRAY
    //     $template->setValue('nama', $history->pegawai->nama);
    //     $template->setValue('nik', $history->pegawai->nik);
    //     $template->setValue('posisi', $history->pegawai->jabatan ?? '-');
    //     $template->setValue('tanggal_masuk', $history->pegawai->tanggal_masuk ?? '-');
    //     $template->setValue('cabang', $history->pegawai->cabang ?? '-');
    
    //     $template->setValue('nama_aset', $history->aset->merk . ' ' . $history->aset->type);
    //     $template->setValue('tipe_aset', $history->aset->type);
    //     $template->setValue('no_inventaris', $history->aset_kode_tag);
    //     $template->setValue('tanggal_terima', \Carbon\Carbon::parse($history->tanggal_serah)->format('d-m-Y'));
    //     $template->setValue('tanggal_dibuat', now()->format('d-m-Y'));
    
    //     $fileName = 'Serah_Terima_' . str_replace(' ', '_', $history->pegawai->nama) . '.docx';
    //     $savePath = storage_path('app/public/' . $fileName);
    //     $template->saveAs($savePath);
    
    //     return response()->download($savePath)->deleteFileAfterSend(true);
    // }
    
    public function cetakManual(Request $request)
    {
        // 1. Validasi semua input dari form modal
        $validated = $request->validate([
            'id'                => 'required|exists:histori_pemakaians,id',
            'nama_lengkap'      => 'required|string',
            'nik'               => 'required|string',
            'posisi'            => 'required|string',
            'tanggal_masuk'     => 'required|date_format:Y-m-d',
            'cabang'            => 'required|string',
            'tanggal_penerimaan'=> 'required|date_format:Y-m-d',
        ]);

        // 2. Ambil data lengkap dari database
        $history = HistoriPemakaian::with(['aset.kategori', 'pegawai'])->findOrFail($validated['id']);

        // 3. Siapkan template
        $templatePath = storage_path('app/template_serah_terima.docx');
        if (!file_exists($templatePath)) {
            return back()->with('error', 'Template dokumen serah terima tidak ditemukan.');
        }
        $template = new TemplateProcessor($templatePath);

        // 4. Isi template dengan data yang sudah divalidasi dan dari database
        $template->setValue('nama', $validated['nama_lengkap']);
        $template->setValue('nik', $validated['nik']);
        $template->setValue('posisi', $validated['posisi']);
        $template->setValue('tanggal_masuk', Carbon::parse($validated['tanggal_masuk'])->format('d F Y'));
        $template->setValue('cabang', $validated['cabang']);
        
        $template->setValue('nama_aset', $history->aset->merk . ' ' . $history->aset->type);
        $template->setValue('kategori', $history->aset->kategori->nama); // Perbaikan: ambil nama kategori
        $template->setValue('kode_tag', $history->aset_kode_tag);
        $template->setValue('tanggal_terima', Carbon::parse($validated['tanggal_penerimaan'])->format('d F Y'));
        
        // 5. Simpan dan unduh file
        $fileName = 'Serah_Terima_' . str_replace(' ', '_', $validated['nama_lengkap']) . '.docx';
        $savePath = storage_path('app/public/' . $fileName);
        $template->saveAs($savePath);

        return response()->download($savePath)->deleteFileAfterSend(true);
    }

    
}
