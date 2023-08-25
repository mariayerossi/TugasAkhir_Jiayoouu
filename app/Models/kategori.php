<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kategori extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="kategori";//diganti dengan nama table di database
    protected $primaryKey ="id_kategori";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertKategori($data)
    {
        $kategori = new kategori();
        $kategori->nama_kategori = $data["nama"];
        $kategori->save();
    }
}
