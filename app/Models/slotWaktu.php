<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class slotWaktu extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="slot_waktu";//diganti dengan nama table di database
    protected $primaryKey ="id_slot";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertSlot($data)
    {
        $slot = new slotWaktu();
        $slot->hari = $data["hari"];
        $slot->jam_buka = $data["buka"];
        $slot->jam_tutup = $data["tutup"];
        $slot->fk_id_lapangan = $data["lapangan"];
        $slot->save();
    }

    public function get_all_data_by_lapangan($id){
        return slotWaktu::where('deleted_at',"=",null)->where("fk_id_lapangan", "=", $id)->orderByRaw("FIELD(hari, 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")->orderBy('jam_buka', 'asc')->get();
    }

    public function updateSlot($data)
    {
        $slot = slotWaktu::find($data["id"]);
        $slot->hari = $data["hari"];
        $slot->jam_buka = $data["buka"];
        $slot->jam_tutup = $data["tutup"];
        $slot->save();
    }
}
