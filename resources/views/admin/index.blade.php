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
                            <button class="dropdown-item" onclick="modalAction('{{ url('/admin/import') }}')">
                                Import Admin
                            </button>
                            <a class="dropdown-item" href="{{ url('/admin/export_excel') }}">
                                <i class="fa fa-file-excel"></i> Export to Excel
                            </a>
                            <a class="dropdown-item" href="{{ url('/admin/export_pdf') }}" target="_blank">
                                <i class="fa fa-file-pdf"></i> Export to PDF
                            </a>
                        </div>
                    </div>

                    <button onclick="modalAction('{{ url('/admin/create_ajax') }}')" class="btn btn-primary mr-2">Tambah Data</button>
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
            {{-- <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Filter :</label>
                        <div class="col-3">
                            <select class="form-control" id="level_id" name="level_id" required>
                                <option value="">- Semua -</option>
                                @foreach ($level as $item)
                                    <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Level Pengguna</small>
                        </div>
                    </div>
                </div>
            </div> --}}
            <table class="table table-bordered table-striped table-hover table-sm" id="table_user">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Username</th>
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

        var tableAdmin;
        $(document).ready(function() {
            tableAdmin = $('#table_user').DataTable({
                processing: true,
                serverSide: true, // Jika ingin menggunakan server-side processing
                ajax: {
                    "url": "{{ url('admin/list') }}",
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
                        data: "photo",
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            if (data) {
                                return `<img src="/storage/uploads/photo/${data}" width="75" height="75" style="object-fit: cover" class="rounded-circle"/>`;

                            }else{
                                return `<img src="userNoImage.webp" width="75" height="75" style="object-fit: cover" class="rounded-circle"/>`;
                            }
                        }
                    },

                    {
                        data: "username",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    // {
                    //     data: "level.level_nama",
                    //     className: "",
                    //     orderable: false,
                    //     searchable: false
                    // }, // Relasi level
                    {
                        data: "aksi",
                        className: "",
                        orderable: false,
                        searchable: false
                    } // Tombol aksi
                ]
            });
            $('#level_id').on('change', function() {
                tableAdmin.ajax.reload();
            });
        });
    </script>
@endpush
