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
        Schema::create('dtrans', function (Blueprint $table) {
            $table->integerIncrements("id_dtrans");
            $table->unsignedInteger("fk_id_htrans");
            $table->unsignedInteger("fk_id_alat");
            $table->integer("harga_sewa_alat");
            $table->integer("subtotal_alat");//harga * durasi
            $table->integer("total_komisi_pemilik")->nullable();// komisi * durasi = 20.000 * 2
            $table->integer("total_komisi_tempat");
            $table->unsignedInteger("fk_id_pemilik")->nullable();
            $table->unsignedInteger("fk_id_tempat")->nullable();
            $table->integer("pendapatan_website_alat")->nullable();//11% dari total_komisi_pemilik
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_htrans')
                  ->references('id_htrans')
                  ->on('htrans');
            $table->foreign('fk_id_alat')
                  ->references('id_alat')
                  ->on('alat_olahraga');
            $table->foreign('fk_id_pemilik')
                  ->references('id_pemilik')
                  ->on('pemilik_alat');
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
        Schema::dropIfExists('dtrans');
    }
};
