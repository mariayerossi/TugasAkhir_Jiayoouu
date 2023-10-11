<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kerusakanAlat extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="kerusakan_alat";//diganti dengan nama table di database
    protected $primaryKey ="id_kerusakan";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertKerusakanAlat($data)
    {
        $ker = new kerusakanAlat();
        $ker->fk_id_dtrans = $data["id_dtrans"];
        $ker->kesengajaan = $data["unsur"];
        $ker->nama_file = $data["foto"];
        $ker->save();
    }
}
