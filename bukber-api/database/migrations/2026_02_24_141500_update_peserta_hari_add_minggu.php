<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peserta_hari', function (Blueprint $table): void {
            $table->unsignedTinyInteger('minggu')->default(1)->after('peserta_id');
        });

        DB::statement('UPDATE peserta_hari SET minggu = (SELECT minggu FROM peserta WHERE peserta.id = peserta_hari.peserta_id)');

        Schema::table('peserta_hari', function (Blueprint $table): void {
            $table->dropUnique('peserta_hari_peserta_id_hari_unique');
            $table->index(['minggu', 'hari']);
            $table->unique(['peserta_id', 'minggu', 'hari']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_hari', function (Blueprint $table): void {
            $table->dropUnique('peserta_hari_peserta_id_minggu_hari_unique');
            $table->dropIndex('peserta_hari_minggu_hari_index');
            $table->unique(['peserta_id', 'hari']);
            $table->dropColumn('minggu');
        });
    }
};
