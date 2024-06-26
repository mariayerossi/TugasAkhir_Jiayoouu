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
            $table->integerIncrements('id_alat');
            $table->string('nama_alat');
            $table->unsignedInteger('fk_id_kategori');
            $table->string('deskripsi_alat', 500);
            $table->float('berat_alat');
            $table->string('ukuran_alat');
            $table->integer('komisi_alat');
            $table->integer('ganti_rugi_alat');
            $table->string('status_alat');
            $table->unsignedInteger('fk_id_pemilik')->nullable();
            $table->unsignedInteger('fk_id_tempat')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_pemilik')
                  ->references('id_pemilik')
                  ->on('pemilik_alat');
            $table->foreign('fk_id_tempat')
                  ->references('id_tempat')
                  ->on('pihak_tempat');
            $table->foreign('fk_id_kategori')
                  ->references('id_kategori')
                  ->on('kategori');
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
