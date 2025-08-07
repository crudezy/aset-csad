<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cetak Label Aset</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 10px; }
        .label-container { display: flex; flex-wrap: wrap; gap: 10px; }
        
        /* Gaya untuk link pembungkus */
        .label-link {
            text-decoration: none; /* Menghilangkan garis bawah pada link */
            color: inherit; /* Mewarisi warna teks dari parent */
            display: block;
        }

        .label-item {
            width: 5cm;
            height: 5cm;
            border: 1px solid #000;
            padding: 10px;
            box-sizing: border-box;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            transition: all 0.2s ease-in-out;
        }

        /* Efek hover untuk membuatnya terasa interaktif */
        .label-link:hover .label-item {
            cursor: pointer;
            border-color: #007bff; /* Warna biru saat cursor di atas */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .qr-code { margin-bottom: 10px; }
        
        /* Mengubah warna teks kode tag menjadi biru */
        .kode-tag { 
            font-weight: bold; 
            font-size: 14px; 
            color: #007bff; /* Warna biru untuk teks */
        }

        @media print {
            body { margin: 0; padding: 0; }
            .label-container { gap: 0; }
            /* Sembunyikan tampilan link saat mode cetak */
            .label-link { color: inherit; }
            .kode-tag { color: #000; }
            .label-link:hover .label-item { border-color: #000; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="label-container">
        @foreach ($asetsToPrint as $aset)
            {{-- KITA BUNGKUS SEMUA DENGAN TAG <a> --}}
            <a href="{{ route('aset.showPublic', $aset->kode_tag) }}" target="_blank" class="label-link">
                <div class="label-item">
                    <div class="qr-code">
                        {!! QrCode::size(140)->generate(route('aset.showPublic', $aset->kode_tag)) !!}
                    </div>
                    <div class="kode-tag">
                        {{ $aset->kode_tag }}
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <script>
        // Fungsi print tetap berjalan seperti biasa
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>