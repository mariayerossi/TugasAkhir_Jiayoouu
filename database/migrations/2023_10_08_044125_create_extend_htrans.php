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
        Schema::create('extend_htrans', function (Blueprint $table) {
            $table->integerIncrements("id_extend_htrans");
            $table->unsignedInteger("fk_id_htrans");
            $table->date("tanggal_extend");
            $table->time("jam_sewa");
            $table->integer("durasi_extend");
            $table->integer("subtotal_lapangan");
            $table->integer("subtotal_alat");
            $table->integer("total");
            $table->integer("pendapatan_website_lapangan");
            $table->string("status_extend");//menunggu, diterima, ditolak
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('extend_htrans');
    }
};
