<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Laporan::count(),
            'diproses' => Laporan::where('status', 'diproses')->count(),
            'selesai' => Laporan::where('status', 'selesai')->count(),
            'ditolak' => Laporan::where('status', 'ditolak')->count(),
        ];

        $cekLaporan = null;
        $tokenNotFound = false;

        if ($request->filled('token')) {
            $token = trim((string) $request->input('token'));
            $cekLaporan = Laporan::where('kode_token', $token)->first();
            $tokenNotFound = !$cekLaporan;
        }

        return view('landing', [
            'stats' => $stats,
            'cekLaporan' => $cekLaporan,
            'tokenNotFound' => $tokenNotFound,
        ]);
    }
}
