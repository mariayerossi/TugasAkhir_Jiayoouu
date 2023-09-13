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
        Schema::create('htrans', function (Blueprint $table) {
            $table->integerIncrements("id_htrans");
            $table->integer("fk_id_lapangan");
            $table->integer("subtotal_lapangan");// harga * durasi
            $table->timestamp("tanggal_trans");
            $table->date("tanggal_sewa");
            $table->time("jam_sewa");
            $table->integer("durasi_sewa");//jam
            $table->integer("total_trans");
            $table->integer("fk_id_user");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('htrans');
    }
};
