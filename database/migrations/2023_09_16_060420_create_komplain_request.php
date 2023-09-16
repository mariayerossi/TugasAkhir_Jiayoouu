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
            $table->integer("fk_id_request");
            $table->string("jenis_request");
            $table->timestamp("waktu_komplain");
            $table->string("status_komplain");//Menunggu, Diterima, Ditolak
            $table->string("penanganan_komplain")->nullable();//pengembalian dana ke pemilik/pihak, tidak ada dll
            $table->timestamps();
            $table->softDeletes();
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
