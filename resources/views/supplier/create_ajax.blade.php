    <form action="{{ url('/supplier/ajax') }}" method="POST" id="form-tambah">
        @csrf
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Data Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Supplier Kode</label>
                        <input value="" type="text" name="supplier_kode" id="supplier_kode" class="form-control"
                            required>
                        <small id="error-supplier_kode" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Supplier Nama</label>
                        <input value="" type="text" name="supplier_nama" id="supplier_nama" class="form-control"
                            required>
                        <small id="error-supplier_nama" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Supplier WA</label>
                        <input value="" name="supplier_wa" id="supplier_wa" class="form-control" type="text" placeholder="ex: 628123456789"
                            required>
                        <small class="form-text text-muted">ex: 628123456789</small>
                        <small id="error-supplier_wa" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Supplier Alamat</label>
                        <textarea name="supplier_alamat" id="supplier_alamat" class="form-control" required></textarea>
                        <small id="error-supplier_alamat" class="error-text form-text text-danger"></small>
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
            $("#form-tambah").validate({
                rules: {
                    supplier_kode: {
                        required: true,
                        minlength: 3,
                        maxlength: 20
                    },
                    supplier_nama: {
                        required: true,
                        minlength: 0,
                        maxlength: 100
                    },
                    supplier_wa: {
                        required: false,
                        pattern: /^(628)[0-9]{7,12}$/
                    },
                    supplier_alamat: {
                        required: false,
                    },
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
                                tableSupplier.ajax.reload();
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
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
