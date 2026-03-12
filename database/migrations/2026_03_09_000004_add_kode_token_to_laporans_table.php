<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->string('kode_token', 20)->nullable()->unique()->after('status');
        });

        $ids = DB::table('laporans')->whereNull('kode_token')->pluck('id');

        foreach ($ids as $id) {
            do {
                $token = Str::upper(Str::random(10));
            } while (DB::table('laporans')->where('kode_token', $token)->exists());

            DB::table('laporans')->where('id', $id)->update([
                'kode_token' => $token,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->dropUnique('laporans_kode_token_unique');
            $table->dropColumn('kode_token');
        });
    }
};
