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

        .p-1 {
            padding: 5px 1px 5px 1px;
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
    </style>
</head>

<body>
    <h3 class="text-center">LAPORAN DATA KATEGORI</h4>
        <table class="border-all">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Kode Supplier</th>
                    <th>Nama Supplier</th>
                    <th>WA Supplier</th>
                    <th>Alamat Supplier</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($supplier as $b)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $b->supplier_kode }}</td>
                        <td>{{ $b->supplier_nama }}</td>
                        <td>{{ $b->supplier_wa }}</td>
                        <td>{{ $b->supplier_alamat }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
</body>

</html>
