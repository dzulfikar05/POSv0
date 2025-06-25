@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary content-card">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <div class="row">
                    <div class="dropdown mr-2">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                            Export
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item d-flex align-items-center" id="exportExcelUrl"
                                href="{{ url('/stok/export_excel') }}">
                                <i class="fa fa-file-excel mr-2 text-success"></i> Export to Excel
                            </a>
                            <a class="dropdown-item d-flex align-items-center" id="exportPdfUrl"
                                href="{{ url('/stok/export_pdf') }}" target="_blank">
                                <i class="fa fa-file-pdf mr-2 text-danger"></i> Export to PDF
                            </a>
                        </div>
                    </div>
                    <button onclick="modalAction('{{ url('/stok/create_ajax') }}')" class="btn btn-primary mr-2">Tambah
                        Data</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-2">
                    <select id="filter_tahun" class="form-control filter_tahun">
                        <option value="">Semua Tahun</option>
                        @for ($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                {{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filter_bulan" class="form-control filter_bulan">
                        <option value="">Semua Bulan</option>
                        @foreach ([
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ] as $num => $nama)
                            <option value="{{ sprintf('%02d', $num) }}" {{ $num == date('n') ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <div id="total_harga"
                    class="p-3 col-md-3 bg-white border border-primary rounded shadow-sm text-primary font-weight-bold h5">
                    Total Harga: Rp 0
                </div>
            </div>

            <table class="table table-bordered table-striped table-hover table-sm" id="table_stok">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>User</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header d-flex justify-content-between">
                    <h5>Rekap Pengeluaran per Bulan (Tahun <span id="rekap_tahun_text">{{ date('Y') }}</span>)</h5>
                    <select id="filter_tahun_rekap" class="form-control form-control-sm w-25 ml-5 filter_tahun_rekap">
                        @for ($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                {{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm" id="table_pengeluaran_bulanan">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Bulan</th>
                                <th>Total Harga</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h5 class="mb-0">Grafik Pengeluaran Bulanan</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartPengeluaranBulanan" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false"></div>

        <style>
            #select2-filter_tahun_rekap-container{
                font-size: 18px !important;
            }
        </style>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let tableStok;
        let chartPengeluaranInstance;

        $(function() {
            $('.filter_tahun, .filter_bulan, .filter_tahun_rekap').select2({
                dropdownParent: $('.content-card')
            });

            tableStok = $('#table_stok').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/stok/list') }}",
                    type: "POST",
                    data: function(d) {
                        d.tahun = $('#filter_tahun').val();
                        d.bulan = $('#filter_bulan').val();
                    }
                },
                columns: [{
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "item"
                    },
                    {
                        data: "stok_jumlah",
                        className: "text-center"
                    },
                    {
                        data: "harga_total",
                        className: "text-right",
                        render: data => formatRupiah(data)
                    },
                    {
                        data: "keterangan"
                    },
                    {
                        data: "stok_tanggal",
                        className: "text-center",
                        render: formatDate
                    },
                    {
                        data: "supplier_nama"
                    },
                    {
                        data: "user_nama"
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                ],
                footerCallback: function(row, data) {
                    let total = this.api().column(3).data().reduce((a, b) => a + b, 0);
                    $('#total_harga').text('Total Harga: Rp ' + new Intl.NumberFormat('id-ID').format(
                        total));
                }
            });

            $('#filter_tahun, #filter_bulan').on('change', function() {
                tableStok.ajax.reload();
                updateExportLinks();
            });

            $('#filter_tahun_rekap').on('change', function() {
                fetchRekapBulanan($(this).val());
            });

            fetchRekapBulanan($('#filter_tahun_rekap').val());
            updateExportLinks();
        });

        function updateExportLinks() {
            const tahun = $('#filter_tahun').val();
            const bulan = $('#filter_bulan').val();
            let qs = [];

            if (tahun) qs.push(`tahun=${tahun}`);
            if (bulan) qs.push(`bulan=${bulan}`);

            const q = qs.length ? `?${qs.join('&')}` : '';
            $('#exportExcelUrl').attr('href', `{{ url('/stok/export_excel') }}` + q);
            $('#exportPdfUrl').attr('href', `{{ url('/stok/export_pdf') }}` + q);
        }

        function formatDate(dateStr) {
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID') + ' ' + d.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        function fetchRekapBulanan(tahun) {
            $.get("{{ url('/stok/rekap_per_bulan') }}", {
                tahun
            }, function(res) {
                let tbody = '',
                    grandTotal = 0,
                    labels = [],
                    dataChart = [];

                res.forEach((row, i) => {
                    const total = parseFloat(row.total_harga);
                    grandTotal += total;
                    tbody += `
                    <tr>
                        <td class="text-center">${i + 1}</td>
                        <td>${row.bulan_nama}</td>
                        <td>Rp ${formatRupiah(total)}</td>
                    </tr>
                `;
                    labels.push(row.bulan_nama);
                    dataChart.push(total);
                });

                tbody += `
                <tr class="font-weight-bold bg-light">
                    <td colspan="2" class="text-center">Grand Total</td>
                    <td>Rp ${formatRupiah(grandTotal)}</td>
                </tr>
            `;

                $('#table_pengeluaran_bulanan tbody').html(tbody);
                $('#rekap_tahun_text').text(tahun);
                renderChart(labels, dataChart);
            });
        }

        function renderChart(labels, data) {
            if (chartPengeluaranInstance) chartPengeluaranInstance.destroy();

            const ctx = document.getElementById('chartPengeluaranBulanan').getContext('2d');
            chartPengeluaranInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Total Pengeluaran',
                        data,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: tooltip => "Rp " + formatRupiah(tooltip.raw)
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
