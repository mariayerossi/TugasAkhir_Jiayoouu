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
            $table->integer('req_lapangan');
            $table->integer('req_id_alat');
            $table->integer('fk_id_tempat');
            $table->timestamps();
            $table->softDeletes();
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
