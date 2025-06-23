    <form action="{{ url('/produk/ajax') }}" method="POST" id="form-tambah" enctype="multipart/form-data">
        @csrf
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Data Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group ">
                        <label>Kategori</label>
                        <select class="form-control" id="kategori_id" name="kategori_id" required>
                            <option value="">- Pilih Kategori -</option>
                            @foreach ($kategori as $item)
                                <option value="{{ $item->kategori_id }}">{{ $item->kategori_nama }}</option>
                            @endforeach
                        </select>
                        <small id="error-kategori_id" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Produk Kode</label>
                        <input value="" type="text" name="barang_kode" id="barang_kode"
                            class="form-control" required>
                        <small id="error-barang_kode" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Produk Nama</label>
                        <input value="" type="text" name="barang_nama" id="barang_nama"
                            class="form-control" required>
                        <small id="error-barang_nama" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Harga</label>
                        <input value="" type="number" name="harga" id="harga" class="form-control"
                            required>
                        <small id="error-harga" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Gambar</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        <small id="error-image" class="error-text form-text text-danger"></small>
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
            $.validator.addMethod('filesize', function(value, element, param) {
                if (element.files.length == 0) return true;
                return this.optional(element) || (element.files[0].size <= param);
            }, 'Ukuran file maksimal 2 MB');

            $("#form-tambah").validate({
                rules: {
                    kategori_id: {
                        required: true,
                        number: true
                    },
                    barang_kode: {
                        required: true,
                        minlength: 3,
                        maxlength: 20
                    },
                    barang_nama: {
                        required: true,
                        minlength: 0,
                        maxlength: 100
                    },
                    harga: {
                        required: true,
                        number: true
                    },
                    image: {
                        required: false,
                        extension: "jpg|jpeg|png",
                        filesize: 2048000
                    }
                },
                submitHandler: function(form) {
                    let formData = new FormData(form);

                    $.ajax({
                        url: form.action,
                        type: form.method,
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                tableProduk.ajax.reload();
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
