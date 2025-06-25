@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary content-card">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <div class="row">
                    <div class="dropleft mx-2">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button"
                            id="importExportDropdownPenjualan" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="fa fa-download mr-1"></i> Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="importExportDropdownPenjualan">
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('/penjualan/export_excel') }}"
                                id="exportExcelUrl">
                                <i class="fa fa-file-excel text-success mr-2"></i> Export to Excel
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="{{ url('/penjualan/export_pdf') }}"
                                id="exportPdfUrl" target="_blank">
                                <i class="fa fa-file-pdf text-danger mr-2"></i> Export to PDF
                            </a>
                        </div>
                    </div>
                    {{-- Tombol Tambah Data (aktifkan jika dibutuhkan) --}}
                    {{-- <button onclick="modalAction('{{ url('/penjualan/create_ajax') }}')" class="btn btn-primary mr-2">Tambah Data</button> --}}
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
                            $currentMonth = date('n');
                        @endphp
                        @foreach ($bulanIndonesia as $num => $nama)
                            <option value="{{ sprintf('%02d', $num) }}" {{ $num == $currentMonth ? 'selected' : '' }}>
                                {{ $nama }}</option>
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
                        <th>Nomor Whatsapp</th>
                        <th class="text-center">Total Harga</th>
                        <th>User Pembuat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false"></div>
    <div id="confirmModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false"></div>
@endsection

@push('js')
    <script>
        let tablePenjualan;

        $(document).ready(function() {
            $('.filter_tahun, .filter_bulan').select2({
                dropdownParent: $('.content-card')
            });

            // Datatable
            tablePenjualan = $('#table_penjualan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('penjualan/list') }}",
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
                        data: "penjualan_kode"
                    },
                    {
                        data: "penjualan_tanggal"
                    },
                    {
                        data: "customer_nama"
                    },
                    {
                        data: "customer_wa"
                    },
                    {
                        data: "total_harga",
                        className: "text-right"
                    },
                    {
                        data: "user_nama"
                    },
                    {
                        data: "status",
                        render: function(data) {
                            if (data === 'paid_off')
                                return '<span class="badge badge-primary" style="font-size:12px">Lunas - Disiapkan</span>';
                            if (data === 'completed')
                                return '<span class="badge badge-success" style="font-size:12px">Selesai</span>';
                            return data;
                        }
                    },
                    {
                        data: "aksi",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#filter_tahun, #filter_bulan').on('change', function() {
                tablePenjualan.ajax.reload();
                setTimeout(() => {
                    tablePenjualan.columns.adjust().draw();
                }, 200);
                updateExportLinks();
            });

            updateExportLinks(); // awal
        });

        function updateExportLinks() {
            const tahun = $('#filter_tahun').val();
            const bulan = $('#filter_bulan').val();
            const params = [];

            if (tahun) params.push(`tahun=${tahun}`);
            if (bulan) params.push(`bulan=${bulan}`);

            const query = params.length ? `?${params.join('&')}` : '';
            $('#exportExcelUrl').attr('href', `{{ url('/penjualan/export_excel') }}${query}`);
            $('#exportPdfUrl').attr('href', `{{ url('/penjualan/export_pdf') }}${query}`);
        }

        function modalAction(url) {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        function onComplete(id) {
            const html = `
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Konfirmasi Pesanan Selesai</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body"><p>Apakah Anda yakin pesanan telah selesai?</p></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" id="btn-confirm-completed" class="btn btn-primary">Ya, Validasi</button>
                    </div>
                </div>
            </div>`;

            $('#confirmModal').html(html).modal('show');

            $(document).off('click', '#btn-confirm-completed').on('click', '#btn-confirm-completed', function() {
                $('#confirmModal').modal('hide');
                $('#confirmModal').one('hidden.bs.modal', function() {
                    $('#confirmModal').html('');
                    onUpdateCompleted(id);
                });
            });
        }

        function onUpdateCompleted(id) {
            $.ajax({
                url: `/penjualan/${id}/update_status`,
                type: 'POST',
                data: {
                    id: id,
                    status: 'completed'
                },
                success: function(response) {
                    Swal.fire({
                        icon: response.status ? 'success' : 'error',
                        title: response.status ? 'Berhasil' : 'Terjadi Kesalahan',
                        text: response.message
                    });
                    if (response.status) tablePenjualan.ajax.reload();
                    setTimeout(() => {
                        tablePenjualan.columns.adjust().draw();
                    }, 200);
                    $('#myModal').modal('hide');

                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal menghubungi server.'
                    });
                }
            });
        }
    </script>
@endpush
