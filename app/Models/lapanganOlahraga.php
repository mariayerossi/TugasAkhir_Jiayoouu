<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lapanganOlahraga extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="lapangan_olahraga";//diganti dengan nama table di database
    protected $primaryKey ="id_lapangan";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertLapangan($data)
    {
        $lapa = new lapanganOlahraga();
        $lapa->nama_lapangan = $data["nama"];
        $lapa->kategori_lapangan = $data["kategori"];
        $lapa->tipe_lapangan = $data["tipe"];
        $lapa->lokasi_lapangan = $data["lokasi"];
        $lapa->kota_lapangan = $data["kota"];
        $lapa->deskripsi_lapangan = $data["deskripsi"];
        $lapa->luas_lapangan = $data["luas"];
        $lapa->harga_sewa_lapangan = $data["harga"];
        $lapa->status_lapangan = $data["status"];
        $lapa->pemilik_lapangan = $data["pemilik"];
        $lapa->save();

        return $lapa->id_lapangan;
    }

    public function get_all_data($role){
        return lapanganOlahraga::where('deleted_at',"=",null)->where("pemilik_lapangan","=",$role)->get();
    }

    public function get_all_data2(){
        return lapanganOlahraga::where('deleted_at',"=",null)->where("status_lapangan","=","Aktif")->get();
    }

    public function get_all_data_by_id($id){
        return lapanganOlahraga::where('deleted_at',"=",null)->where("id_lapangan","=",$id)->get();
    }

    public function count_all_data($role){
        return lapanganOlahraga::where('deleted_at',"=",null)->where("pemilik_lapangan","=",$role)->count();
    }

    public function updateLapangan($data){
        $lapa = lapanganOlahraga::find($data["id"]);
        $lapa->nama_lapangan = $data["nama"];
        $lapa->kategori_lapangan = $data["kategori"];
        $lapa->tipe_lapangan = $data["tipe"];
        $lapa->lokasi_lapangan = $data["lokasi"];
        $lapa->kota_lapangan = $data["kota"];
        $lapa->deskripsi_lapangan = $data["deskripsi"];
        $lapa->luas_lapangan = $data["luas"];
        $lapa->harga_sewa_lapangan = $data["harga"];
        $lapa->status_lapangan = $data["status"];
        $lapa->pemilik_lapangan = $data["pemilik"];
        $lapa->save();
    }
}
