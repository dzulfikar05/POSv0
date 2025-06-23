<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Penjualan</title>
    <style>
        @page {
    margin: 0;
    size: auto; /* Biarkan DomPDF tentukan tinggi */
}
html, body {
    margin: 0;
    padding: 0;
    font-size: 10px;
    font-family: Arial, sans-serif;
    width: 100%;
}

        .text-center {
            text-align: center;
        }
        .section {
            padding: 2px 6px;
        }
        table {
            width: 100%;
        }
        .items th, .items td {
            text-align: left;
            padding: 2px 0;
        }
        .total {
            border-top: 1px dashed #000;
            margin-top: 5px;
            padding-top: 5px;
        }
    </style>

</head>
<body>
    <div class="text-center">
        <br><strong>STRUK PEMBELIAN</strong><br>
        ------------------------------------------------------------
    </div>

    <div class="section">
        <table>
            <tr>
                <td>No. Nota</td>
                <td>: {{ $penjualan->penjualan_kode }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: {{ $penjualan->user->nama ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="items">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Qty</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualan->detail as $item)
                    <tr>
                        <td>{{ $item->barang->barang_nama }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td class="text-right">Rp {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            <table>
                <tr>
                    <td><strong>Total</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="text-center">
        ------------------------------------------------------------<br>
        Terima Kasih!
    </div>
</body>
</html>
