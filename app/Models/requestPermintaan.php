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
        $req->req_lapangan = $data["lapangan"];
        $req->req_tanggal_mulai= $data["mulai"];
        $req->req_tanggal_selesai = $data["selesai"];
        $req->req_id_alat = $data["id_alat"];
        $req->fk_id_tempat = $data["id_tempat"];
        $req->fk_id_pemilik = $data["id_pemilik"];
        $req->tanggal_minta = $data["tgl_minta"];
        $req->status_permintaan = $data["status"];
        $req->save();
    }

    public function get_all_data_by_id($id){
        return requestPermintaan::where('deleted_at',"=",null)->where("id_permintaan", "=", $id)->get();
    }

    public function get_all_data_by_pemilik_baru($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->where("status_permintaan","=", "Menunggu")->get();
    }

    public function get_all_data_by_pemilik_diterima($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->where("status_permintaan","=", "Diterima")->get();
    }

    public function get_all_data_by_pemilik_ditolak($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->where("status_permintaan","=", "Ditolak")->get();
    }

    public function get_all_data_by_pemilik_selesai($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->where("status_permintaan","=", "Selesai")->get();
    }

    public function get_all_data_by_pemilik_dibatalkan($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->where("status_permintaan","=", "Dibatalkan")->get();
    }

    public function get_all_data_by_pemilik_dikomplain($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->where("status_permintaan","=", "Dikomplain")->get();
    }

    public function get_all_data_by_pemilik_disewakan($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $role)->where("status_permintaan","=", "Disewakan")->get();
    }

    public function get_all_data_by_tempat_baru($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_permintaan","=","Menunggu")->get();
    }

    public function get_all_data_by_tempat_diterima($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_permintaan","=", "Diterima")->get();
    }

    public function get_all_data_by_tempat_ditolak($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_permintaan","=", "Ditolak")->get();
    }

    public function get_all_data_by_tempat_selesai($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_permintaan","=", "Selesai")->get();
    }

    public function get_all_data_by_tempat_dibatalkan($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_permintaan","=", "Dibatalkan")->get();
    }

    public function get_all_data_by_tempat_disewakan($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_permintaan","=", "Disewakan")->get();
    }

    public function get_all_data_by_tempat_dikomplain($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_permintaan","=", "Dikomplain")->get();
    }

    public function count_all_data_pemilik($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_pemilik","=",$role)->where("status_permintaan","!=","Dibatalkan")->where("status_permintaan","!=","Ditolak")->count();
    }

    public function count_all_data_tempat($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("fk_id_tempat","=",$role)->count();
    }

    public function get_all_data() {
        return requestPermintaan::where('deleted_at',"=",null)->get();
    }

    public function updateStatus($data)
    {
        $per = requestPermintaan::find($data["id"]);
        $per->status_permintaan = $data["status"];
        $per->save();
    }

    public function updateStatusAlat($data)
    {
        $per = requestPermintaan::find($data["id"]);
        $per->status_alat = $data["status"];
        $per->save();
    }

    public function updateKodeMulai($data)
    {
        $per = requestPermintaan::find($data["id"]);
        $per->kode_mulai = $data["kode"];
        $per->save();
    }

    public function updateKodeSelesai($data)
    {
        $per = requestPermintaan::find($data["id"]);
        $per->kode_selesai = $data["kode"];
        $per->save();
    }

    public function updateHargaSewa($data)
    {
        $per = requestPermintaan::find($data["id"]);
        $per->req_harga_sewa = $data["harga"];
        $per->save();
    }

    public function get_all_data_by_lapangan($role){
        return requestPermintaan::where('deleted_at',"=",null)->where("req_lapangan", "=", $role)->where("status_permintaan","=", "Disewakan")->get();
    }
}
