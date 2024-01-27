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
        Schema::create('rating_alat', function (Blueprint $table) {
            $table->integerIncrements("id_rating_alat");
            $table->integer("rating");//1 - 5
            $table->string("review", 500)->nullable();//opsional
            $table->string("hide");//Ya / Tidak
            $table->unsignedInteger("fk_id_user");
            $table->unsignedInteger("fk_id_alat");
            $table->unsignedInteger("fk_id_dtrans");
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_user')
                  ->references('id_user')
                  ->on('user');
            $table->foreign('fk_id_alat')
                  ->references('id_alat')
                  ->on('alat_olahraga');
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
        Schema::dropIfExists('rating_alat');
    }
};
