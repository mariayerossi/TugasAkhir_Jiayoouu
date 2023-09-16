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

    public function get_all_data_by_id_htrans($id, $jenis){
        return komplainRequest::where('deleted_at',"=",null)->where("fk_id_request","=",$id)->where("jenis_request","=",$jenis)->get();
    }
}
