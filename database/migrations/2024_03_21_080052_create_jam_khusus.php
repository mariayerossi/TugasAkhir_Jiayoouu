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
        Schema::create('jam_khusus', function (Blueprint $table) {
            $table->integerIncrements("id_jam");
            $table->date("tanggal");
            $table->time("jam_mulai");
            $table->time("jam_selesai");
            $table->unsignedInteger("fk_id_lapangan");
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_lapangan')
                  ->references('id_lapangan')
                  ->on('lapangan_olahraga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_khusus');
    }
};
