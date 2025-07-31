{{-- resources/views/aset/cetak-label.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Aset</title>
    <style>
        /* Gaya CSS untuk layout label Anda */
        body { font-family: sans-serif; margin: 0; padding: 20px; }
        .label-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px; /* Jarak antar label */
        }
        .label-item {
            width: 10cm; /* Ukuran label, sesuaikan */
            height: 5cm; /* Ukuran label, sesuaikan */
            border: 1px solid #ccc;
            padding: 10px;
            box-sizing: border-box;
            page-break-inside: avoid; /* Hindari pemisahan label di tengah halaman */
        }
        .label-item h4 { margin-bottom: 5px; font-size: 16px; }
        .label-item p { margin-bottom: 3px; font-size: 12px; }
        .label-item .qr-code { margin-top: 10px; text-align: center; }
        @media print {
            /* Sembunyikan elemen yang tidak perlu dicetak */
            body { margin: 0; padding: 0; }
            .label-container { gap: 0; } /* Hilangkan gap saat cetak jika tidak diperlukan */
        }
    </style>
</head>
<body>
    <div class="label-container">
        @foreach ($asetsToPrint as $aset)
            <div class="label-item">
                <h4>{{ $aset->merk }} {{ $aset->type }}</h4>
                <p>Kategori: {{ $aset->kategori->nama }}</p>
                <p>Kode Tag: <strong>{{ $aset->kode_tag }}</strong></p>
                <p>Serial Number: {{ $aset->serial_number ?? 'N/A' }}</p>
                {{-- Anda bisa menambahkan QR Code di sini menggunakan library QR code --}}
                {{-- Contoh ( simple-qrcode): --}}
                {{-- <div class="qr-code">
                    {!! QrCode::size(80)->generate(route('aset.show', $aset->kode_tag)) !!}
                </div> --}}
            </div>
        @endforeach
    </div>

    <script>
        // Otomatis mencetak setelah halaman dimuat
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>