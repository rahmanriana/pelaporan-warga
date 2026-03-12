@extends('layouts.guest')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card card-laporan">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-1">Login</h4>
                <p class="text-muted mb-4">Masuk untuk mengakses beranda.</p>

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email / Username</label>
                        <input type="text" name="email" value="{{ old('email') }}" class="form-control" required>
                        <div class="form-text">Contoh admin login: username <span class="fw-semibold">admin</span> dan password <span class="fw-semibold">admin123</span>.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>Login
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('register') }}" class="text-decoration-none">Belum punya akun? Register.</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
