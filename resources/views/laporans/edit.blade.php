@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0">Edit Laporan</h4>
        <a href="{{ route('laporans.show', $laporan) }}" class="btn btn-outline-primary btn-sm">Kembali</a>
    </div>

    <div class="card card-laporan">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('laporans.update', $laporan) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Judul laporan</label>
                    <input type="text" name="judul" value="{{ old('judul', $laporan->judul) }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keluhan</label>
                    <input type="text" name="kategori" value="{{ old('kategori', $laporan->kategori) }}" class="form-control" required>
                    <div class="form-text">Isi keluhan secara singkat. Contoh: Jalan berlubang di depan sekolah.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" value="{{ old('lokasi', $laporan->lokasi) }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $laporan->no_hp) }}" class="form-control" required>
                    <div class="form-text">Contoh: 08xxxxxxxxxx atau +62xxxxxxxxxx</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi laporan</label>
                    <textarea name="deskripsi" rows="4" class="form-control" required>{{ old('deskripsi', $laporan->deskripsi) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Upload foto (opsional)</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>

                <button class="btn btn-primary" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2"></i>Simpan
                </button>
            </form>
        </div>
    </div>
@endsection
