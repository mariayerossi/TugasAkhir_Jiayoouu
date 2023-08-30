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
        Schema::create('lapangan_olahraga', function (Blueprint $table) {
            $table->id('id_lapangan');
            $table->string('nama_lapangan');
            $table->string('kategori_lapangan');
            $table->string('tipe_lapangan');
            $table->string('lokasi_lapangan');
            $table->string('deskripsi_lapangan');
            $table->string('luas_lapangan');
            $table->string('harga_sewa_lapangan');
            $table->string('status_lapangan');
            $table->integer('pemilik_lapangan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lapangan_olahraga');
    }
};
