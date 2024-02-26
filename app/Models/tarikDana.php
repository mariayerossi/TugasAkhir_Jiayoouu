<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tarikDana extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="tarik_dana";//diganti dengan nama table di database
    protected $primaryKey ="id_tarik";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertTarik($data)
    {
        $tarik = new tarikDana();
        $tarik->fk_id_pemilik = $data["pemilik"];
        $tarik->fk_id_tempat = $data["tempat"];
        $tarik->total_tarik = $data["total"];
        $tarik->tanggal_tarik = $data["tanggal"];
        $tarik->status_tarik = "Menunggu";
        $tarik->save();
    }

    public function get_all_data(){
        return tarikDana::where("tarik_dana.status_tarik","=","Menunggu")
        ->leftJoin("pemilik_alat","tarik_dana.fk_id_pemilik","=","pemilik_alat.id_pemilik")
        ->leftJoin("pihak_tempat","tarik_dana.fk_id_tempat","=","pihak_tempat.id_tempat")
        ->get();
    }

    public function get_all_data_by_id($id){
        return tarikDana::where("id_tarik","=",$id)->get();
    }

    public function get_all_data_by_id_pemilik($id){
        return tarikDana::where("fk_id_pemilik","=",$id)->get();
    }

    public function get_all_data_by_id_tempat($id){
        return tarikDana::where("fk_id_tempat","=",$id)->get();
    }

    public function updateStatus($data)
    {
        $tarik = tarikDana::find($data["id"]);
        $tarik->status_tarik = $data["status"];
        $tarik->save();
    }
}
