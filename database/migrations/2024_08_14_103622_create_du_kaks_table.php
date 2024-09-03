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
        Schema::create('du_kak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->referencesOnDelete();
            $table->foreignId('sub_bidang_id')->constrained('dm_sub_bidang')->referencesOnDelete()->nullable();
            $table->foreignId('tahun_id')->constrained('dm_tahun')->referencesOnDelete();
            $table->foreignId('sub_kegiatan_id')->constrained('dm_sub_kegiatan')->referencesOnDelete()->nullable();
            $table->string('jenis_kak');
            $table->string('pdf')->nullable();
            $table->string('status')->default('belum diverifikasi');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('du_kak');
    }
};
