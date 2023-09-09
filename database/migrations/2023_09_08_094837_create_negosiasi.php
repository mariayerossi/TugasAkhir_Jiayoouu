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
        Schema::create('negosiasi', function (Blueprint $table) {
            $table->integerIncrements("id_negosiasi");
            $table->string("isi_negosiasi", 500);
            $table->timestamp("waktu_negosiasi");
            $table->integer("fk_id_request");
            $table->string("jenis_request");
            $table->integer("fk_id_user");
            $table->string("role_user");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negosiasi');
    }
};
