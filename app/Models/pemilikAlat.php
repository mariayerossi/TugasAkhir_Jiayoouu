<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pemilikAlat extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="pemilik_alat";//diganti dengan nama table di database
    protected $primaryKey ="id_pemilik";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function cek_email_pemilik($isi)
    {
        return pemilikAlat::where('email_pemilik',"=", $isi)->get();
    }

    public function insertPemilik($data)
    {
        $pemilik = new pemilikAlat();
        $pemilik->nama_pemilik = $data["nama"];
        $pemilik->email_pemilik = $data["email"];
        $pemilik->telepon_pemilik = $data["telepon"];
        $pemilik->ktp_pemilik = $data["ktp"];
        $pemilik->password_pemilik = $data["password"];
        $pemilik->saldo_pemilik = $data["saldo"];
        $pemilik->save();
    }
}
