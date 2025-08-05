<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cetak Label Aset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .label-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .label-item {
            width: 7cm;
            height: 3cm;
            border: 1px solid #000;
            padding: 6px;
            box-sizing: border-box;
            page-break-inside: avoid;
        }

        .label-title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .label-table {
            display: table;
            width: 100%;
            font-size: 12px;
        }

        .label-row {
            display: table-row;
        }

        .label-cell {
            display: table-cell;
            padding: 2px 4px;
            vertical-align: top;
        }

        .label-cell:first-child {
            width: 30%;
            white-space: nowrap;
            font-weight: bold; /* Jadikan label kiri tebal */
        }

        .label-cell:nth-child(2) {
            width: 5px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .label-container {
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="label-container">
        @foreach ($asetsToPrint as $aset)
            <div class="label-item">
                <div class="label-title">ASET CSAD</div>
                <div class="label-table">
                    <div class="label-row">
                        <div class="label-cell">Merk</div>
                        <div class="label-cell">:</div>
                        <div class="label-cell">{{ $aset->merk }}</div>
                    </div>
                    <div class="label-row">
                        <div class="label-cell">Kategori</div>
                        <div class="label-cell">:</div>
                        <div class="label-cell">{{ $aset->kategori->nama }}</div>
                    </div>
                    <div class="label-row">
                        <div class="label-cell">Kode Tag</div>
                        <div class="label-cell">:</div>
                        <div class="label-cell">{{ $aset->kode_tag }}</div>
                    </div>
                    <div class="label-row">
                        <div class="label-cell">Serial No</div>
                        <div class="label-cell">:</div>
                        <div class="label-cell">{{ $aset->serial_number ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>
