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
        Schema::create('files_komplain_req', function (Blueprint $table) {
            $table->integerIncrements("id_file_komplain_req");
            $table->string("nama_file_komplain");
            $table->unsignedInteger("fk_id_komplain_req");
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fk_id_komplain_req')
                  ->references('id_komplain_req')
                  ->on('komplain_request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files_komplain_req');
    }
};
