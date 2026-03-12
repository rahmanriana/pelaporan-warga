<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Pelaporan Warga') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="/css/pelaporan.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-pelaporan">
    <div class="container-fluid px-3">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <i class="fa-solid fa-bullhorn me-2"></i>Pelaporan Warga
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navApp" aria-controls="navApp" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navApp">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('laporans.index') }}">Laporan</a></li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <span class="text-white-50 small">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light btn-sm" type="submit">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row g-0">
        <aside class="col-lg-2 sidebar p-3">
            <div class="d-grid gap-2">
                @if ((auth()->user()->role ?? null) !== 'admin')
                    <a class="nav-link {{ request()->routeIs('laporans.create') ? 'active' : '' }}" href="{{ route('laporans.create') }}">
                        <i class="fa-solid fa-plus me-2"></i>Buat Laporan
                    </a>
                @endif
                @if ((auth()->user()->role ?? null) !== 'admin')
                    <a class="nav-link {{ request()->routeIs('laporans.mine') ? 'active' : '' }}" href="{{ route('laporans.mine') }}">
                        <i class="fa-solid fa-file-lines me-2"></i>Laporan Saya
                    </a>
                @endif
                @if ((auth()->user()->role ?? null) === 'admin')
                    <a class="nav-link {{ request()->routeIs('admin.panel') ? 'active' : '' }}" href="{{ route('admin.panel') }}">
                        <i class="fa-solid fa-user-shield me-2"></i>Kelola Laporan
                    </a>
                @endif
            </div>
        </aside>

        <main class="col-lg-10 p-4">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="fw-semibold mb-1">Terjadi kesalahan:</div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
