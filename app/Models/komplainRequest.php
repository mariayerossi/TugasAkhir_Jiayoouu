<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class komplainRequest extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="komplain_request";//diganti dengan nama table di database
    protected $primaryKey ="id_komplain_req";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertKomplainReq($data)
    {
        $komp = new komplainRequest();
        $komp->jenis_komplain = $data["jenis"];
        $komp->keterangan_komplain = $data["keterangan"];
        $komp->fk_id_permintaan = $data["permintaan"];
        $komp->fk_id_penawaran = $data["penawaran"];
        $komp->waktu_komplain = $data["waktu"];
        $komp->status_komplain = "Menunggu";
        $komp->fk_id_pemilik = $data["pemilik"];
        $komp->fk_id_tempat = $data["tempat"];
        $komp->save();

        return $komp->id_komplain_req;
    }

    public function get_all_data_by_id_permintaan($id){
        return komplainRequest::where('deleted_at',"=",null)->where("fk_id_permintaan","=",$id)->get();
    }

    public function get_all_data_by_id_penawaran($id){
        return komplainRequest::where('deleted_at',"=",null)->where("fk_id_penawaran","=",$id)->get();
    }

    public function get_all_data_by_id_req_tempat_permintaan($id, $role){
        return komplainRequest::where('deleted_at',"=",null)->where("fk_id_permintaan","=",$id)->where("fk_id_tempat","=",$role)->get();
    }

    public function get_all_data_by_id_req_tempat_penawaran($id, $role){
        return komplainRequest::where('deleted_at',"=",null)->where("fk_id_penawaran","=",$id)->where("fk_id_tempat","=",$role)->get();
    }

    public function get_all_data_by_id_req_pemilik_permintaan($id, $role){
        return komplainRequest::where('deleted_at',"=",null)->where("fk_id_permintaan","=",$id)->where("fk_id_pemilik","=",$role)->get();
    }

    public function get_all_data_by_id_req_pemilik_penawaran($id, $role){
        return komplainRequest::where('deleted_at',"=",null)->where("fk_id_penawaran","=",$id)->where("fk_id_pemilik","=",$role)->get();
    }

    public function get_all_data_by_admin_baru(){
        return komplainRequest::where('deleted_at',"=",null)->where("status_komplain","=","Menunggu")->orderBy("waktu_komplain","desc")->get();
    }

    public function get_all_data_by_admin_diterima(){
        return komplainRequest::where('deleted_at',"=",null)->where("status_komplain","=","Diterima")->orderBy("waktu_komplain","desc")->get();
    }

    public function get_all_data_by_admin_ditolak(){
        return komplainRequest::where('deleted_at',"=",null)->where("status_komplain","=","Ditolak")->orderBy("waktu_komplain","desc")->get();
    }

    public function get_all_data_by_id($id){
        return komplainRequest::where('deleted_at',"=",null)->where("id_komplain_req","=",$id)->get();
    }

    public function count_all_data_admin() {
        return komplainRequest::where('deleted_at',"=",null)->where("status_komplain","=","Menunggu")->count();
    }

    public function updateStatus($data)
    {
        $komp = komplainRequest::find($data["id"]);
        $komp->status_komplain = $data["status"];
        $komp->save();
    }

    public function updatePenanganan($data)
    {
        $komp = komplainRequest::find($data["id"]);
        $komp->penanganan_komplain = $data["penanganan"];
        $komp->save();
    }

    public function updateAlasan($data)
    {
        $komp = komplainRequest::find($data["id"]);
        $komp->alasan_komplain = $data["alasan"];
        $komp->save();
    }
}
