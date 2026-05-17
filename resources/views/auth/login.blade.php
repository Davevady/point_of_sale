@extends('layouts.auth')

@section('title', 'Login - ' . config('app.name'))

@section('content')
<style>
    .auth-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .login-card {
        border-radius: 28px;
        overflow: hidden;
    }

    .login-brand-panel {
        background:
            radial-gradient(circle at top left, rgba(255, 255, 255, .22), transparent 32%),
            linear-gradient(160deg, #1d4ed8 0%, #2563eb 55%, #0f172a 100%);
        color: #fff;
        position: relative;
    }

    .brand-icon {
        width: 96px;
        height: 96px;
        border-radius: 26px;
        background: rgba(255, 255, 255, .18);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(8px);
        box-shadow: 0 18px 40px rgba(15, 23, 42, .25);
    }

    .feature-list {
        margin-top: 28px;
    }

    .feature-list div {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        color: rgba(255, 255, 255, .85);
        font-size: 14px;
    }

    .login-panel {
        background: #fff;
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

    .form-control-user {
        background: #f8fafc;
        border: 1px solid #dbeafe;
        font-weight: 500;
    }

    .form-control-user:focus {
        background: #fff;
        border-color: #2563eb;
        box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .15);
    }

    .btn-login {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border: none;
        border-radius: 999px;
        padding: 13px 18px;
        font-weight: 600;
        box-shadow: 0 12px 24px rgba(37, 99, 235, .28);
    }

    .btn-login:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
    }

    .back-link {
        color: #2563eb;
        font-weight: 600;
    }
</style>

<div class="container auth-wrapper">
    <div class="row justify-content-center w-100">
        <div class="col-xl-9 col-lg-10 col-md-11">
            <div class="card login-card border-0 shadow-lg">
                <div class="card-body p-0">
                    <div class="row no-gutters">

                        <div class="col-lg-6 d-none d-lg-flex align-items-center login-brand-panel">
                            <div class="p-5">
                                <div class="brand-icon mb-4">
                                    <i class="fas fa-cash-register fa-3x"></i>
                                </div>

                                <h1 class="h3 font-weight-bold mb-3">
                                    {{ config('app.name') }}
                                </h1>

                                <p class="mb-0" style="color: rgba(255,255,255,.82);">
                                    Masuk ke sistem untuk mengelola data dealer,
                                    pelanggan, product, dan aktivitas penjualan.
                                </p>
                            </div>
                        </div>

                        <div class="col-lg-6 login-panel">
                            <div class="p-5">
                                <div class="text-center mb-4">
                                    <span class="app-pill">
                                        Dealer Flow Access
                                    </span>

                                    <h1 class="h3 text-gray-900 font-weight-bold mb-2">
                                        Selamat Datang Kembali
                                    </h1>
                                </div>

                                <form class="user" method="POST" action="{{ route('login.post') }}">
                                    @csrf

                                    <div class="form-group">
                                        <input type="email"
                                               name="email"
                                               class="form-control form-control-user"
                                               placeholder="Alamat email"
                                               value="{{ old('email') }}"
                                               autocomplete="email"
                                               autofocus>
                                        @error('email')
                                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="password"
                                               name="password"
                                               class="form-control form-control-user"
                                               placeholder="Password"
                                               autocomplete="current-password">
                                        @error('password')
                                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-login btn-user btn-block">
                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                        Login
                                    </button>
                                </form>

                                <hr class="my-4">

                                <div class="text-center">
                                    <a class="small back-link" href="{{ route('landing') }}">
                                        <i class="fas fa-arrow-left mr-1"></i>
                                        Kembali ke Landing Page
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
     </div>
</div>
@endsection