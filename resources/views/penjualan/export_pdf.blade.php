<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 10px 30px;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        td,
        th {
            padding: 6px 5px;
            font-size: 11pt;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid black;
        }

        .section {
            margin-bottom: 30px;
        }

        .title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }

        .hr-divider {
            border: none;
            border-top: 2px dashed #999;
            margin: 20px 0;
        }
    </style>
</head>

<body>


    <h3 class="text-center mt-1">LAPORAN DATA PENJUALAN</h3>
    <p class="text-center font-11">Periode: {{ $bulanNama }} {{ $tahunNama }}</p>

    @foreach ($penjualan as $p)
        <div class="section">
            <table class="border-all">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="20%">Kode Penjualan</th>
                        <th width="15%">Tanggal</th>
                        <th width="25%">Nama Pembeli</th>
                        <th width="20%" class="text-right">Total Harga</th>
                        <th width="15%">User Input</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $p->penjualan_kode }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->penjualan_tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $p->customer->nama ?? '-' }}</td>
                        <td class="text-right">{{ number_format($p->total_harga ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $p->user->nama ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>

            @if ($p->detail->count())
                <table class="border-all">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">#</th>
                            <th>Nama Barang</th>
                            <th class="text-right" width="20%">Harga Satuan</th>
                            <th class="text-right" width="10%">Jumlah</th>
                            <th class="text-right" width="20%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($p->detail as $i => $d)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $d->barang->barang_nama ?? '-' }}</td>
                                <td class="text-right">{{ number_format($d->harga, 0, ',', '.') }}</td>
                                <td class="text-right">{{ $d->jumlah }}</td>
                                <td class="text-right">{{ number_format($d->harga * $d->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <hr class="hr-divider" />
        </div>
    @endforeach

</body>

</html>
