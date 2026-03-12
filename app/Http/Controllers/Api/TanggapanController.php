<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TanggapanStoreRequest;
use App\Models\Laporan;
use App\Models\Tanggapan;
use Symfony\Component\HttpFoundation\Response;

class TanggapanController extends Controller
{
    public function store(TanggapanStoreRequest $request, $laporanId)
    {
        $laporan = Laporan::find($laporanId);

        if (!$laporan) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $admin = auth('api')->user();

        $tanggapan = Tanggapan::create([
            'laporan_id' => $laporan->id,
            'admin_id' => $admin->id,
            'isi_tanggapan' => $request->isi_tanggapan,
        ]);

        $tanggapan->load(['admin', 'laporan']);

        return response()->json([
            'success' => true,
            'message' => 'Tanggapan berhasil dibuat',
            'data' => $tanggapan,
        ], Response::HTTP_CREATED);
    }
}
