<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifikasi extends Model
{
    use HasFactory;
    protected $table ="notifikasi";//diganti dengan nama table di database
    protected $primaryKey ="id_notifikasi";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertNotifikasi($data)
    {
        $notif = new notifikasi();
        $notif->keterangan_notifikasi = $data["keterangan"];
        $notif->waktu_notifikasi = $data["waktu"];
        $notif->link_notifikasi = $data["link"];
        $notif->fk_id_user = $data["user"];
        $notif->fk_id_pemilik = $data["pemilik"];
        $notif->fk_id_tempat = $data["tempat"];
        $notif->admin = $data["admin"];
        $notif->status_notifikasi = $data["status"];
        $notif->save();
    }

    public function updateStatus($data){
        $notif = notifikasi::find($data["id"]);
        $notif->status_notifikasi = $data["status"];
        $notif->save();
    }
}
