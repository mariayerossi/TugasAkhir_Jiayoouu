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
        Schema::create('pemilik_alat', function (Blueprint $table) {
            $table->integerIncrements('id_pemilik');
            $table->string('nama_pemilik',255);
            $table->string('email_pemilik',255);
            $table->string('telepon_pemilik',15);
            $table->string('ktp_pemilik',255);
            $table->string('password_pemilik',255);
            $table->string('saldo_pemilik',255);
            $table->integer('norek_pemilik',20)->nullable();
            $table->string('nama_rek_pemilik',255)->nullable();
            $table->string('nama_bank_pemilik',255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemilik_alat');
    }
};
