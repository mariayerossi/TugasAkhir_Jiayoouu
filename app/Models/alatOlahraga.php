<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class alatOlahraga extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="alat_olahraga";//diganti dengan nama table di database
    protected $primaryKey ="id_alat";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertAlat($data)
    {
        $alat = new alatOlahraga();
        $alat->nama_alat = $data["nama"];
        $alat->kategori_alat = $data["kategori"];
        $alat->deskripsi_alat = $data["deskripsi"];
        $alat->berat_alat = $data["berat"];
        $alat->ukuran_alat = $data["ukuran"];
        $alat->stok_alat = $data["stok"];
        $alat->komisi_alat = $data["komisi"];
        $alat->ganti_rugi_alat = $data["ganti"];
        $alat->status_alat = $data["status"];
        $alat->pemilik_alat = $data["pemilik"];
        $alat->save();
    }
}
