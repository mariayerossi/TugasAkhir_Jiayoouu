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
            $table->integerIncrements('id_lapangan');
            $table->string('nama_lapangan');
            $table->string('kategori_lapangan');
            $table->string('tipe_lapangan');
            $table->string('lokasi_lapangan');
            $table->string('kota_lapangan');
            $table->string('deskripsi_lapangan',500);
            $table->string('luas_lapangan');
            $table->integer('harga_sewa_lapangan');
            $table->string('status_lapangan');
            $table->unsignedInteger('pemilik_lapangan');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('pemilik_lapangan')
                  ->references('id_tempat')
                  ->on('pihak_tempat');
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
