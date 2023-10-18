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
        Schema::create('komplain_trans', function (Blueprint $table) {
            $table->integerIncrements("id_komplain_trans");
            $table->string("jenis_komplain");
            $table->string("keterangan_komplain",500);
            $table->unsignedInteger("fk_id_htrans");
            $table->timestamp("waktu_komplain");
            $table->string("status_komplain");//Menunggu, Diterima, Ditolak
            $table->string("penanganan_komplain")->nullable();//pengembalian dana ke cust dan saldo tempat/pemilik dipotong, penutupan produk/akun, tidak ada dll
            $table->string("alasan_komplain")->nullable();//alasan komplain ditolak
            $table->unsignedInteger("fk_id_user");
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_user')
                  ->references('id_user')
                  ->on('user');
            $table->foreign('fk_id_htrans')
                  ->references('id_htrans')
                  ->on('htrans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komplain_trans');
    }
};
