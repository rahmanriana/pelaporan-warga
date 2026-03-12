@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Admin Panel</h4>
            <div class="text-muted small">Kelola laporan warga, tanggapan, dan status.</div>
        </div>
    </div>

    <div class="card card-laporan">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="px-3">Judul</th>
                        <th>Warga</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-end px-3">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($laporans as $laporan)
                        @php
                            $badge = $laporan->status === 'menunggu' ? 'badge-menunggu' : ($laporan->status === 'diproses' ? 'badge-diproses' : ($laporan->status === 'ditolak' ? 'badge-ditolak' : 'badge-selesai'));
                        @endphp
                        <tr>
                            <td class="px-3 fw-semibold">{{ $laporan->judul }}</td>
                            <td>
                                <div class="fw-semibold">{{ $laporan->user->name ?? '-' }}</div>
                                <div class="text-muted small">{{ $laporan->no_hp ?? '-' }}</div>
                            </td>
                            <td>{{ $laporan->kategori }}</td>
                            <td><span class="badge {{ $badge }}">{{ $laporan->status }}</span></td>
                            <td class="text-muted">{{ optional($laporan->created_at)->format('d M Y') }}</td>
                            <td class="text-end px-3">
                                <a href="{{ route('laporans.show', $laporan) }}" class="btn btn-sm btn-primary">Detail & Tanggapi</a>
                                <a href="{{ route('laporans.edit', $laporan) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada laporan.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
