<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class komplainRequest extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="komplain_request";//diganti dengan nama table di database
    protected $primaryKey ="id_komplain_request";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertKomplainReq($data)
    {
        $komp = new komplainRequest();
        $komp->jenis_komplain = $data["jenis"];
        $komp->keterangan_komplain = $data["keterangan"];
        $komp->fk_id_request = $data["id_req"];
        $komp->jenis_request = $data["req"];
        $komp->waktu_komplain = $data["waktu"];
        $komp->status_komplain = "Menunggu";
        $komp->save();

        return $komp->id_komplain_req;
    }

    public function get_all_data_by_id_htrans($id, $jenis){
        return komplainRequest::where('deleted_at',"=",null)->where("fk_id_request","=",$id)->where("jenis_request","=",$jenis)->get();
    }
}
