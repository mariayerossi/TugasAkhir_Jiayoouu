<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class filesLapanganOlahraga extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="files_lapangan";//diganti dengan nama table di database
    protected $primaryKey ="id_file_lapangan";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertFilesLapangan($data)
    {
        $file = new filesLapanganOlahraga();
        $file->nama_file_lapangan = $data["nama"];
        $file->fk_id_lapangan = $data["fk"];
        $file->save();
    }

    public function get_all_data($fk_id){
        return filesLapanganOlahraga::where('deleted_at',"=",null)->where("fk_id_lapangan","=",$fk_id)->get();
    }
}
