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
        Schema::create('files_alat', function (Blueprint $table) {
            $table->integerIncrements('id_file_alat');
            $table->string('nama_file_alat');
            $table->unsignedInteger('fk_id_alat');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_alat')
                  ->references('id_alat')
                  ->on('alat_olahraga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files_alat');
    }
};
