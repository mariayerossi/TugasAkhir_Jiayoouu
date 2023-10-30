<?php

namespace App\Models;

use Carbon\Carbon;
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

    public static function generateCode()
    {
        $today = Carbon::now()->format('dmy');
        
        // Mengambil jumlah item yang sudah ada hari ini
        $count = self::where('kode_trans', 'LIKE', "H$today%")->count() + 1;
        
        return 'H' . $today . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function insertHtrans($data){
        $trans = new htrans();
        $trans->kode_trans = htrans::generateCode();
        $trans->fk_id_lapangan = $data["id_lapangan"];
        $trans->subtotal_lapangan = $data["subtotal_lapangan"];
        $trans->subtotal_alat = $data["subtotal_alat"];
        $trans->tanggal_trans = $data["tanggal_trans"];
        $trans->tanggal_sewa = $data["tanggal_sewa"];
        $trans->jam_sewa = $data["jam_sewa"];
        $trans->durasi_sewa = $data["durasi_sewa"];
        $trans->total_trans = $data["total"];
        $trans->fk_id_user = $data["id_user"];
        $trans->fk_id_tempat = $data["id_tempat"];
        $trans->pendapatan_website_lapangan = $data["pendapatan"];
        $trans->status_trans = "Menunggu";
        $trans->save();

        return $trans->id_htrans;
    }

    public function get_all_data(){
        return htrans::where('deleted_at',"=",null)->get();
    }

    public function get_all_data_by_id($id){
        return htrans::where('deleted_at',"=",null)->where("id_htrans", "=", $id)->get();
    }

    public function get_all_data_by_tempat($id){
        return htrans::where('deleted_at',"=",null)->where("fk_id_tempat","=",$id)->get();
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

    public function count_all_data_admin() {
        return htrans::where('deleted_at',"=",null)->count();
    }

    public function count_all_data_tempat($role){
        return htrans::where('deleted_at',"=",null)->where("fk_id_tempat","=",$role)->count();
    }

    public function updateStatus($data){
        $trans = htrans::find($data["id"]);
        $trans->status_trans = $data["status"];
        $trans->save();
    }

    public function updateSubtotalAlat($data){
        $trans = htrans::find($data["id"]);
        $trans->subtotal_alat = $data["subtotal"];
        $trans->save();
    }

    public function updateTotal($data){
        $trans = htrans::find($data["id"]);
        $trans->total_trans = $data["total"];
        $trans->save();
    }
}
