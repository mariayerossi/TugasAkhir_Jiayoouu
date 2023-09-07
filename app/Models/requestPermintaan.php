<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class requestPermintaan extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="request_permintaan";//diganti dengan nama table di database
    protected $primaryKey ="id_permintaan";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertPermintaan($data)
    {
        $req = new requestPermintaan();
        $req->req_harga_sewa = $data["harga"];
        $req->req_durasi = $data["durasi"];
        $req->req_lapangan = $data["lapangan"];
        $req->req_id_alat = $data["id_alat"];
        $req->fk_id_tempat = $data["id_tempat"];
        $req->fk_id_pemilik = $data["id_pemilik"];
        $req->tanggal_minta = $data["tgl_minta"];
        $req->save();
    }

    public function get_all_data_by_pemilik($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->get();
    }

    public function get_all_data_by_tempat_baru($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_permintaan","=",null)->get();
    }

    public function get_all_data_by_tempat_diterima($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_permintaan","=", "Diterima")->get();
    }

    public function get_all_data_by_tempat_ditolak($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_permintaan","=", "Ditolak")->get();
    }

    public function count_all_data_pemilik($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_pemilik","=",$role)->count();
    }

    public function count_all_data_tempat($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_tempat","=",$role)->count();
    }
}
