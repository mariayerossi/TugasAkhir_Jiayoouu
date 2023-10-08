<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class extendDtrans extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="extend_dtrans";//diganti dengan nama table di database
    protected $primaryKey ="id_extend_dtrans";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    // protected $dates = ['deleted_at'];
    //---------------------------------

    public function insertExtendDtrans($data){
        $extend = new extendDtrans();
        $extend->fk_id_extend_htrans = $data["id_extend_htrans"];
        $extend->fk_id_dtrans = $data["id_dtrans"];
        $extend->harga_sewa_alat = $data["harga"];
        $extend->subtotal_alat = $data["subtotal"];
        $extend->total_komisi_pemilik = $data["total_pemilik"];
        $extend->total_komisi_tempat = $data["total_tempat"];
        $extend->pendapatan_website_alat = $data["pendapatan"];
        $extend->save();
    }
}
