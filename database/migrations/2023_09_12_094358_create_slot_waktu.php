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
        Schema::create('slot_waktu', function (Blueprint $table) {
            $table->integerIncrements("id_slot");
            $table->string("hari");
            $table->time("jam_buka");
            $table->time("jam_tutup");
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
        Schema::dropIfExists('slot_waktu');
    }
};
