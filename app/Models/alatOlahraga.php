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
        $alat->fk_id_kategori = $data["kategori"];
        $alat->kota_alat = $data["kota"];
        $alat->deskripsi_alat = $data["deskripsi"];
        $alat->berat_alat = $data["berat"];
        $alat->ukuran_alat = $data["ukuran"];
        $alat->komisi_alat = $data["komisi"];
        $alat->ganti_rugi_alat = $data["ganti"];
        $alat->kota_alat = $data["kota"];
        $alat->status_alat = $data["status"];
        $alat->fk_id_pemilik = $data["pemilik"];
        $alat->fk_id_tempat = $data["tempat"];
        $alat->save();

        return $alat->id_alat;
    }

    public function get_all_data($id){
        return alatOlahraga::where('deleted_at',"=",null)->where("fk_id_pemilik","=",$id)->get();
    }

    public function get_all_data2(){
        return alatOlahraga::where('deleted_at',"=",null)->where("fk_id_pemilik","!=",null)->where("status_alat","=","Aktif")->get();
    }

    public function get_all_data3($id){
        return alatOlahraga::where('deleted_at',"=",null)->where("fk_id_tempat","=",$id)->where("status_alat","=","Aktif")->get();
    }

    public function get_all_data_for_admin(){
        return alatOlahraga::where('deleted_at',"=",null)->get();
    }

    public function get_all_data_by_id($id){
        return alatOlahraga::where('deleted_at',"=",null)->where("id_alat","=",$id)->get();
    }

    public function count_all_data($id){
        return alatOlahraga::where('deleted_at',"=",null)->where("fk_id_pemilik","=",$id)->count();
    }

    public function count_all_data_admin(){
        return alatOlahraga::where('deleted_at',"=",null)->count();
    }

    public function get_all_data_status($id){
        return alatOlahraga::where('deleted_at',"=",null)->where("fk_id_pemilik","=",$id)->where("status_alat","=","Aktif")->get();
    }

    public function updateAlat($data){
        $alat = alatOlahraga::find($data["id"]);
        $alat->nama_alat = $data["nama"];
        $alat->fk_id_kategori = $data["kategori"];
        $alat->kota_alat = $data["kota"];
        $alat->deskripsi_alat = $data["deskripsi"];
        $alat->berat_alat = $data["berat"];
        $alat->ukuran_alat = $data["ukuran"];
        $alat->komisi_alat = $data["komisi"];
        $alat->ganti_rugi_alat = $data["ganti"];
        $alat->kota_alat = $data["kota"];
        $alat->status_alat = $data["status"];
        $alat->fk_id_pemilik = $data["pemilik"];
        $alat->fk_id_tempat = $data["tempat"];
        $alat->save();
    }

    public function updateStatus($data){
        $alat = alatOlahraga::find($data["id"]);
        $alat->status_alat = $data["status"];
        $alat->save();
    }

    public function updateKomisi($data){
        $alat = alatOlahraga::find($data["id"]);
        $alat->komisi_alat = $data["komisi"];
        $alat->save();
    }

    public function softDelete($data){
        $alat = alatOlahraga::find($data["id"]);
        $alat->deleted_at = $data["tanggal"];
        $alat->save();
    }
}
