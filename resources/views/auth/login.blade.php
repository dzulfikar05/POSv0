<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Pengguna</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

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

        .login-wrapper {
            display: flex;
            width: 90%;
            max-width: 900px;
            height: 600px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            overflow: hidden;
            background-color: #fff;
        }

        .login-image {
            flex: 1;
            background: url('https://images.pexels.com/photos/12935074/pexels-photo-12935074.jpeg?cs=srgb&dl=pexels-imin-technology-276315592-12935074.jpg&fm=jpg') no-repeat center center;
            background-size: cover;
        }

        .login-form {
            flex: 1;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form h1 {
            font-weight: 600;
            margin-bottom: 20px;
            color: #007bff;
            font-size: 26px;
        }

        .form-control {
            border-radius: 8px;
            height: 45px;
        }

        .btn-primary {
            border-radius: 8px;
            font-weight: 600;
        }

        .links {
            display: flex;
            justify-content: space-between;
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
            .login-wrapper {
                flex-direction: column;
                height: auto;
            }

            .login-image {
                height: 200px;
                width: 100%;
            }

            .login-form {
                padding: 25px;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-image d-none d-md-block"></div>
        <div class="login-form">
            <h1>{{ $app_name ?? 'Point of Sale' }}</h1>
            <p class="text-muted mb-4">Silakan masuk untuk melanjutkan</p>

            <form action="{{ url('login') }}" method="POST" id="form-login">
                @csrf
                <div class="mb-3">
                    <input type="text" id="username" name="username" class="form-control" placeholder="Username">
                    <small id="error-username" class="error-text text-danger"></small>
                </div>
                <div class="mb-3">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                    <small id="error-password" class="error-text text-danger"></small>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Masuk</button>
            </form>

            <div class="links mt-3 d-flex justify-content-between flex-wrap gap-2">
                <a href="{{ url('register') }}">Belum punya akun?</a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $waSystem ?? '6281234567890') }}"
                    target="_blank">
                    Lupa Password? Hubungi Admin
                </a>
            </div>

        </div>
    </div>

    <!-- JS Dependencies -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $("#form-login").validate({
                rules: {
                    username: {
                        required: true,
                        minlength: 4,
                        maxlength: 20
                    },
                    password: {
                        required: true,
                        minlength: 6,
                        maxlength: 20
                    }
                },
                messages: {
                    username: {
                        required: "Username wajib diisi",
                        minlength: "Minimal 4 karakter",
                        maxlength: "Maksimal 20 karakter"
                    },
                    password: {
                        required: "Password wajib diisi",
                        minlength: "Minimal 6 karakter",
                        maxlength: "Maksimal 20 karakter"
                    }
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                }).then(() => {
                                    window.location = response.redirect;
                                });
                            } else {
                                $('.error-text').text('');
                                if (response.msgField) {
                                    $.each(response.msgField, function(prefix, val) {
                                        $('#error-' + prefix).text(val[0]);
                                    });
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Server',
                                text: 'Terjadi kesalahan pada server. Silakan coba lagi.'
                            });
                        }
                    });
                    return false;
                },
                errorPlacement: function(error, element) {
                    const parent = element.closest('.form-group') || element.parent();
                    parent.find('.error-text').text(error.text());
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
</body>

</html>
