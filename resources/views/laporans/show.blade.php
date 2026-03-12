@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')

@section('content')
    @php
        $badge = $laporan->status === 'menunggu' ? 'badge-menunggu' : ($laporan->status === 'diproses' ? 'badge-diproses' : ($laporan->status === 'ditolak' ? 'badge-ditolak' : 'badge-selesai'));
        $foto = $laporan->foto_url ?: '/images/placeholder-report.svg';
    @endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Detail Laporan</h4>
            <div class="text-muted small">{{ $laporan->judul }}</div>
        </div>
        <a href="{{ route('laporans.index') }}" class="btn btn-outline-primary btn-sm">Kembali</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card card-laporan">
                <img src="{{ $foto }}" alt="Foto laporan" class="w-100" style="max-height:420px; object-fit:cover;">
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card card-laporan">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <div class="text-muted small">Judul</div>
                            <div class="h5 fw-bold">{{ $laporan->judul }}</div>
                        </div>
                        <span class="badge {{ $badge }}">{{ $laporan->status }}</span>
                    </div>

                    <hr>

                    <div class="mb-2"><span class="text-muted">Keluhan:</span> <span class="fw-semibold">{{ $laporan->kategori }}</span></div>
                    <div class="mb-2"><span class="text-muted">Lokasi:</span> <span class="fw-semibold">{{ $laporan->lokasi }}</span></div>
                    <div class="mb-2"><span class="text-muted">Nomor HP:</span> <span class="fw-semibold">{{ $laporan->no_hp ?? '-' }}</span></div>
                    <div class="mb-2"><span class="text-muted">Tanggal:</span> <span class="fw-semibold">{{ optional($laporan->created_at)->format('d M Y, H:i') }}</span></div>
                    <div class="mt-3">
                        <div class="text-muted">Deskripsi</div>
                        <div class="mt-1">{{ $laporan->deskripsi }}</div>
                    </div>

                    @auth
                        @if ((auth()->user()->role ?? null) === 'admin')
                            <hr>
                            <div class="mb-3">
                                <div class="text-muted small">Pengirim</div>
                                <div class="fw-semibold">{{ $laporan->user->name ?? '-' }}</div>
                                <div class="text-muted small">{{ $laporan->user->email ?? '' }}</div>
                            </div>

                            <hr>
                            <form method="POST" action="{{ route('admin.tanggapan.store', $laporan) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Penanganan yang akan dilakukan</label>
                                    <textarea name="isi_tanggapan" class="form-control" rows="3" required></textarea>
                                    <div class="form-text">Wajib diisi saat admin mengubah status.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Update status</label>
                                    <select name="status" class="form-select" required>
                                        @foreach (['menunggu','diproses','selesai','ditolak'] as $st)
                                            <option value="{{ $st }}" {{ $laporan->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa-solid fa-reply me-2"></i>Kirim Tanggapan
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h5 class="fw-semibold mb-3">Tanggapan Admin</h5>

        <div class="row g-3">
            @forelse ($laporan->tanggapans as $t)
                <div class="col-12">
                    <div class="card card-laporan">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between">
                                <div class="fw-bold">
                                    <i class="fa-solid fa-user-shield me-2"></i>{{ $t->admin->name ?? 'Admin' }}
                                </div>
                                <div class="text-muted small">{{ optional($t->created_at)->format('d M Y, H:i') }}</div>
                            </div>
                            <div class="mt-2">{{ $t->isi_tanggapan }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning mb-0">Belum ada tanggapan admin.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
