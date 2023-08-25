<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pihakTempat extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="pihak_tempat";//diganti dengan nama table di database
    protected $primaryKey ="id_tempat";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function cek_email_tempat($isi)
    {
        return pihakTempat::where('email_tempat',"=", $isi)->get();
    }
}
