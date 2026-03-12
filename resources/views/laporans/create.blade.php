@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0">Buat Laporan</h4>
        <a href="{{ route('laporans.mine') }}" class="btn btn-outline-primary btn-sm">Kembali</a>
    </div>

    <div class="card card-laporan">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('laporans.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Judul laporan</label>
                    <input type="text" name="judul" value="{{ old('judul') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keluhan</label>
                    <input type="text" name="kategori" value="{{ old('kategori') }}" class="form-control" required>
                    <div class="form-text">Contoh: Sampah menumpuk, banjir, jalan berlubang, lampu jalan mati, dll.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" value="{{ old('lokasi') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="form-control" required>
                    <div class="form-text">Contoh: 08xxxxxxxxxx atau +62xxxxxxxxxx</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi laporan</label>
                    <textarea name="deskripsi" rows="4" class="form-control" required>{{ old('deskripsi') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Upload foto</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                    <div class="form-text">Maks 2MB (JPG/PNG).</div>
                </div>

                <button class="btn btn-primary" type="submit">
                    <i class="fa-solid fa-paper-plane me-2"></i>Kirim Laporan
                </button>
            </form>
        </div>
    </div>
@endsection
