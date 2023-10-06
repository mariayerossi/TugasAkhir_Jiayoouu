<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class komplainTrans extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="komplain_trans";//diganti dengan nama table di database
    protected $primaryKey ="id_komplain_trans";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertKomplainTrans($data)
    {
        $komp = new komplainTrans();
        $komp->jenis_komplain = $data["jenis"];
        $komp->keterangan_komplain = $data["keterangan"];
        $komp->fk_id_htrans = $data["id_htrans"];
        $komp->waktu_komplain = $data["waktu"];
        $komp->status_komplain = "Menunggu";
        $komp->fk_id_user = $data["user"];
        $komp->fk_id_pemilik = $data["pemilik"];
        $komp->fk_id_tempat = $data["tempat"];
        $komp->save();

        return $komp->id_komplain_trans;
    }
}
