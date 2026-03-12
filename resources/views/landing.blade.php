@extends('layouts.app')

@section('content')
    <div class="hero text-white p-5 mb-4">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="display-5 fw-bold">Sistem Pelaporan Warga</h1>
                <p class="lead mb-4">Dusun Anjung, Desa Tanjungsari, Salopa</p>
                <p class="lead mb-4">Laporkan masalah lingkungan di sekitar Anda dengan mudah, cepat, dan transparan.</p>
                <div class="d-flex gap-2 flex-wrap">
                    @if ((auth()->user()->role ?? null) === 'admin')
                        <a href="{{ route('admin.panel') }}" class="btn btn-light btn-lg">
                            <i class="fa-solid fa-user-shield me-2"></i>Kelola Laporan
                        </a>
                    @else
                        <a href="{{ route('laporans.create') }}" class="btn btn-light btn-lg">
                            <i class="fa-solid fa-plus me-2"></i>Buat Laporan
                        </a>
                        <a href="{{ route('laporans.mine') }}" class="btn btn-accent btn-lg">
                            <i class="fa-solid fa-file-lines me-2"></i>Laporan Saya
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="p-4 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-25">
                    <div class="mt-3 text-center">
                        <img src="{{ asset('images/logo-malta.png') }}" alt="Logo Pelaporan Warga" style="max-width: 250px; width: 100%; height: auto;" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="landing-main-bg rounded-4 p-3 p-md-4 mb-4">
        @if (session('laporan_token'))
            <div class="alert alert-info">
                <div class="fw-semibold mb-1">Kode Token Laporan Kamu</div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        Simpan kode ini untuk mengecek laporan di Beranda:
                        <span class="badge text-bg-dark ms-2" style="letter-spacing:.08em">{{ session('laporan_token') }}</span>
                    </div>
                    <a class="btn btn-sm btn-outline-primary" href="{{ url('/?token=' . session('laporan_token')) }}">
                        Cek Sekarang
                    </a>
                </div>
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card card-laporan text-bg-warning">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="small opacity-75">Total Laporan</div>
                            <div class="fs-3 fw-bold">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="fs-1 opacity-25">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-laporan bg-warning-subtle text-warning-emphasis">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="small opacity-75">Dalam Proses</div>
                            <div class="fs-3 fw-bold">{{ $stats['diproses'] ?? 0 }}</div>
                        </div>
                        <div class="fs-1 opacity-25">
                            <i class="fa-solid fa-spinner"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-laporan text-bg-success">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="small opacity-75">Selesai</div>
                            <div class="fs-3 fw-bold">{{ $stats['selesai'] ?? 0 }}</div>
                        </div>
                        <div class="fs-1 opacity-25">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-laporan text-bg-danger">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="small opacity-75">Ditolak</div>
                            <div class="fs-3 fw-bold">{{ $stats['ditolak'] ?? 0 }}</div>
                        </div>
                        <div class="fs-1 opacity-25">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-laporan mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">Cek Laporan dengan Kode Token</h5>
                        <div class="text-muted small">Masukkan kode token yang kamu terima setelah membuat laporan.</div>
                    </div>
                </div>

                <form method="GET" action="{{ url('/') }}" class="row g-2">
                    <div class="col-md-6">
                        <input type="text" name="token" value="{{ request('token') }}" class="form-control" placeholder="Contoh: ABCD12EFGH" required>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="fa-solid fa-magnifying-glass me-2"></i>Cek
                        </button>
                    </div>
                </form>

                @if (!empty($tokenNotFound))
                    <div class="alert alert-warning mt-3 mb-0">Kode token tidak ditemukan. Pastikan kamu memasukkan kode yang benar.</div>
                @endif

                @if (!empty($cekLaporan))
                    @php
                        $badge = $cekLaporan->status === 'menunggu'
                            ? 'badge-menunggu'
                            : ($cekLaporan->status === 'diproses'
                                ? 'badge-diproses'
                                : ($cekLaporan->status === 'ditolak'
                                    ? 'badge-ditolak'
                                    : 'badge-selesai'));
                    @endphp
                    <div class="mt-3">
                        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                            <div>
                                <div class="fw-semibold">{{ $cekLaporan->judul }}</div>
                                <div class="text-muted small"><i class="fa-solid fa-location-dot me-1"></i>{{ $cekLaporan->lokasi }}</div>
                                <div class="text-muted small">Token: <span class="badge text-bg-dark" style="letter-spacing:.08em">{{ $cekLaporan->kode_token }}</span></div>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $badge }}">{{ $cekLaporan->status }}</span>
                                <div class="mt-2">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('laporans.show', $cekLaporan) }}">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card card-laporan h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div>
                                <div class="fw-semibold mb-2">1) Tulis Keluhan</div>
                                <div class="text-muted">Isi judul, keluhan, lokasi, dan foto (opsional) agar laporan jelas.</div>
                            </div>
                            <div class="fs-1 opacity-25">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-laporan h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div>
                                <div class="fw-semibold mb-2">2) Kirim &amp; Simpan Token</div>
                                <div class="text-muted">Setelah terkirim, kamu akan mendapat kode token untuk mengecek laporan.</div>
                            </div>
                            <div class="fs-1 opacity-25">
                                <i class="fa-solid fa-paper-plane"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-laporan h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div>
                                <div class="fw-semibold mb-2">3) Pantau Progres</div>
                                <div class="text-muted">Gunakan token di Beranda untuk melihat status: menunggu, diproses, selesai, atau ditolak.</div>
                            </div>
                            <div class="fs-1 opacity-25">
                                <i class="fa-solid fa-eye"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
