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
            $table->integer("fk_id_htrans");
            $table->integer("fk_id_alat")->nullable();//dibuat null krn opsional
            $table->integer("harga_sewa_alat")->nullable();
            $table->integer("subtotal_alat")->nullable();//harga * durasi
            $table->integer("total_komisi_pemilik")->nullable();// komisi * durasi = 20.000 * 2
            $table->integer("total_komisi_tempat")->nullable();
            $table->timestamps();
            $table->softDeletes();
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
