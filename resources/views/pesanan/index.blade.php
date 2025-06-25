@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary content-card">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <div class="row">
                    <button onclick="modalAction('{{ url('/pesanan/create_ajax') }}')" class="btn btn-primary mr-2">
                        Tambah Data
                    </button>
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

            <table class="table table-bordered table-striped table-hover table-sm" id="table_pesanan">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Pesanan</th>
                        <th>Tanggal</th>
                        <th>Pembeli</th>
                        <th>Nomor Whatsapp Pembeli</th>
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
        $(document).ready(function() {
            // Init select2
            $('.filter_tahun, .filter_bulan').select2({
                dropdownParent: $('.content-card')
            });

            // Reload table on filter change
            $('#filter_tahun, #filter_bulan').on('change', function() {
                tablePesanan.ajax.reload();
                setTimeout(() => {
                    tablePesanan.columns.adjust().draw();
                }, 200);
            });

            // Datatable init
            window.tablePesanan = $('#table_pesanan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('pesanan/list') }}",
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
                        data: "penjualan_kode",
                        name: "t_penjualan.penjualan_kode"
                    },
                    {
                        data: "penjualan_tanggal",
                        name: "t_penjualan.penjualan_tanggal"
                    },
                    {
                        data: "customer_nama",
                        name: "customers.nama"
                    },
                    {
                        data: "customer_wa",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "total_harga",
                        name: "total_harga", // diperlukan agar kolom custom bisa diurutkan
                        className: "text-right",
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: "user_nama",
                        name: "m_user.nama"
                    },
                    {
                        data: "status",
                        name: "t_penjualan.status"
                    },
                    {
                        data: "aksi",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

        });

        // Open modal
        function modalAction(url) {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        // Confirm modal generic
        function showConfirmationModal(message, buttonLabel, onConfirm) {
            const html = `
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Konfirmasi</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body"><p>${message}</p></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" id="confirmButton" class="btn btn-primary">${buttonLabel}</button>
                    </div>
                </div>
            </div>
        `;

            $('#confirmModal').html(html).modal('show');

            $(document).off('click', '#confirmButton').on('click', '#confirmButton', function() {
                $('#confirmModal').modal('hide');
                $('#confirmModal').on('hidden.bs.modal', function() {
                    $('#confirmModal').html('');
                    onConfirm();
                });
            });
        }

        // Validate payment
        function onValidatePayment(id) {
            showConfirmationModal("Apakah Anda yakin customer telah melakukan pembayaran dan pembayaran valid?",
                "Ya, Validasi",
                function() {
                    updateStatus(id, 'paid_off');
                });
        }

        // Reject pesanan
        function onReject(id) {
            showConfirmationModal("Apakah Anda yakin akan membatalkan pesanan ini?", "Ya, Batalkan", function() {
                updateStatus(id, 'rejected');
            });
        }

        // Update status handler
        function updateStatus(id, status) {
            $.ajax({
                url: `/pesanan/${id}/update_status`,
                type: 'POST',
                data: {
                    id,
                    status
                },
                success: function(res) {
                    Swal.fire({
                        icon: res.status ? 'success' : 'error',
                        title: res.status ? 'Berhasil' : 'Terjadi Kesalahan',
                        text: res.message
                    });
                    if (res.status) tablePesanan.ajax.reload();
                    setTimeout(() => {
                        tablePesanan.columns.adjust().draw();
                    }, 200);
                    $('#myModal').modal('hide');

                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menghubungi server'
                    });
                }
            });
        }
    </script>
@endpush
