<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sewaSendiri extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="sewa_sendiri";//diganti dengan nama table di database
    protected $primaryKey ="id_sewa";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertSewa($data)
    {
        $sewa = new sewaSendiri();
        $sewa->req_lapangan = $data["lapangan"];
        $sewa->req_id_alat = $data["alat"];
        $sewa->fk_id_tempat = $data["tempat"];
        $sewa->save();
    }

    public function deleteSewa($data)
    {
        $sewa = sewaSendiri::find($data["id"]);
        $sewa->deleted_at = $data["delete"];
        $sewa->save();
    }

    public function get_all_data_by_lapangan($id){
        return sewaSendiri::where('deleted_at',"=",null)->where("req_lapangan", "=", $id)->get();
    }
}
