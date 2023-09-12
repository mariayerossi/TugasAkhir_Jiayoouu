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
            $table->integer("fk_id_lapangan");
            $table->timestamps();
            $table->softDeletes();
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
