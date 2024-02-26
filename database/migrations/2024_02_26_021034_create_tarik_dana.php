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
        Schema::create('tarik_dana', function (Blueprint $table) {
            $table->integerIncrements('id_tarik');
            $table->unsignedInteger('fk_id_pemilik')->nullable();
            $table->unsignedInteger('fk_id_tempat')->nullable();
            $table->integer('total_tarik');
            $table->timestamp('tanggal_tarik');
            $table->string('status_tarik');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_pemilik')
                  ->references('id_pemilik')
                  ->on('pemilik_alat');
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
        Schema::dropIfExists('tarik_dana');
    }
};
