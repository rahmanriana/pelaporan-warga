<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LaporanStatusRequest;
use App\Http\Requests\Api\LaporanStoreRequest;
use App\Http\Requests\Api\LaporanUpdateRequest;
use App\Models\Laporan;
use App\Models\Tanggapan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    public function index()
    {
        $laporans = Laporan::with([
            'user',
            'tanggapans' => function ($q) {
                $q->latest()->with('admin');
            },
        ])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data laporan',
            'data' => $laporans,
        ]);
    }

    public function show($id)
    {
        $laporan = Laporan::with([
            'user',
            'tanggapans' => function ($q) {
                $q->latest()->with('admin');
            },
        ])->find($id);

        if (!$laporan) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil detail laporan',
            'data' => $laporan,
        ]);
    }

    public function store(LaporanStoreRequest $request)
    {
        $user = auth('api')->user();

        if (($user->role ?? null) === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak dapat membuat laporan',
                'data' => null,
            ], Response::HTTP_FORBIDDEN);
        }

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('laporans', 'public');
        }

        $laporan = Laporan::create([
            'user_id' => $user->id,
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'lokasi' => $request->lokasi,
            'no_hp' => $request->no_hp,
            'deskripsi' => $request->deskripsi,
            'foto' => $path,
            'status' => 'menunggu',
        ]);

        $laporan->load(['user', 'tanggapans.admin']);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dibuat',
            'data' => $laporan,
        ], Response::HTTP_CREATED);
    }

    public function update(LaporanUpdateRequest $request, $id)
    {
        $laporan = Laporan::find($id);

        if (!$laporan) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $user = auth('api')->user();
        $isAdmin = ($user->role ?? null) === 'admin';
        $isOwner = $laporan->user_id === $user->id;

        if (!$isAdmin && !$isOwner) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
                'data' => null,
            ], Response::HTTP_FORBIDDEN);
        }

        if ($request->hasFile('foto')) {
            if ($laporan->foto) {
                Storage::disk('public')->delete($laporan->foto);
            }
            $laporan->foto = $request->file('foto')->store('laporans', 'public');
        }

        $laporan->fill($request->only(['judul', 'kategori', 'lokasi', 'no_hp', 'deskripsi']));

        if ($isAdmin && $request->filled('status')) {
            $laporan->status = $request->status;

            Tanggapan::create([
                'laporan_id' => $laporan->id,
                'admin_id' => $user->id,
                'isi_tanggapan' => (string) $request->input('isi_tanggapan'),
            ]);
        }

        $laporan->save();
        $laporan->load(['user', 'tanggapans.admin']);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diupdate',
            'data' => $laporan,
        ]);
    }

    public function destroy($id)
    {
        $laporan = Laporan::find($id);

        if (!$laporan) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $user = auth('api')->user();
        $isAdmin = ($user->role ?? null) === 'admin';
        $isOwner = $laporan->user_id === $user->id;

        if (!$isAdmin && !$isOwner) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
                'data' => null,
            ], Response::HTTP_FORBIDDEN);
        }

        if ($laporan->foto) {
            Storage::disk('public')->delete($laporan->foto);
        }

        $laporan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dihapus',
            'data' => null,
        ]);
    }

    public function updateStatus(LaporanStatusRequest $request, $id)
    {
        $laporan = Laporan::find($id);

        if (!$laporan) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $admin = auth('api')->user();

        Tanggapan::create([
            'laporan_id' => $laporan->id,
            'admin_id' => $admin->id,
            'isi_tanggapan' => (string) $request->input('isi_tanggapan'),
        ]);

        $laporan->status = $request->status;
        $laporan->save();

        $laporan->load(['user', 'tanggapans.admin']);

        return response()->json([
            'success' => true,
            'message' => 'Status laporan berhasil diupdate',
            'data' => $laporan,
        ]);
    }
}
