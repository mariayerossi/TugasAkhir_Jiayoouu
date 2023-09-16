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
        Schema::create('htrans', function (Blueprint $table) {
            $table->integerIncrements("id_htrans");
            $table->string("kode_trans");
            $table->unsignedInteger("fk_id_lapangan");
            $table->integer("subtotal_lapangan");// harga * durasi
            $table->integer("subtotal_alat");//jumlah dari subtotal semua alat
            $table->timestamp("tanggal_trans");
            $table->date("tanggal_sewa");
            $table->time("jam_sewa");
            $table->integer("durasi_sewa");//jam
            $table->integer("total_trans");
            $table->unsignedInteger("fk_id_user");
            $table->unsignedInteger("fk_id_tempat");
            $table->string("status_trans");
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_lapangan')
                  ->references('id_lapangan')
                  ->on('lapangan_olahraga');
            $table->foreign('fk_id_user')
                  ->references('id_user')
                  ->on('user');
            $table->foreign('fk_id_tempat')
                  ->references('id_tempat')
                  ->on('pihak_tempat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('htrans');
    }
};
