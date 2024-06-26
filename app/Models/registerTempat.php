<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class registerTempat extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="register_tempat";//diganti dengan nama table di database
    protected $primaryKey ="id_register";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertRegister($data)
    {
        $reg = new registerTempat();
        $reg->nama_tempat_reg = $data["nama"];
        $reg->nama_pemilik_tempat_reg = $data["pemilik"];
        $reg->email_tempat_reg = $data["email"];
        $reg->telepon_tempat_reg = $data["telepon"];
        $reg->alamat_tempat_reg = $data["alamat"];
        $reg->kota_tempat_reg = $data["kota"];
        $reg->ktp_tempat_reg = $data["ktp"];
        $reg->npwp_tempat_reg = $data["npwp"];
        $reg->password_tempat_reg = $data["password"];
        $reg->saldo_tempat_reg = $data["saldo"];
        $reg->email_verified_at = null;
        $reg->save();

        return $reg->id_register;
    }

    public function get_all_data(){
        return registerTempat::where('deleted_at',"=",null)->where("email_verified_at","!=",null)->get();
    }

    public function deleteRegister($data)
    {
        $reg = registerTempat::find($data["id"]);
        $reg->delete();
    }

    public function cek_email_tempat($isi)
    {
        return registerTempat::where('deleted_at',"=",null)->where('email_tempat_reg',"=", $isi)->get();
    }

    public function verifikasiEmail($data){
        $reg = registerTempat::find($data["id"]);
        $reg->email_verified_at = $data["tanggal"];
        $reg->save();
    }
}
