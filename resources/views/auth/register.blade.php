@extends('layouts.guest')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card card-laporan">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-1">Register</h4>
                <p class="text-muted mb-4">Buat akun warga untuk mulai melapor.</p>

                <form method="POST" action="{{ route('register.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="fa-solid fa-user-plus me-2"></i>Register
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-decoration-none">Sudah punya akun? Login.</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
