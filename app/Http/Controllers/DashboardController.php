<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\HistoriPemakaian;
use App\Models\Kategori;
use App\Models\RiwayatService;
use App\Models\StatusAset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Filter total aset berdasarkan departemen user
        $totalAset = Aset::where('department_id', $user->department_id)->count();

        $statusDigunakanId = StatusAset::whereRaw('LOWER(nama) = ?', ['digunakan'])->value('id');
        $asetDigunakan = $statusDigunakanId 
            ? Aset::where('status_id', $statusDigunakanId)
                ->where('department_id', $user->department_id)
                ->count() 
            : 0;
        
        $statusRusakId = StatusAset::whereRaw('LOWER(nama) = ?', ['rusak'])->value('id');
        $asetRusak = $statusRusakId 
            ? Aset::where('status_id', $statusRusakId)
                ->where('department_id', $user->department_id)
                ->count() 
            : 0;
        
        $asetDalamPerbaikan = RiwayatService::whereNull('tanggal_selesai_service')
            ->whereHas('aset', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })
            ->count();

        $statusData = StatusAset::withCount(['asets' => function ($query) use ($user) {
            $query->where('department_id', $user->department_id);
        }])->get();
        $statusLabels = $statusData->pluck('nama');
        $statusCounts = $statusData->pluck('asets_count');

        $kategoriData = Kategori::withCount(['asets' => function ($query) use ($user) {
            $query->where('department_id', $user->department_id);
        }])->get();
        $kategoriLabels = $kategoriData->pluck('nama');
        $kategoriCounts = $kategoriData->pluck('asets_count');

        $servisTerbaru = RiwayatService::whereHas('aset', function ($query) use ($user) {
            $query->where('department_id', $user->department_id);
        })->with('aset')->latest('tanggal_masuk_service')->take(5)->get();

        $pemakaianTerbaru = HistoriPemakaian::whereHas('aset', function ($query) use ($user) {
            $query->where('department_id', $user->department_id);
        })->with(['aset', 'pegawai'])->latest('tanggal_serah')->take(5)->get();

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
