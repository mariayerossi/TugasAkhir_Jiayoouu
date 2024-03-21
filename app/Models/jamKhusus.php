<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jamKhusus extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="jam_khusus";//diganti dengan nama table di database
    protected $primaryKey ="id_jam";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertJam($data)
    {
        $jam = new jamKhusus();
        $jam->tanggal = $data["tanggal"];
        $jam->jam_mulai = $data["mulai"];
        $jam->jam_selesai = $data["selesai"];
        $jam->fk_id_lapangan = $data["lapangan"];
        $jam->save();
    }

    public function get_all_data_by_lapangan($id){
        return jamKhusus::where('deleted_at',"=",null)->where("fk_id_lapangan", "=", $id)->orderBy("tanggal","asc")->get();
    }

    public function get_all_data(){
        return jamKhusus::where('deleted_at',"=",null)->get();
    }

    public function updateJam($data)
    {
        $jam = jamKhusus::find($data["id"]);
        $jam->tanggal = $data["tanggal"];
        $jam->jam_mulai = $data["mulai"];
        $jam->jam_selesai = $data["selesai"];
        $jam->save();
    }

    public function deleteJam($data)
    {
        $jam = jamKhusus::find($data["id"]);
        $jam->deleted_at = $data["delete"];
        $jam->save();
    }
}
