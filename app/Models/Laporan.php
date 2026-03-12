<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Laporan extends Model
{
    use HasFactory;

    public const KATEGORI = [
        'sampah',
        'banjir',
        'jalan rusak',
        'lampu jalan mati',
    ];

    public const STATUS = [
        'menunggu',
        'diproses',
        'selesai',
        'ditolak',
    ];

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'kategori',
        'lokasi',
        'no_hp',
        'foto',
        'status',
        'kode_token',
    ];

    protected $appends = [
        'foto_url',
    ];

    protected static function booted()
    {
        static::creating(function (self $laporan) {
            if ($laporan->kode_token) {
                return;
            }

            do {
                $token = Str::upper(Str::random(10));
            } while (self::where('kode_token', $token)->exists());

            $laporan->kode_token = $token;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tanggapans()
    {
        return $this->hasMany(Tanggapan::class);
    }

    public function getFotoUrlAttribute()
    {
        if (!$this->foto) {
            return null;
        }

        $path = ltrim((string) $this->foto, '/');

        // Return a relative URL so it works on any host (IP/ngrok), not only APP_URL/localhost.
        if (Str::startsWith($path, 'storage/')) {
            return '/' . $path;
        }

        return '/storage/' . $path;
    }
}
