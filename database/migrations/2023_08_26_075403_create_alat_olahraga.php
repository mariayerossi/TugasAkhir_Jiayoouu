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
        Schema::create('alat_olahraga', function (Blueprint $table) {
            $table->id('id_alat');
            $table->string('nama_alat');
            $table->string('kategori_alat');
            $table->string('deskripsi_alat', 500);
            $table->float('berat_alat');
            $table->string('ukuran_alat');
            $table->integer('stok_alat');
            $table->integer('komisi_alat');
            $table->integer('ganti_rugi_alat');
            $table->string('status_alat');
            $table->integer('pemilik_alat');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat_olahraga');
    }
};
