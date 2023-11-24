<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class kategori extends Model
{
    use HasFactory;
    // use SoftDeletes;
    //-------- !HARUS ADA! ------------
    protected $table ="kategori";//diganti dengan nama table di database
    protected $primaryKey ="id_kategori";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    // protected $dates = ['deleted_at'];
    //---------------------------------

    public function insertKategori($data)
    {
        $kategori = new kategori();
        $kategori->nama_kategori = $data["nama"];
        $kategori->save();
    }

    public function get_all_data(){
        return kategori::where('deleted_at',"=",null)->get();
    }

    public function deleteKategori($data)
    {
        $kat = kategori::find($data["id"]);
        $kat->delete();
    }

    public function updateKategori($data){
        $kat = kategori::find($data["id"]);
        $kat->nama_kategori = $data["nama"];
        $kat->save();
    }
}
