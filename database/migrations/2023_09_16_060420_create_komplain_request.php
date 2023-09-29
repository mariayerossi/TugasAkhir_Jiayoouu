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
        Schema::create('komplain_request', function (Blueprint $table) {
            $table->integerIncrements("id_komplain_req");
            $table->string("jenis_komplain");
            $table->string("keterangan_komplain",500);
            $table->unsignedInteger("fk_id_permintaan")->nullable();
            $table->unsignedInteger("fk_id_penawaran")->nullable();
            $table->timestamp("waktu_komplain");
            $table->string("status_komplain");//Menunggu, Diterima, Ditolak
            $table->string("penanganan_komplain")->nullable();//pengembalian dana ke pemilik/pihak, tidak ada dll
            $table->unsignedInteger("fk_id_pemilik")->nullable();
            $table->unsignedInteger("fk_id_tempat")->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_pemilik')
                  ->references('id_pemilik')
                  ->on('pemilik_alat');
            $table->foreign('fk_id_tempat')
                  ->references('id_tempat')
                  ->on('pihak_tempat');
            $table->foreign('fk_id_permintaan')
                  ->references('id_permintaan')
                  ->on('request_permintaan');
            $table->foreign('fk_id_penawaran')
                  ->references('id_penawaran')
                  ->on('request_penawaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komplain_request');
    }
};
