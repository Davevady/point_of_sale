<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - POS App</title>

    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-xl-8 col-lg-10 col-md-12">
                <div class="card o-hidden border-0 shadow-lg">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-light">
                                <div class="text-center p-4">
                                    <i class="fas fa-cash-register fa-5x text-primary mb-4"></i>
                                    <h2 class="h4 text-gray-900 mb-2">POS</h2>
                                    <p class="text-muted mb-0">
                                        Sistem point of sale sederhana berbasis Laravel.
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h3 text-gray-900 mb-2">Selamat Datang</h1>
                                        <p class="mb-4 text-muted">
                                            Kelola user dan akses dashboard aplikasi dari sini.
                                        </p>
                                    </div>

                                    <div class="d-grid gap-2">
                                        @auth
                                            <a href="{{ route('users.index') }}" class="btn btn-primary btn-user btn-block">
                                                Masuk ke Dashboard
                                            </a>

                                            <form action="{{ route('logout') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-user btn-block">
                                                    Logout
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-primary btn-user btn-block">
                                                Login
                                            </a>

                                            {{-- <a href="{{ route('register') }}" class="btn btn-success btn-user btn-block">
                                                Daftar
                                            </a> --}}
                                        @endauth
                                    </div>

                                    <hr>

                                    <div class="text-center small text-muted">
                                        Laravel + SB Admin 2
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assets/js/sb-admin-2.min.js') }}"></script>
</body>

</html>