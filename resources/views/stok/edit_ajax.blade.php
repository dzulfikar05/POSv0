@empty($stok)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/stok') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/stok/' . $stok->stok_id . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf @method('PUT')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Stok</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="stok_tanggal" id="stok_tanggal" disabled
                            value="{{ $stok->stok_tanggal ? \Carbon\Carbon::parse($stok->stok_tanggal)->format('Y-m-d') : '' }}"
                            class="form-control" required>
                        <small id="error-stok_tanggal" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>item</label>
                        <input type="text" name="item" id="item" value="{{ $stok->item }}"
                            class="form-control" required>
                        <small id="error-item" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="stok_jumlah" id="stok_jumlah" value="{{ $stok->stok_jumlah }}"
                            class="form-control" required>
                        <small id="error-stok_jumlah" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Harga Total</label>
                        <input type="number" name="harga_total" id="harga_total" value="{{ $stok->harga_total }}"
                            class="form-control" required>
                        <small id="error-harga_total" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="form-control" required>
                            @foreach ($supplier as $supplier)
                                <option value="{{ $supplier->supplier_id }}"
                                    {{ $stok->supplier_id == $supplier->supplier_id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_nama }}
                                </option>
                            @endforeach
                        </select>
                        <small id="error-supplier_id" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" required>{{ $stok->keterangan }}</textarea>

                        <small id="error-keterangan" class="error-text form-text text-danger"></small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            $("#form-edit").validate({
                rules: {
                    barang_id: {
                        required: true
                    },
                    supplier_id: {
                        required: true
                    },
                    stok_tanggal: {
                        required: true,
                        date: true
                    },
                    stok_jumlah: {
                        required: true,
                        number: true,
                        min: 1
                    },
                    keterangan: {
                        required: false
                    }
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                tableStok.ajax.reload();
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endempty
