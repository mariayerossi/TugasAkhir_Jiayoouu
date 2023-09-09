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
        Schema::create('request_penawaran', function (Blueprint $table) {
            $table->integerIncrements('id_penawaran');
            $table->integer('req_harga_sewa')->nullable();
            $table->integer('req_durasi');
            $table->integer('req_lapangan');
            $table->date('req_tanggal_mulai')->nullable();//diisi klo request disetujui
            $table->date('req_tanggal_selesai')->nullable();//diisi klo request disetujui
            $table->integer('req_id_alat');
            $table->integer('fk_id_tempat');
            $table->integer('fk_id_pemilik');
            $table->timestamp('tanggal_tawar');
            $table->string('status_penawaran');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_penawaran');
    }
};
