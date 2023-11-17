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
        Schema::create('rating_lapangan', function (Blueprint $table) {
            $table->integerIncrements("id_rating_lapangan");
            $table->integer("rating");//1 - 5
            $table->string("review", 500)->nullable();//opsional
            $table->unsignedInteger("fk_id_user");
            $table->unsignedInteger("fk_id_lapangan");
            $table->unsignedInteger("fk_id_htrans");
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_user')
                  ->references('id_user')
                  ->on('user');
            $table->foreign('fk_id_lapangan')
                  ->references('id_lapangan')
                  ->on('lapangan_olahraga');
            $table->foreign('fk_id_htrans')
                  ->references('id_htrans')
                  ->on('htrans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rating_lapangan');
    }
};
