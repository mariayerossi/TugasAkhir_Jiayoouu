<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class htrans extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="htrans";//diganti dengan nama table di database
    protected $primaryKey ="id_htrans";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    // protected $dates = ['deleted_at'];
    //---------------------------------

    public function get_all_data_by_id($id){
        return htrans::where('deleted_at',"=",null)->where("id_htrans", "=", $id)->get();
    }

    public function get_all_data(){
        return htrans::where('deleted_at',"=",null)->where("status_trans", "!=", "Ditolak")->where("status_trans", "!=", "Dibatalkan")->where("status_trans", "!=", "Dikomplain")->get();
    }
    
    public function get_all_data_by_admin(){
        return htrans::where('deleted_at',"=",null)->get();
    }

    public function get_all_data_by_tempat_baru($role){
        return htrans::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_trans","=", "Menunggu")->get();
    }

    public function get_all_data_by_tempat_diterima($role){
        return htrans::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_trans","=", "Diterima")->get();
    }

    public function get_all_data_by_tempat_berlangsung($role){
        return htrans::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_trans","=", "Berlangsung")->get();
    }

    public function get_all_data_by_tempat_ditolak($role){
        return htrans::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_trans","=", "Ditolak")->get();
    }

    public function get_all_data_by_tempat_dibatalkan($role){
        return htrans::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_trans","=", "Dibatalkan")->get();
    }

    public function get_all_data_by_tempat_selesai($role){
        return htrans::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_trans","=", "Selesai")->get();
    }

    public function get_all_data_by_tempat_dikomplain($role){
        return htrans::where('deleted_at',"=",null)->where("fk_id_tempat", "=", $role)->where("status_trans","=", "Dikomplain")->get();
    }
}
