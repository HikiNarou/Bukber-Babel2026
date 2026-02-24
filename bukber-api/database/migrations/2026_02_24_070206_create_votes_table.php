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
        Schema::create('votes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lokasi_id')->constrained('lokasi')->cascadeOnDelete();
            $table->string('voter_name', 100);
            $table->string('voter_ip', 45)->nullable();
            $table->string('session_token', 64);
            $table->timestamps();

            $table->index('lokasi_id');
            $table->index('session_token');
            $table->unique('session_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
