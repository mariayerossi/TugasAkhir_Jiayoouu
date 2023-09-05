<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class requestPermintaan extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="request_permintaan";//diganti dengan nama table di database
    protected $primaryKey ="id_permintaan";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------
}
