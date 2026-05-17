<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome - {{ config('app.name') }}</title>

    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, .18), transparent 30%),
                linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #2563eb 100%);
        }

        .welcome-card {
            border-radius: 28px;
            overflow: hidden;
        }

        .brand-panel {
            background: linear-gradient(160deg, #eff6ff 0%, #ffffff 100%);
            position: relative;
        }

        .brand-badge {
            width: 92px;
            height: 92px;
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #2563eb;
            color: #fff;
            box-shadow: 0 16px 35px rgba(37, 99, 235, .35);
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #64748b;
            font-size: 14px;
            margin-top: 12px;
        }

        .feature-item i {
            color: #2563eb;
        }

        .login-panel {
            background: #ffffff;
        }

        .btn-dealer {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 999px;
            padding: 13px 18px;
            font-weight: 600;
            box-shadow: 0 12px 24px rgba(37, 99, 235, .28);
        }

        .btn-dealer:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
        }

        .btn-outline-danger {
            border-radius: 999px;
            padding: 13px 18px;
            font-weight: 600;
        }

        .app-pill {
            display: inline-block;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 14px;
            margin-bottom: 18px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-xl-9 col-lg-10 col-md-11">
                <div class="card welcome-card border-0 shadow-lg">
                    <div class="card-body p-0">
                        <div class="row no-gutters">

                            <div class="col-lg-6 d-none d-lg-flex align-items-center brand-panel">
                                <div class="p-5">
                                    <div class="brand-badge mb-4">
                                        <i class="fas fa-cash-register fa-3x"></i>
                                    </div>

                                    <h1 class="h3 text-gray-900 font-weight-bold mb-3">
                                        {{ config('app.name') }}
                                    </h1>

                                    <p class="text-muted mb-4">
                                        Platform manajemen dealer untuk membantu alur penjualan,
                                        data product, pelanggan, dan operasional berjalan lebih rapi.
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-6 login-panel">
                                <div class="p-5">
                                    <div class="text-center mb-4">
                                        <span class="app-pill">
                                            Dealer Flow
                                        </span>

                                        <h2 class="h3 text-gray-900 font-weight-bold mb-2">
                                            Selamat Datang
                                        </h2>

                                        <p class="text-muted mb-0">
                                            Masuk untuk mengelola aktivitas dan dashboard
                                            {{ config('app.name') }}.
                                        </p>
                                    </div>

                                    @auth
                                        <a href="{{ route('users.index') }}" class="btn btn-primary btn-dealer btn-user btn-block mb-3">
                                            <i class="fas fa-tachometer-alt mr-2"></i>
                                            Masuk ke Dashboard
                                        </a>

                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-user btn-block">
                                                <i class="fas fa-sign-out-alt mr-2"></i>
                                                Logout
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-primary btn-dealer btn-user btn-block">
                                            <i class="fas fa-sign-in-alt mr-2"></i>
                                            Login
                                        </a>
                                    @endauth

                                    <hr class="my-4">

                                    <div class="text-center small text-muted">
                                        © {{ date('Y') }} {{ config('app.name') }}.
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