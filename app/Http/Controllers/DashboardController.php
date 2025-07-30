<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\HistoriPemakaian;
use App\Models\Kategori;
use App\Models\RiwayatService;
use App\Models\StatusAset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data untuk Kartu KPI
        $totalAset = Aset::count();

        $statusDigunakanId = StatusAset::whereRaw('LOWER(nama) = ?', ['digunakan'])->value('id');
        $asetDigunakan = $statusDigunakanId ? Aset::where('status_id', $statusDigunakanId)->count() : 0;
        
        $statusRusakId = StatusAset::whereRaw('LOWER(nama) = ?', ['rusak'])->value('id');
        $asetRusak = $statusRusakId ? Aset::where('status_id', $statusRusakId)->count() : 0;
        
        $asetDalamPerbaikan = RiwayatService::whereNull('tanggal_selesai_service')->count();

        // 2. Data untuk Grafik Pie Status Aset
        $statusData = StatusAset::withCount('asets')->get();
        $statusLabels = $statusData->pluck('nama');
        $statusCounts = $statusData->pluck('asets_count');

        // 3. Data untuk Grafik Batang Aset per Kategori
        $kategoriData = Kategori::withCount('asets')->get();
        $kategoriLabels = $kategoriData->pluck('nama');
        $kategoriCounts = $kategoriData->pluck('asets_count');

        // 4. Data untuk Daftar Aktivitas Terbaru
        $servisTerbaru = RiwayatService::with('aset')->latest('tanggal_masuk_service')->take(5)->get();
        $pemakaianTerbaru = HistoriPemakaian::with(['aset', 'pegawai'])->latest('tanggal_serah')->take(5)->get();

        return view('dashboard.index', compact(
            'totalAset',
            'asetDigunakan',
            'asetRusak',
            'asetDalamPerbaikan',
            'statusLabels',
            'statusCounts',
            'kategoriLabels',
            'kategoriCounts',
            'servisTerbaru',
            'pemakaianTerbaru'
        ));
    }
}