<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LaporanController extends Controller
{
    public function index()
    {
        $laporans = Laporan::with([
            'tanggapans' => function ($q) {
                $q->latest()->with('admin');
            },
        ])->latest()->get();

        return view('laporans.index', [
            'laporans' => $laporans,
        ]);
    }

    public function mine()
    {
        $laporans = Laporan::where('user_id', auth()->id())
            ->with([
                'tanggapans' => function ($q) {
                    $q->latest()->with('admin');
                },
            ])
            ->latest()
            ->get();

        return view('laporans.mine', [
            'laporans' => $laporans,
        ]);
    }

    public function create()
    {
        return view('laporans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'deskripsi' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('laporans', 'public');
        }

        $laporan = Laporan::create([
            'user_id' => auth()->id(),
            'judul' => $data['judul'],
            'kategori' => $data['kategori'],
            'lokasi' => $data['lokasi'],
            'no_hp' => $data['no_hp'],
            'deskripsi' => $data['deskripsi'],
            'foto' => $path,
            'status' => 'menunggu',
        ]);

        return redirect()->route('home')
            ->with('status', 'Laporan berhasil dibuat. Simpan kode token untuk mengecek laporan.')
            ->with('laporan_token', $laporan->kode_token);
    }

    public function show(Laporan $laporan)
    {
        $laporan->load(['tanggapans.admin']);

        return view('laporans.show', [
            'laporan' => $laporan,
        ]);
    }

    public function edit(Laporan $laporan)
    {
        $user = auth()->user();
        $isAdmin = ($user->role ?? null) === 'admin';

        if (!$isAdmin && $laporan->user_id !== $user->id) {
            abort(403);
        }

        return view('laporans.edit', [
            'laporan' => $laporan,
        ]);
    }

    public function update(Request $request, Laporan $laporan)
    {
        $user = auth()->user();
        $isAdmin = ($user->role ?? null) === 'admin';

        if (!$isAdmin && $laporan->user_id !== $user->id) {
            abort(403);
        }

        $rules = [
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'deskripsi' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ];

        $data = $request->validate($rules);

        if ($request->hasFile('foto')) {
            if ($laporan->foto) {
                Storage::disk('public')->delete($laporan->foto);
            }
            $laporan->foto = $request->file('foto')->store('laporans', 'public');
        }

        $laporan->judul = $data['judul'];
        $laporan->kategori = $data['kategori'];
        $laporan->lokasi = $data['lokasi'];
        $laporan->no_hp = $data['no_hp'];
        $laporan->deskripsi = $data['deskripsi'];

        $laporan->save();

        return redirect()->route('laporans.show', $laporan)->with('status', 'Laporan berhasil diupdate.');
    }

    public function destroy(Laporan $laporan)
    {
        $user = auth()->user();
        $isAdmin = ($user->role ?? null) === 'admin';

        if (!$isAdmin && $laporan->user_id !== $user->id) {
            abort(403);
        }

        if ($laporan->foto) {
            Storage::disk('public')->delete($laporan->foto);
        }

        $laporan->delete();

        return redirect()->route('laporans.index')->with('status', 'Laporan berhasil dihapus.');
    }
}
