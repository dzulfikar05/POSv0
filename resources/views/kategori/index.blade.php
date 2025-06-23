@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <div class="row">
                    <div class="dropdown mr-2">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" id="importExportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Import / Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="importExportDropdown">
                            <button class="dropdown-item" onclick="modalAction('{{ url('/kategori/import') }}')">
                                Import Kategori
                            </button>
                            <a class="dropdown-item" href="{{ url('/kategori/export_excel') }}">
                                <i class="fa fa-file-excel"></i> Export to Excel
                            </a>
                            <a class="dropdown-item" href="{{ url('/kategori/export_pdf') }}" target="_blank">
                                <i class="fa fa-file-pdf"></i> Export to PDF
                            </a>
                        </div>
                    </div>

                    <button onclick="modalAction('{{ url('/kategori/create_ajax') }}')" class="btn btn-primary mr-2">Tambah Data</button>
                </div>
            </div>

        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered table-striped table-hover table-sm" id="table_kategori">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data- backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true">
    </div>
@endsection
@push('css')
@endpush
@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }
        var tableKategori;
        $(document).ready(function() {
            tableKategori = $('#table_kategori').DataTable({
                processing: true,
                serverSide: true, // Jika ingin menggunakan server-side processing
                ajax: {
                    "url": "{{ url('kategori/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d.level_id = $('#level_id').val();
                    }
                },
                columns: [{
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }, // Kolom nomor urut
                    {
                        data: "kategori_kode",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "kategori_nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "aksi",
                        className: "",
                        orderable: false,
                        searchable: false
                    } // Tombol aksi
                ]
            });
        });
    </script>
@endpush
