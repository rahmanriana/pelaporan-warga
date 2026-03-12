<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LaporanUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['sometimes', 'required', 'string', 'max:255'],
            'kategori' => ['sometimes', 'required', 'string', 'max:255'],
            'lokasi' => ['sometimes', 'required', 'string', 'max:255'],
            'no_hp' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'deskripsi' => ['sometimes', 'required', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'status' => ['sometimes', 'required', 'string', 'in:menunggu,diproses,selesai,ditolak'],
            'isi_tanggapan' => ['required_with:status', 'string'],
        ];
    }
}
