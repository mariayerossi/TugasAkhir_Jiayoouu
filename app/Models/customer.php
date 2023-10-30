<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="user";//diganti dengan nama table di database
    protected $primaryKey ="id_user";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertUser($data)
    {
        $user = new customer();
        $user->nama_user = $data["nama"];
        $user->email_user = $data["email"];
        $user->telepon_user = $data["telepon"];
        $user->password_user = $data["password"];
        $user->saldo_user = $data["saldo"];
        $user->email_verified_at = null;
        $user->save();
    }

    public function cek_email_user($isi){
        return customer::where('deleted_at',"=",null)->where('email_user',"=", $isi)->get();
    }

    public function get_all_data_by_id($id){
        return customer::where('deleted_at',"=",null)->where('id_user',"=", $id)->get();
    }

    public function get_all_data(){
        return customer::where('deleted_at',"=",null)->get();
    }

    public function count_all_data_admin(){
        return customer::where('deleted_at',"=",null)->count();
    }

    public function updateSaldo($data){
        $user = customer::find($data["id"]);
        $user->saldo_user = $data["saldo"];
        $user->save();
    }

    public function verifikasiEmail($data){
        $user = customer::find($data["id"]);
        $user->email_verified_at = $data["tanggal"];
        $user->save();
    }
}
