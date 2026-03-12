<?php

namespace App\Http\Requests\Api;

use App\Models\Laporan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LaporanStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(Laporan::STATUS)],
            'isi_tanggapan' => ['required', 'string'],
        ];
    }
}
