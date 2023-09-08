<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class negosiasi extends Model
{
    use HasFactory;
    protected $table ="negosiasi";//diganti dengan nama table di database
    protected $primaryKey ="id_negosiasi";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertNegosiasi($data)
    {
        $nego = new negosiasi();
        $nego->isi_negosiasi = $data["isi"];
        $nego->waktu_negosiasi = $data["waktu"];
        $nego->fk_id_permintaan = $data["permintaan"];
        $nego->fk_id_user = $data["id_user"];
        $nego->role_user = $data["role"];
        $nego->save();
    }

    public function get_all_data_by_id_permintaan($id){
        return negosiasi::where('deleted_at',"=",null)->where("fk_id_permintaan", "=", $id)->get();
    }
}
