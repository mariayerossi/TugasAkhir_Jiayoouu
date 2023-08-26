<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class filesAlatOlahraga extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="files_alat";//diganti dengan nama table di database
    protected $primaryKey ="id_file_alat";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertFilesAlat($data)
    {
        $file = new filesAlatOlahraga();
        $file->nama_file_alat = $data["nama"];
        $file->save();
    }
}
