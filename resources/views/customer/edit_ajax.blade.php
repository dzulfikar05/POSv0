@empty($user)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria- label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5> Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/customer') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/customer/' . $user->user_id . '/update_ajax') }}" method="POST" id="form-edit"   enctype="multipart/form-data">
        @csrf @method('PUT')
        <div id="modal-master" class="modal-dialog modal-lg" role="document" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>Username</label>
                        <input value="{{ $user->username }}" type="text" name="username" id="username"
                            class="form-control" required>
                        <small id="error-username" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input value="{{ $user->nama }}" type="text" name="nama" id="nama" class="form-control"
                            required>
                        <small id="error-nama" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label style="width: 100%">Jenis Kelamin</label>
                        <select name="jk" id="jk" class="form-control select-jk" required style="width: 100%">

                            <option value="male" {{ $user->jk == 'male' ? 'selected' : '' }}>Laki-laki
                            </option>
                            <option value="female" {{ $user->jk == 'female' ? 'selected' : '' }}>Perempuan
                            </option>
                        </select>
                        <small id="error-jk" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control" cols="30" rows="3">{{ $user->alamat  }}</textarea>
                        <small id="error-alamat" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Nomor Whatsapp</label>
                        <input value="{{ $user->wa }}" type="text" name="wa" id="wa" class="form-control" placeholder="ex: 628123456789"
                            required>
                        <small class="form-text text-muted">ex: 628123456789</small>
                        <small id="error-wa" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input value="" type="password" name="password" id="password" class="form-control">
                        <small class="form-text text-muted">Abaikan jika tidak ingin ubah
                            password</small>
                        <small id="error-password" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Foto Profil (opsional)</label>
                        <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti foto</small>
                        <small id="error-photo" class="error-text form-text text-danger"></small>
                    </div>
                    @if ($user->photo)
                        <div class="form-group">
                            <label>Foto Saat Ini:</label><br>
                            <img src="{{ asset('/storage/uploads/photo/' . $user->photo) }}" width="100" class="img-thumbnail">
                        </div>
                    @endif

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
            $('.select-jk').select2({
                dropdownParent: $('#form-edit')
            });

            $.validator.addMethod('filesize', function(value, element, param) {
                if (element.files.length == 0) return true;
                return this.optional(element) || (element.files[0].size <= param);
            }, 'Ukuran file maksimal 2 MB');

            $("#form-edit").validate({
                rules: {
                    username: {
                        required: true,
                        minlength: 3,
                        maxlength: 20
                    },
                    nama: {
                        required: true,
                        minlength: 3,
                        maxlength: 100
                    },
                    jk: {
                        required: true,
                    },
                    alamat: {
                        required: true,
                    },
                    wa: {
                        required: true,
                        pattern: /^(628)[0-9]{7,12}$/
                    },
                    password: {
                        minlength: 6,
                        maxlength: 20
                    },
                    photo: {
                        required: false, // optional, bisa true kalau wajib
                        extension: "jpg|jpeg|png",
                        filesize: 2048000 // maksimal 2MB
                    }
                },
                submitHandler: function(form) {
                    let formData = new FormData(form);
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                tableCustomer.ajax.reload();
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
@endempty
