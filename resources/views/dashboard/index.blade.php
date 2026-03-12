@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Selamat datang, {{ auth()->user()->name }}</h3>
            <div class="text-muted">Kelola laporan lingkungan Anda.</div>
        </div>
        @if ((auth()->user()->role ?? null) !== 'admin')
            <a href="{{ route('laporans.create') }}" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-plus me-2"></i>Buat Laporan Baru
            </a>
        @endif
    </div>

    <h5 class="fw-semibold mb-3">Laporan Terbaru</h5>

    <div class="row g-3">
        @forelse ($laporans as $laporan)
            @php
                $badge = $laporan->status === 'menunggu' ? 'badge-menunggu' : ($laporan->status === 'diproses' ? 'badge-diproses' : ($laporan->status === 'ditolak' ? 'badge-ditolak' : 'badge-selesai'));
                $foto = $laporan->foto_url ?: '/images/placeholder-report.svg';
                $lastTanggapan = $laporan->tanggapans->first();
            @endphp
            <div class="col-sm-6 col-xl-4">
                <div class="card card-laporan h-100">
                    <img src="{{ $foto }}" class="w-100" alt="Foto laporan" style="height:190px; object-fit:cover;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="fw-bold">{{ $laporan->judul }}</div>
                                <div class="text-muted small">
                                    <i class="fa-solid fa-tag me-1"></i>{{ $laporan->kategori }}
                                    <span class="mx-2">•</span>
                                    <i class="fa-solid fa-location-dot me-1"></i>{{ $laporan->lokasi }}
                                </div>

                                @if ($lastTanggapan)
                                    <div class="text-muted small mt-2">
                                        <div class="fw-semibold">Penanganan:</div>
                                        <div>{{ \Illuminate\Support\Str::limit($lastTanggapan->isi_tanggapan, 120) }}</div>
                                    </div>
                                @endif
                            </div>
                            <span class="badge {{ $badge }}">{{ $laporan->status }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3 d-flex gap-2">
                        <a href="{{ route('laporans.show', $laporan) }}" class="btn btn-primary btn-sm">Detail</a>
                        @if ((auth()->user()->role ?? null) !== 'admin' && $laporan->user_id === auth()->id())
                            <a href="{{ route('laporans.edit', $laporan) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info mb-0">Belum ada laporan.</div>
            </div>
        @endforelse
    </div>
@endsection
