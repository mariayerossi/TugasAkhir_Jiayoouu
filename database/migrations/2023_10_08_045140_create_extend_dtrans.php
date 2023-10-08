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
        Schema::create('extend_dtrans', function (Blueprint $table) {
            $table->integerIncrements("id_extend_dtrans");
            $table->unsignedInteger("fk_id_extend_htrans");
            $table->unsignedInteger("fk_id_dtrans");
            $table->integer("harga_sewa_alat");
            $table->integer("subtotal_alat");//harga * durasi
            $table->integer("total_komisi_pemilik")->nullable();// komisi * durasi = 20.000 * 2
            $table->integer("total_komisi_tempat");
            $table->integer("pendapatan_website_alat")->nullable();//11% dari total_komisi_pemilik
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_extend_htrans')
                  ->references('id_extend_htrans')
                  ->on('extend_htrans');
            $table->foreign('fk_id_dtrans')
                  ->references('id_dtrans')
                  ->on('dtrans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extend_dtrans');
    }
};
