<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class filesKomplainReq extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="files_komplain_req";//diganti dengan nama table di database
    protected $primaryKey ="id_file_komplain_req";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertFilesKomplainReq($data)
    {
        $file = new filesKomplainReq();
        $file->nama_file_komplain = $data["nama"];
        $file->fk_id_komplain_req = $data["fk"];
        $file->save();
    }

    public function get_all_data($fk_id){
        return filesKomplainReq::where('deleted_at',"=",null)->where("fk_id_komplain_req","=",$fk_id)->get();
    }
}
