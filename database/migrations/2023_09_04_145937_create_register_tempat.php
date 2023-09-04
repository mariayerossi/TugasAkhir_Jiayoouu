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
        Schema::create('register_tempat', function (Blueprint $table) {
            $table->integerIncrements('id_register');
            $table->string('nama_tempat_reg',255);
            $table->string('nama_pemilik_tempat_reg',255);
            $table->string('email_tempat_reg',255);
            $table->string('telepon_tempat_reg',15);
            $table->string('alamat_tempat_reg',255);
            $table->string('ktp_tempat_reg',255);
            $table->string('npwp_tempat_reg',255);
            $table->string('password_tempat_reg',255);
            $table->string('saldo_tempat_reg',255);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_tempat');
    }
};
