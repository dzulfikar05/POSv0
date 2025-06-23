@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <div class="row">
                    <div class="dropdown mr-2">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="importExportDropdownPenjualan" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="importExportDropdownPenjualan">
                            <a class="dropdown-item" id="exportExcelUrl" href="{{ url('/penjualan/export_excel') }}">
                                <i class="fa fa-file-excel"></i> Export to Excel
                            </a>
                            <a class="dropdown-item" id="exportPdfUrl" href="{{ url('/penjualan/export_pdf') }}" target="_blank">
                                <i class="fa fa-file-pdf"></i> Export to PDF
                            </a>
                        </div>
                    </div>

                    <button onclick="modalAction('{{ url('/penjualan/create_ajax') }}')" class="btn btn-primary mr-2">
                        Tambah Data
                    </button>
                </div>
            </div>

        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-2">
                    <select id="filter_tahun" class="form-control">
                        <option value="">Semua Tahun</option>
                        @for ($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                {{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filter_bulan" class="form-control">
                        <option value="">Semua Bulan</option>
                        @php
                            $bulanIndonesia = [
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
                            ];
                            $currentMonth = date('m');
                        @endphp
                        @foreach ($bulanIndonesia as $num => $nama)
                            <option value="{{ sprintf('%02d', $num) }}" {{ $num == $currentMonth ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered table-striped table-hover table-sm" id="table_penjualan">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Pesanan</th>
                        <th>Tanggal</th>
                        <th>Pembeli</th>
                        <th>Nomor Whatsapp Pembeli</th>
                        <th style="text-align: center">Total Harga</th>
                        <th>User Pembuat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog"
        data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true">
    </div>
    <div id="confirmModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" aria-hidden="true">
    </div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
         $(() => {
            updateExportLinks();
        })

        $('#filter_tahun, #filter_bulan').on('change', function() {
            tablePenjualan.ajax.reload();
            updateExportLinks();
        });

        function updateExportLinks() {
            const tahun = $('#filter_tahun').val();
            const bulan = $('#filter_bulan').val();

            let baseExcelUrl = "{{ url('/penjualan/export_excel') }}";
            let basePdfUrl = "{{ url('/penjualan/export_pdf') }}";

            let queryParams = [];
            if (tahun) queryParams.push(`tahun=${tahun}`);
            if (bulan) queryParams.push(`bulan=${bulan}`);

            let queryString = queryParams.length ? '?' + queryParams.join('&') : '';

            $('#exportExcelUrl').attr('href', baseExcelUrl + queryString);
            $('#exportPdfUrl').attr('href', basePdfUrl + queryString);
        }

        function modalAction(url = '') {
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
            });
        }

        function onComplete(id) {
            const html = `
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Konfirmasi Pesanan Selesai</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin pesanan telah selesai?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" onclick="onUpdateCompleted('${id}')" data-dismiss="modal" class="btn btn-primary">Ya, Validasi</button>
                    </div>
                </div>
            </div>`;

            $('#confirmModal').html(html).modal('show');
        }

        function onUpdateCompleted(id) {
            $.ajax({
                url: `{{ url('/penjualan/${id}/update_status') }}`,
                type: 'POST',
                data: {
                    id: id,
                    status: 'completed'
                },
                success: function(response) {
                    if (response.status) {
                        $('#confirmModal').html('');
                        $('#confirmModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        tablePenjualan.ajax.reload();
                    } else {
                        $('#confirmModal').html('');
                        $('#confirmModal').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                }
            })
        }

        var tablePenjualan;
        $(document).ready(function () {
            tablePenjualan = $('#table_penjualan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('penjualan/list') }}",
                    dataType: "json",
                    type: "POST",
                    data: function (d) {
                        d.tahun = $('#filter_tahun').val();
                        d.bulan = $('#filter_bulan').val();
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "penjualan_kode",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "penjualan_tanggal",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "customer_nama",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "customer_wa",
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: "total_harga",
                        className: "text-right",
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: "user_nama",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "status",
                        orderable: true,
                        searchable: true,
                        render: function(data) {
                            if (data == 'paid_off') {
                                return `<span style="font-size:12px" class="badge badge-warning">Lunas - Disiapkan</span>`;
                            } else if (data == 'completed') {
                                return `<span style="font-size:12px" class="badge badge-success">Selesai</span>`;
                            }
                        }
                    },
                    {
                        data: "aksi",
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush
