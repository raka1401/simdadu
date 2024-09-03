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
        Schema::table('dm_jenis_dokumen', function (Blueprint $table) {
            $table->foreignId('sub_bidang_id')->nullable()->constrained('dm_sub_bidang')->referencesOnDelete();
            $table->boolean('perangkat_daerah')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dm_jenis_dokumen', function (Blueprint $table) {
            // $table->foreignId('sub_bidang_id')->nullable()->constrained('dm_sub_bidang')->referencesOnDelete();
            // $table->boolean('perangkat_daerah')->default(0);
        });
    }
};
