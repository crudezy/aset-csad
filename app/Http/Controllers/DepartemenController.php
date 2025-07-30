<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;

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
        if ($departemen->pegawais()->count() > 0) {
            return back()->with('error', 'Departemen tidak dapat dihapus karena masih digunakan oleh pegawai.');
        }
        $departemen->delete();
        return redirect()->route('master-data.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
