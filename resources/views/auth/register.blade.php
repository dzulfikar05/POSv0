<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Pengguna</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .register-wrapper {
            display: flex;
            width: 90%;
            max-width: 900px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            overflow: hidden;
            background-color: #fff;
        }

        .register-image {
            flex: 1;
            background: url('https://images.pexels.com/photos/12935074/pexels-photo-12935074.jpeg?cs=srgb&dl=pexels-imin-technology-276315592-12935074.jpg&fm=jpg') no-repeat center center;
            background-size: cover;
        }

        .register-form {
            flex: 1;
            padding: 40px 30px;
        }

        .register-form h1 {
            font-weight: 600;
            margin-bottom: 20px;
            color: #007bff;
        }

        .form-control {
            border-radius: 8px;
            height: 45px;
        }

        .select2-container--default .select2-selection--single {
            height: 45px;
            border-radius: 8px;
            padding: 8px 12px;
            border: 1px solid #ced4da;
        }

        .btn-primary {
            border-radius: 8px;
            font-weight: 600;
        }

        .links {
            font-size: 14px;
            margin-top: 20px;
        }

        .links a {
            color: #007bff;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .error-text {
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .register-wrapper {
                flex-direction: column;
            }

            .register-image {
                height: 200px;
                width: 100%;
            }

            .register-form {
                padding: 25px;
            }
        }
    </style>
</head>

<body>
    <div class="register-wrapper">
        <div class="register-image d-none d-md-block"></div>
        <div class="register-form">
            <h1>{{ $app_name ?? 'Point of Sale' }}</h1>
            <p class="text-muted mb-4">Silakan isi form untuk mendaftar</p>

            <form action="{{ url('register') }}" method="POST" id="form-register">
                @csrf
                <div class="mb-3">
                    <input type="text" name="name" id="name" class="form-control" placeholder="Full name">
                    <small id="error-name" class="error-text text-danger"></small>
                </div>
                <div class="mb-3">
                    <input type="text" name="username" id="username" class="form-control" placeholder="Username">
                    <small id="error-username" class="error-text text-danger"></small>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                    <small id="error-password" class="error-text text-danger"></small>
                </div>
                <div class="mb-3">
                    <select name="jk" id="jk" class="form-control">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="male">Laki-laki</option>
                        <option value="female">Perempuan</option>
                    </select>
                    <small id="error-jk" class="error-text text-danger"></small>
                </div>
                <div class="mb-3">
                    <input type="text" name="wa" id="wa" class="form-control" placeholder="WhatsApp (ex: 628123456789)">
                    <small id="error-wa" class="error-text text-danger"></small>
                </div>
                <div class="mb-3">
                    <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Alamat"></textarea>
                    <small id="error-alamat" class="error-text text-danger"></small>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>

            <div class="links mt-3">
                <a href="{{ url('login') }}">Sudah punya akun? Masuk di sini</a>
            </div>
        </div>
    </div>

    <!-- JS Dependencies -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#jk').select2({
                placeholder: "-- Pilih Jenis Kelamin --",
                width: '100%',
                allowClear: true
            });

            $('#form-register').validate({
                rules: {
                    name: { required: true, minlength: 3, maxlength: 50 },
                    username: { required: true, minlength: 4, maxlength: 20 },
                    password: { required: true, minlength: 6, maxlength: 20 },
                    jk: { required: true },
                    wa: { required: true, pattern: /^(62)[1-9][0-9]{7,11}$/ },
                    alamat: { required: true }
                },
                messages: {
                    name: { required: "Nama lengkap wajib diisi", minlength: "Minimal 3 karakter", maxlength: "Maksimal 50 karakter" },
                    username: { required: "Username wajib diisi", minlength: "Minimal 4 karakter", maxlength: "Maksimal 20 karakter" },
                    password: { required: "Password wajib diisi", minlength: "Minimal 6 karakter", maxlength: "Maksimal 20 karakter" },
                    wa: { required: "Nomor WhatsApp wajib diisi", pattern: "Harus dimulai dengan 62 dan hanya angka" },
                    alamat: { required: "Alamat wajib diisi" }
                },
                submitHandler: function (form) {
                    $('.error-text').text('');
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        dataType: 'json',
                        success: function (response) {
                            if (response.status) {
                                Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message }).then(() => {
                                    window.location = response.redirect;
                                });
                            } else {
                                if (response.msgField) {
                                    $.each(response.msgField, function (field, msg) {
                                        $('#error-' + field).text(msg[0]);
                                    });
                                }
                                Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                            }
                        },
                        error: function () {
                            Swal.fire({ icon: 'error', title: 'Kesalahan Server', text: 'Terjadi kesalahan saat proses registrasi.' });
                        }
                    });
                    return false;
                },
                errorPlacement: function (error, element) {
                    const parent = element.closest('.form-group') || element.parent();
                    parent.find('.error-text').text(error.text());
                },
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
</body>

</html>
