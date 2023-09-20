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
        Schema::create('request_permintaan', function (Blueprint $table) {
            $table->integerIncrements('id_permintaan');
            $table->integer('req_harga_sewa');
            $table->unsignedInteger('req_lapangan');
            $table->date('req_tanggal_mulai');
            $table->date('req_tanggal_selesai');
            $table->unsignedInteger('req_id_alat');
            $table->unsignedInteger('fk_id_tempat');
            $table->unsignedInteger('fk_id_pemilik');
            $table->timestamp('tanggal_minta');
            $table->string('status_permintaan');
            $table->string('kode_mulai')->nullable();
            $table->string('kode_selesai')->nullable();
            $table->string("status_alat")->nullable();//null/dikembalikan
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('req_lapangan')
                  ->references('id_lapangan')
                  ->on('lapangan_olahraga');
            $table->foreign('req_id_alat')
                  ->references('id_alat')
                  ->on('alat_olahraga');
            $table->foreign('fk_id_tempat')
                  ->references('id_tempat')
                  ->on('pihak_tempat');
            $table->foreign('fk_id_pemilik')
                  ->references('id_pemilik')
                  ->on('pemilik_alat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_permintaan');
    }
};
