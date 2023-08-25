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
        Schema::create('pihak_tempat', function (Blueprint $table) {
            $table->id('id_tempat');
            $table->string('nama_tempat',255);
            $table->string('nama_pemilik_tempat',255);
            $table->string('email_tempat',255);
            $table->string('telepon_tempat',15);
            $table->string('alamat_tempat',255);
            $table->string('ktp_tempat',255);
            $table->string('npwp_tempat',255);
            $table->string('password_tempat',255);
            $table->string('saldo_tempat',255);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pihak_tempat');
    }
};
