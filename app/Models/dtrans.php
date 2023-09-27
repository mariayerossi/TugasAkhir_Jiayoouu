<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dtrans extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="dtrans";//diganti dengan nama table di database
    protected $primaryKey ="id_dtrans";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    // protected $dates = ['deleted_at'];
    //---------------------------------

    public function get_all_data_by_id_htrans($id){
        return dtrans::where('deleted_at',"=",null)->where("fk_id_htrans", "=", $id)->get();
    }

    public function get_all_data_by_pemilik($id){
        return dtrans::where('deleted_at',"=",null)->where("fk_id_pemilik", "=", $id)->get();
    }
}
