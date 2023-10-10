<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class filesKerusakan extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="files_kerusakan";//diganti dengan nama table di database
    protected $primaryKey ="id_file_kerusakan";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertFilesKerusakan($data)
    {
        $ker = new filesKerusakan();
        $ker->nama_file_kerusakan = $data["nama_file"];
        $ker->fk_id_kerusakan = $data["id_kerusakan"];
        $ker->save();
    }
}
