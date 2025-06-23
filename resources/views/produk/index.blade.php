@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar barang</h3>
            <div class="card-tools">
                <div class="row">
                    <div class="dropdown mr-2">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="importExportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Import / Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="importExportDropdown">
                            <button class="dropdown-item" onclick="modalAction('{{ url('/produk/import') }}')">
                                Import Produk
                            </button>
                            <a class="dropdown-item" href="{{ url('/produk/export_excel') }}">
                                <i class="fa fa-file-excel"></i> Export to Excel
                            </a>
                            <a class="dropdown-item" href="{{ url('/produk/export_pdf') }}" target="_blank">
                                <i class="fa fa-file-pdf"></i> Export to PDF
                            </a>
                        </div>
                    </div>

                    <button onclick="modalAction('{{ url('/produk/create_ajax') }}')" class="btn btn-primary mr-2">Tambah Data</button>
                </div>
            </div>

        </div>
        <div class="card-body">
            <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm row text-sm mb-0">
                            <label for="filter_date" class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-3">
                                <select name="filter_kategori" class="form-control form-control-sm filter_kategori">
                                    <option value="">- Semua -</option>
                                    @foreach ($kategori as $l)
                                        <option value="{{ $l->kategori_id }}">{{ $l->kategori_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Kategori Produk</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered table-sm table-striped table-hover" id="table-barang">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data- backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true">
    </div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        var tableProduk;
        $(document).ready(function() {
            tableProduk = $('#table-barang').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ url('produk/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d.filter_kategori = $('.filter_kategori').val();
                    }
                },
                columns: [{
                        data: "DT_RowIndex",
                        className: "text-center",
                        width: "5%",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "image",
                        className: "",
                        width: "10%",
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            if (data) {
                                return `<img src="/storage/uploads/product/${data}" width="75" height="75" style="object-fit: cover"/>`;
                            } else {
                                return `<img src="No_Image_Available.jpg" width="75" height="75" style="object-fit: cover"/>`;
                            }
                        }
                    },
                    {
                        data: "barang_kode",
                        className: "",
                        width: "10%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "barang_nama",
                        className: "",
                        width: "37%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "harga",
                        className: "",
                        width: "10%",
                        orderable: true,
                        searchable: false,
                        render: function(data, type, row) {
                            return new Intl.NumberFormat('id-ID').format(data);
                        }
                    },

                    {
                        data: "kategori.kategori_nama",
                        className: "",
                        width: "14%",
                        orderable: true,
                        searchable: false
                    },

                    {
                        data: "aksi",
                        className: "text-center",
                        width: "7%",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#table-barang_filter input').unbind().bind().on('keyup', function(e) {
                if (e.keyCode == 13) { // enter key
                    tableProduk.search(this.value).draw();
                }
            });

            $('.filter_kategori').change(function() {
                tableProduk.draw();
            });
        });
    </script>
@endpush
