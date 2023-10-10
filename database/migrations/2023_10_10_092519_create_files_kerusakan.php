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
        Schema::create('files_kerusakan', function (Blueprint $table) {
            $table->integerIncrements("id_file_kerusakan");
            $table->string("nama_file_kerusakan");
            $table->unsignedInteger("fk_id_kerusakan");
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_kerusakan')
                  ->references('id_kerusakan')
                  ->on('kerusakan_alat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files_kerusakan');
    }
};
