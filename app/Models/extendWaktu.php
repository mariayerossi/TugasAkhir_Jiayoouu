<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class extendWaktu extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="extend_waktu";//diganti dengan nama table di database
    protected $primaryKey ="id_extend";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    // protected $dates = ['deleted_at'];
    //---------------------------------

    public function insertExtend($data){
        $extend = new extendWaktu();
        $extend->jam_sewa = $data["jam"];
        $extend->durasi_extend = $data["durasi"];
        $extend->fk_id_htrans = $data["id_htrans"];
        $extend->subtotal_lapangan = $data["lapangan"];
        $extend->subtotal_alat = $data["alat"];
        $extend->total = $data["total"];
        $extend->status_extend = "Menunggu";
        $extend->save();
    }
}
