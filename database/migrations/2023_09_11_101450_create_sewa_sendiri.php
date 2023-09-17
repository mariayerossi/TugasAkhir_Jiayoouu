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
        Schema::create('sewa_sendiri', function (Blueprint $table) {
            $table->integerIncrements("id_sewa");
            $table->unsignedInteger('req_lapangan');
            $table->unsignedInteger('req_id_alat');
            $table->unsignedInteger('fk_id_tempat');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('req_lapangan')
                  ->references('id_lapangan')
                  ->on('lapangan_olahraga');
            $table->foreign('req_id_alat')
                  ->references('id_alat')
                  ->on('alat_olahraga');
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
        Schema::dropIfExists('sewa_sendiri');
    }
};
