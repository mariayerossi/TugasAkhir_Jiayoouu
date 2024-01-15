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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->integerIncrements("id_notifikasi");
            $table->string("keterangan_notifikasi");
            $table->timestamp("waktu_notifikasi");
            $table->string("link_notifikasi");
            $table->unsignedInteger("fk_id_user")->nullable();
            $table->unsignedInteger("fk_id_pemilik")->nullable();
            $table->unsignedInteger("fk_id_tempat")->nullable();
            $table->unsignedInteger("admin")->nullable();
            $table->string("status_notifikasi");//Dibaca / Tidak
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_user')
                  ->references('id_user')
                  ->on('user');
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
        Schema::dropIfExists('notifikasi');
    }
};
