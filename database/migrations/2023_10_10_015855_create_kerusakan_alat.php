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
        Schema::create('kerusakan_alat', function (Blueprint $table) {
            $table->integerIncrements("id_kerusakan");
            $table->unsignedInteger("fk_id_dtrans");
            $table->string("kesengajaan");//ya / tidak
            $table->string("nama_file");
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_dtrans')
                  ->references('id_dtrans')
                  ->on('dtrans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerusakan_alat');
    }
};
