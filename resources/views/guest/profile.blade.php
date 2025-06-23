<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel"
    aria-hidden="true">
    @if ($auth?->user_id)
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">Profil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="form-profile" action="{{ url('/public/' . $auth->user_id . '/update_profile') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Username</label>
                            <input value="{{ $auth->username }}" type="text" name="username" class="form-control"
                                required>
                            <small id="error-username" class="form-text text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input value="{{ $auth->nama }}" type="text" name="nama" class="form-control"
                                required>
                            <small id="error-nama" class="form-text text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jk" class="form-control" required>
                                <option value="male" {{ $auth->jk == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="female" {{ $auth->jk == 'female' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            <small id="error-jk" class="form-text text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ $auth->alamat }}</textarea>
                            <small id="error-alamat" class="form-text text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label>Nomor Whatsapp</label>
                            <input value="{{ $auth->wa }}" type="text" name="wa" class="form-control"
                                placeholder="628123456789" required>
                            <small class="form-text text-muted">ex: 628123456789</small>
                            <small id="error-wa" class="form-text text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control">
                            <small class="form-text text-muted">Abaikan jika tidak ingin mengubah password</small>
                            <small id="error-password" class="form-text text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label>Foto Profil (opsional)</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti foto</small>
                            <small id="error-photo" class="form-text text-danger"></small>
                        </div>
                        @if ($auth->photo)
                            <div class="form-group">
                                <label>Foto Saat Ini:</label><br>
                                <img src="{{ asset('storage/uploads/photo/' . $auth->photo) }}" width="100"
                                    class="img-thumbnail">
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Tutup</button>
                        <button type="button" onclick="updateProfile()" class="btn btn-primary">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    @endif
</div>

<script>
    onShowProfile = () => {
        $('#profileModal').modal('show');
    }
    onHideProfile = () => {
        $('#profileModal').modal('hide');
    }

    updateProfile = () => {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Perubahan tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ubah',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $('#form-profile').attr('action'),
                    method: 'POST',
                    data: new FormData($('#form-profile')[0]),
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        if (res.status) {
                            onHideProfile();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            $('.error-text').text('');
                            $.each(res.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: res.message
                            }).then(function() {
                                $.each(res.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal mengirim data. Silakan coba lagi.'
                        });
                    }
                });
            }
        });

    }
</script>
