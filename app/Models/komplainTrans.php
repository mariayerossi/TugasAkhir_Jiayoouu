<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class komplainTrans extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="komplain_trans";//diganti dengan nama table di database
    protected $primaryKey ="id_komplain_trans";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertKomplainTrans($data)
    {
        $komp = new komplainTrans();
        $komp->jenis_komplain = $data["jenis"];
        $komp->keterangan_komplain = $data["keterangan"];
        $komp->fk_id_htrans = $data["id_htrans"];
        $komp->waktu_komplain = $data["waktu"];
        $komp->status_komplain = "Menunggu";
        $komp->fk_id_user = $data["user"];
        $komp->save();

        return $komp->id_komplain_trans;
    }

    public function get_all_data_by_admin_baru(){
        return komplainTrans::where('deleted_at',"=",null)->where("status_komplain","=","Menunggu")->orderBy("waktu_komplain","desc")->get();
    }

    public function get_all_data_by_admin_diterima(){
        return komplainTrans::where('deleted_at',"=",null)->where("status_komplain","=","Diterima")->orderBy("waktu_komplain","desc")->get();
    }

    public function get_all_data_by_admin_ditolak(){
        return komplainTrans::where('deleted_at',"=",null)->where("status_komplain","=","Ditolak")->orderBy("waktu_komplain","desc")->get();
    }

    public function get_all_data_by_id_htrans($id){
        return komplainTrans::where('deleted_at',"=",null)->where("fk_id_htrans","=",$id)->get();
    }

    public function get_all_data_by_id($id){
        return komplainTrans::where('deleted_at',"=",null)->where("id_komplain_trans","=",$id)->get();
    }

    public function count_all_data_admin() {
        return komplainTrans::where('deleted_at',"=",null)->where("status_komplain","=","Menunggu")->count();
    }

    public function updateStatus($data)
    {
        $komp = komplainTrans::find($data["id"]);
        $komp->status_komplain = $data["status"];
        $komp->save();
    }

    public function updatePenanganan($data)
    {
        $komp = komplainTrans::find($data["id"]);
        $komp->penanganan_komplain = $data["penanganan"];
        $komp->save();
    }

    public function updateAlasan($data)
    {
        $komp = komplainTrans::find($data["id"]);
        $komp->alasan_komplain = $data["alasan"];
        $komp->save();
    }
}
