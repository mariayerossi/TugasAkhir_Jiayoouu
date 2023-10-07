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
        Schema::create('extend_waktu', function (Blueprint $table) {
            $table->integerIncrements("id_extend");
            $table->time("jam_sewa");
            $table->integer("durasi_extend");
            $table->unsignedInteger("fk_id_htrans");
            $table->integer("subtotal_lapangan");
            $table->integer("subtotal_alat");
            $table->integer("total");
            $table->string("status_extend");
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
        Schema::dropIfExists('extend_waktu');
    }
};
