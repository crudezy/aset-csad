<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::latest()->paginate(10);
        return view('vendor.list-vendor', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Tambahkan aturan 'unique' di sini
            'nama_vendor' => 'required|string|max:255|unique:vendors,nama_vendor',
            'kontak' => 'nullable|string|max:255',
        ]);
        Vendor::create($validated);
        return redirect()->route('vendor.index')->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255|unique:vendors,nama_vendor,' . $vendor->id,
            'kontak' => 'nullable|string|max:255',
        ]);
        $vendor->update($validated);
        return redirect()->route('vendor.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        if ($vendor->asets()->count() > 0) {
            return back()->with('error', 'Vendor tidak dapat dihapus karena masih terhubung dengan aset.');
        }
        $vendor->delete();
        return redirect()->route('vendor.index')->with('success', 'Vendor berhasil dihapus.');
    }
}
