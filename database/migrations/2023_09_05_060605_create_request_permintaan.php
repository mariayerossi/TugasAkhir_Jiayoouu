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
        Schema::create('request_permintaan', function (Blueprint $table) {
            $table->integerIncrements('id_permintaan');
            $table->integer('req_harga_sewa');
            $table->integer('req_id_alat');
            $table->integer('fk_id_tempat');
            $table->integer('fk_id_pemilik');
            $table->string('status_permintaan', null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_permintaan');
    }
};
