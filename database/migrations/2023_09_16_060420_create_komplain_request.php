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
            $table->string("keterangan_komplain");
            $table->integer("fk_id_request");
            $table->string("jenis_request");
            $table->string("status_komplain");//Menunggu, Diterima, Ditolak
            $table->string("penanganan_komplain");//pengembalian dana ke pemilik/pihak
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
