<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Point Of Sale') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- AdminLTE & Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    @include('guest.style')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-dark bg-custom-green fixed-top">
            <div class="container">
                <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center">
                    <span class="brand-text font-weight-bold d-none d-md-inline">{{ $title ?? 'POS System' }}</span>
                </a>


                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <!-- Search Form -->
                    <form class="navbar-search mx-auto my-md-0 my-2">
                        <div class="input-group w-100">
                            <input id="search-input" class="form-control" type="search" placeholder="Cari produk..."
                                aria-label="Search">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                        <div id="search-suggestions">
                            <!-- Search suggestions will be populated here -->
                        </div>
                    </form>
                </div>

                <!-- Right navbar links -->
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto icon-navbar"  style="margin-top:-12px !important">
                    <li class="nav-item">
                        <a class="nav-link header-icon" href="#" onclick="onShowCart()" id="cart-icon" >
                            <i class="fas fa-shopping-cart"></i>
                            @if ($cart != null)
                                <span class="badge cart-count" style="margin-top:10px"></span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link header-icon" href="#" onclick="onShowHistory()" id="history-icon">
                            <i class="fas fa-history"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#" id="profile-dropdown">
                            <div class="user-profile">
                                @if ($auth?->photo != null)
                                    <img src="/storage/uploads/photo/{{ $auth->photo }}" alt="User Profile">
                                @else
                                    <img src="/userNoImage.webp" alt="User Profile">
                                @endif
                                <span
                                    class="d-none d-md-inline text-white">{{ $auth != null ? $auth->nama : 'Guest' }}</span>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            @if ($auth != null)
                                <a href="#" onclick="onShowProfile()" class="dropdown-item">
                                    <i class="fas fa-user"></i> Profil Saya
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="/logout" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            @else
                                <a href="/login" class="dropdown-item">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>
        </nav>


        <!-- Hero Section -->
        <div id="hero-carousel" class="carousel slide " style="margin-top: 100px" data-ride="carousel">
            {{-- <ol class="carousel-indicators">
                <li data-target="#hero-carousel" data-slide-to="0" class="active"></li>
                <li data-target="#hero-carousel" data-slide-to="1"></li>
                <li data-target="#hero-carousel" data-slide-to="2"></li>
            </ol> --}}
            <div class="carousel-inner">
                <!-- First Slide -->
                <div class="carousel-item active">
                    <div class="container py-5">
                        <div class="row align-items-center">
                            <div class="col-md-12 text-center position-relative">
                                <div class="skeleton skeleton-img img-fluid" style="border-radius: 10px 10px 0px 0px;">
                                </div>
                                <div
                                    class="text-overlay p-3 text-white position-absolute top-50 start-50 translate-middle w-100">
                                    <div class="skeleton skeleton-text"></div>
                                    <div class="skeleton skeleton-subtext"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Repeat the same for other slides -->
                <div class="carousel-item">
                    <div class="container py-5">
                        <div class="row align-items-center">
                            <div class="col-md-12 text-center position-relative">
                                <div class="skeleton skeleton-img img-fluid"
                                    style="border-radius: 10px 10px 0px 0px;"></div>
                                <div
                                    class="text-overlay p-3 text-white position-absolute top-50 start-50 translate-middle w-100">
                                    <div class="skeleton skeleton-text"></div>
                                    <div class="skeleton skeleton-subtext"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="container py-5">
                        <div class="row align-items-center">
                            <div class="col-md-12 text-center position-relative">
                                <div class="skeleton skeleton-img img-fluid"
                                    style="border-radius: 10px 10px 0px 0px;"></div>
                                <div
                                    class="text-overlay p-3 text-white position-absolute top-50 start-50 translate-middle w-100">
                                    <div class="skeleton skeleton-text"></div>
                                    <div class="skeleton skeleton-subtext"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <a class="carousel-control-prev bg-dark" href="#hero-carousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next bg-dark" href="#hero-carousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper bg-white list-product">
            <div class="content">
                <div class="container py-5">
                    <h2 class="text-center mb-5 text-list-product">Daftar Produk</h2>

                    <div class="row">
                        <div class="col-md-4 offset-md-4 mb-4">
                            <select id="categoryFilter" class="form-control" onchange="getProductData()">
                                <option value="">Semua Kategori</option>
                                @foreach ($kategori as $cat)
                                    <option value="{{ $cat->kategori_id }}">{{ $cat->kategori_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="loading" class="text-center my-5" style="display: none;">
                        <i class="fas fa-spinner fa-spin fa-3x text-warning"></i>
                    </div>


                    <div class="row product-list">
                    </div>

                    <nav aria-label="Page navigation">
                        <ul id="pagination" class="pagination justify-content-center mt-5">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer bg-light">
            <div class="container py-4">
                {{-- <div class="row">
                    <div class="col-md-6">
                        <h5>About Us</h5>
                        <p>We provide modern point of sale solutions for businesses of all sizes.</p>
                    </div>

                    <div class="col-md-6">
                        <h5>Contact</h5>
                        <p>
                            <i class="fas fa-envelope mr-2"></i> info@posystem.com<br>
                            <i class="fas fa-phone mr-2"></i> +1 (123) 456-7890
                        </p>
                    </div>
                </div> --}}
                <div class="text-center mt-3">
                    <p>&copy; {{ date('Y') }} POS Zero. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->


    <style>
        .skeleton {
            background-color: #e0e0e0;
            background-image: linear-gradient(90deg, #e0e0e0 0px, #f0f0f0 40px, #e0e0e0 80px);
            background-size: 600px;
            animation: shimmer 1.5s infinite linear;
        }

        @keyframes shimmer {
            0% {
                background-position: -600px 0;
            }

            100% {
                background-position: 600px 0;
            }
        }

        .skeleton-img {
            height: 500px;
            width: 100%;
            object-fit: cover;
        }

        .skeleton-text {
            height: 20px;
            margin: 10px auto;
            width: 60%;
            border-radius: 4px;
        }

        .skeleton-subtext {
            height: 15px;
            margin: 5px auto;
            width: 40%;
            border-radius: 4px;
        }
    </style>
    <!-- Scripts -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @include('guest.profile')
    @include('guest.cart')
    @include('guest.history')
    @include('guest.script')

</body>

</html>
