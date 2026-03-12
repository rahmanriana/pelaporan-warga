<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Laporan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = Laporan::query()->latest();

        if (($user->role ?? null) !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $laporans = $query->take(6)->get();

        return view('dashboard.index', [
            'laporans' => $laporans,
        ]);
    }
}
