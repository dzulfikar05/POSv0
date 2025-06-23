<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px 3px;
        }

        th {
            text-align: left;
        }

        .d-block {
            display: block;
        }

        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-10 {
            font-size: 10pt;
        }

        .font-11 {
            font-size: 11pt;
        }

        .font-12 {
            font-size: 12pt;
        }

        .font-13 {
            font-size: 13pt;
        }

        .border-bottom-header {
            border-bottom: 1px solid;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid;
        }

        .mb-1 {
            margin-bottom: 3px;
        }

        .mt-1 {
            margin-top: 10px;
        }

    </style>
</head>

<body>


    <h3 class="text-center mt-1">LAPORAN DATA PENJUALAN</h3>


    @foreach ($penjualan as $p)
    <div style="margin-bottom: 20px">
        <table class="border-all mb-1 font-11">
            <thead>
                <tr>
                    <th width="5%" class="text-center">ID</th>
                    <th width="20%">Kode Penjualan</th>
                    <th width="15%">Tanggal</th>
                    <th width="20%">Pembeli</th>
                    <th width="20%" class="text-right">Total Harga</th>
                    <th width="20%">User Pembuat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">{{ $p->penjualan_id }}</td>
                    <td>{{ $p->penjualan_kode }}</td>
                    <td>{{ $p->penjualan_tanggal }}</td>
                    <td>{{ $p->customer->nama }}</td>
                    <td class="text-right">{{ number_format($p->total_harga ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $p->user->nama ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        @if ($p->detail->count())
            <table class="border-all font-10 mb-1">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Nama Barang</th>
                        <th class="text-right" width="20%">Harga</th>
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
    </div>

    @endforeach

</body>

</html>
