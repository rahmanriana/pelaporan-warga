<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Tanggapan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function panel()
    {
        $laporans = Laporan::with('user')->latest()->get();

        return view('admin.panel', [
            'laporans' => $laporans,
        ]);
    }

    public function storeTanggapan(Request $request, Laporan $laporan)
    {
        $data = $request->validate([
            'isi_tanggapan' => ['required', 'string'],
            'status' => ['required', 'string', Rule::in(Laporan::STATUS)],
        ]);

        Tanggapan::create([
            'laporan_id' => $laporan->id,
            'admin_id' => auth()->id(),
            'isi_tanggapan' => $data['isi_tanggapan'],
        ]);

        $laporan->status = $data['status'];
        $laporan->save();

        return redirect()->route('laporans.show', $laporan)->with('status', 'Tanggapan terkirim dan status diperbarui.');
    }
}
