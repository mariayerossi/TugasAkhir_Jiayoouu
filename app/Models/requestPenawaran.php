<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class requestPenawaran extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="request_penawaran";//diganti dengan nama table di database
    protected $primaryKey ="id_penawaran";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertPenawaran($data)
    {
        $req = new requestPenawaran();
        $req->req_lapangan = $data["lapangan"];
        $req->req_id_alat = $data["id_alat"];
        $req->fk_id_tempat = $data["id_tempat"];
        $req->fk_id_pemilik = $data["id_pemilik"];
        $req->tanggal_tawar = $data["tgl_tawar"];
        $req->status_penawaran = $data["status"];
        $req->save();
    }

    public function get_all_data_by_id($id){
        return requestPenawaran::where('deleted_at',"=",null)->where("id_penawaran", "=", $id)->get();
    }

    public function get_all_data_by_pemilik_baru($role){
        return requestPenawaran::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->where("status_penawaran","=", "Menunggu")->get();
    }

    public function get_all_data_by_pemilik_diterima($role){
        return requestPenawaran::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->where("status_penawaran","=", "Diterima")->get();
    }

    public function get_all_data_by_pemilik_ditolak($role){
        return requestPenawaran::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->where("status_penawaran","=", "Ditolak")->get();
    }

    public function get_all_data_by_pemilik_selesai($role){
        return requestPenawaran::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->where("status_penawaran","=", "Selesai")->get();
    }

    public function get_all_data_by_pemilik_dibatalkan($role){
        return requestPenawaran::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->where("status_penawaran","=", "Dibatalkan")->get();
    }

    public function get_all_data_by_tempat_baru($role){
        return requestPenawaran::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_penawaran","=", "Menunggu")->get();
    }

    public function get_all_data_by_tempat_diterima($role){
        return requestPenawaran::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_penawaran","=", "Diterima")->get();
    }

    public function get_all_data_by_tempat_ditolak($role){
        return requestPenawaran::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_penawaran","=", "Ditolak")->get();
    }

    public function get_all_data_by_tempat_selesai($role){
        return requestPenawaran::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_penawaran","=", "Selesai")->get();
    }

    public function updateStatus($data)
    {
        $pen = requestPenawaran::find($data["id"]);
        $pen->status_penawaran = $data["status"];
        $pen->save();
    }
}
