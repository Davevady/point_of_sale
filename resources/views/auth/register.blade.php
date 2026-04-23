@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="container">
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>

                <div class="col-lg-7">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                        </div>

                        <form class="user" method="POST" action="{{ route('register.post') }}">
                            @csrf

                            <div class="form-group">
                                <input type="text"
                                       name="name"
                                       class="form-control form-control-user"
                                       placeholder="Full Name"
                                       value="{{ old('name') }}">
                                @error('name')
                                    <small class="text-danger d-block mt-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input type="email"
                                       name="email"
                                       class="form-control form-control-user"
                                       placeholder="Email Address"
                                       value="{{ old('email') }}">
                                @error('email')
                                    <small class="text-danger d-block mt-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <input type="password"
                                           name="password"
                                           class="form-control form-control-user"
                                           placeholder="Password">
                                </div>
                                <div class="col-sm-6">
                                    <input type="password"
                                           name="password_confirmation"
                                           class="form-control form-control-user"
                                           placeholder="Repeat Password">
                                </div>
                            </div>

                            @error('password')
                                <small class="text-danger d-block mt-2 mb-3">{{ $message }}</small>
                            @enderror

                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Register Account
                            </button>
                        </form>

                        <hr>

                        <div class="text-center">
                            <a class="small" href="{{ route('login') }}">Already have an account? Login!</a>
                        </div>

                        <div class="text-center mt-2">
                            <a class="small" href="{{ route('landing') }}">Back to Landing Page</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection