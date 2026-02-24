<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peserta', function (Blueprint $table): void {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->string('nama_lengkap', 100);
            $table->tinyInteger('minggu')->comment('Minggu Ramadhan 1-4');
            $table->unsignedInteger('budget_per_orang')->comment('Budget dalam Rupiah');
            $table->text('catatan')->nullable();
            $table->string('device_fingerprint', 64)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('minggu');
            $table->index('created_at');
            $table->index('device_fingerprint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
