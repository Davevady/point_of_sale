@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>

                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                </div>

                                <form class="user" method="POST" action="{{ route('login.post') }}">
                                    @csrf

                                    <div class="form-group">
                                        <input type="email"
                                               name="email"
                                               class="form-control form-control-user"
                                               placeholder="Enter Email Address..."
                                               value="{{ old('email') }}">
                                        @error('email')
                                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <input type="password"
                                               name="password"
                                               class="form-control form-control-user"
                                               placeholder="Password">
                                        @error('password')
                                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Login
                                    </button>
                                </form>

                                <hr>

                                {{-- <div class="text-center">
                                    <a class="small" href="{{ route('register') }}">Create an Account!</a>
                                </div> --}}

                                <div class="text-center mt-2">
                                    <a class="small" href="{{ route('landing') }}">Back to Landing Page</a>
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