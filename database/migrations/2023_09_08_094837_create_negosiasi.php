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
        Schema::create('negosiasi', function (Blueprint $table) {
            $table->integerIncrements("id_negosiasi");
            $table->string("isi_negosiasi", 500);
            $table->timestamp("waktu_negosiasi");
            $table->unsignedInteger("fk_id_request");
            $table->string("jenis_request");
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negosiasi');
    }
};
