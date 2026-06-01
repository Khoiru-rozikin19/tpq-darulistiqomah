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
        Schema::create('spp_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->tinyInteger('bulan'); // 1-12
            $table->string('tahun_ajaran'); // e.g. "2025/2026"
            $table->decimal('nominal', 15, 2)->default(20000);
            $table->date('tanggal_bayar');
            $table->enum('metode_bayar', ['tunai', 'transfer'])->default('tunai');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('users'); // petugas
            $table->timestamps();

            $table->unique(['santri_id', 'bulan', 'tahun_ajaran']); // prevent double payment
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spp_payments');
    }
};
