<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pihakTempat extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="pihak_tempat";//diganti dengan nama table di database
    protected $primaryKey ="id_tempat";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function cek_email_tempat($isi)
    {
        return pihakTempat::where('deleted_at',"=",null)->where('email_tempat',"=", $isi)->get();
    }

    public function insertTempat($data)
    {
        $tempat = new pihakTempat();
        $tempat->nama_tempat = $data["nama"];
        $tempat->nama_pemilik_tempat = $data["pemilik"];
        $tempat->email_tempat = $data["email"];
        $tempat->telepon_tempat = $data["telepon"];
        $tempat->alamat_tempat = $data["alamat"];
        $tempat->ktp_tempat = $data["ktp"];
        $tempat->npwp_tempat = $data["npwp"];
        $tempat->password_tempat = $data["password"];
        $tempat->saldo_tempat = $data["saldo"];
        $tempat->email_verified_at = $data["veri"];
        $tempat->save();
    }

    public function get_all_data(){
        return pihakTempat::where('deleted_at',"=",null)->get();
    }

    public function get_all_data_by_id($id){
        return pihakTempat::where('deleted_at',"=",null)->where('id_tempat',"=", $id)->get();
    }

    public function softDelete($data){
        $temp = pihakTempat::find($data["id"]);
        $temp->deleted_at = $data["tanggal"];
        $temp->save();
    }

    public function updateSaldo($data){
        $temp = pihakTempat::find($data["id"]);
        $temp->saldo_tempat = $data["saldo"];
        $temp->save();
    }
}
