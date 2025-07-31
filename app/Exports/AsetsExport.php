<?php

namespace App\Exports;

use App\Models\Aset; // Pastikan namespace model Anda benar
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AsetsExport implements FromCollection, WithHeadings, WithMapping
{
    private $rowNumber = 0;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Ambil semua data aset dengan relasinya untuk efisiensi query
        return Aset::with('kategori', 'pemegangTerakhir.pegawai.departemen', 'statusAset', 'vendor')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Fungsi ini akan membuat baris header sesuai gambar Anda
        return [
            'No',
            'Kode Tag',
            'Kategori',
            'Merk',
            'Type',
            'Serial Number',
            'Tahun Beli',
            'PIC',
            'Departemen',
            'Lokasi',
            'Spesifikasi',
            'Status',
            'Vendor',
            'Keterangan',
        ];
    }

    /**
     * @param mixed $aset
     *
     * @return array
     */
    public function map($aset): array
    {
        // Fungsi ini memetakan setiap baris data aset ke kolom yang sesuai
        $this->rowNumber++;
        
        $pic = '-';
        $departemen = '-';

        // Cek jika ada pemegang aset terakhir
        if ($aset->pemegangTerakhir && !$aset->pemegangTerakhir->tanggal_kembali) {
            $pic = optional($aset->pemegangTerakhir->pegawai)->nama ?? '-';
            $departemen = optional($aset->pemegangTerakhir->pegawai->departemen)->nama ?? '-';
        }

        return [
            $this->rowNumber,
            $aset->kode_tag,
            $aset->kategori->nama ?? '-',
            $aset->merk,
            $aset->type,
            $aset->serial_number ?? '-',
            date('Y', strtotime($aset->tanggal_pembelian)), // Mengambil tahun saja
            $pic,
            $departemen,
            $aset->lokasi ?? '-', // Asumsi 'lokasi' ada di tabel aset
            $aset->spesifikasi ?? '-',
            $aset->statusAset->nama ?? '-',
            $aset->vendor->nama ?? '-',
            $aset->keterangan ?? '-',
        ];
    }
}