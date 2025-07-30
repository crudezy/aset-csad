<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Lokasi;
use Illuminate\Http\Request;


class MasterDataController extends Controller
{
    public function index()
    {
        // Ubah dari paginate() menjadi get() untuk mengambil semua data
        $lokasis = Lokasi::latest()->get();
        $departemens = Departemen::latest()->get();

        return view('master-data.index', compact('lokasis', 'departemens'));
    }
}