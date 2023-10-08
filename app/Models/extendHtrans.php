<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class extendHtrans extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="extend_htrans";//diganti dengan nama table di database
    protected $primaryKey ="id_extend_htrans";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    // protected $dates = ['deleted_at'];
    //---------------------------------

    public function insertExtendHtrans($data){
        $extend = new extendHtrans();
        $extend->fk_id_htrans = $data["id_htrans"];
        $extend->tanggal_extend = $data["tanggal"];
        $extend->jam_sewa = $data["jam"];
        $extend->durasi_extend = $data["durasi"];
        $extend->subtotal_lapangan = $data["lapangan"];
        $extend->subtotal_alat = $data["alat"];
        $extend->total = $data["total"];
        $extend->pendapatan_website_lapangan = $data["pendapatan"];
        $extend->status_extend = "Menunggu";
        $extend->save();

        return $extend->id_extend_htrans;
    }
}
