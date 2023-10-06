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
        Schema::create('files_komplain_trans', function (Blueprint $table) {
            $table->integerIncrements("id_file_komplain_trans");
            $table->string("nama_file_komplain");
            $table->unsignedInteger("fk_id_komplain_trans");
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_komplain_trans')
                  ->references('id_komplain_trans')
                  ->on('komplain_trans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files_komplain_trans');
    }
};
