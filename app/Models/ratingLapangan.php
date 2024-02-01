<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ratingLapangan extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="rating_lapangan";//diganti dengan nama table di database
    protected $primaryKey ="id_rating_lapangan";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertRating($data)
    {
        $rate = new ratingLapangan();
        $rate->rating = $data["rating"];
        $rate->review = $data["review"];
        $rate->hide = $data["hide"];
        $rate->fk_id_user = $data["id_user"];
        $rate->fk_id_lapangan = $data["id_lapangan"];
        $rate->fk_id_htrans = $data["id_htrans"];
        $rate->save();
    }

    public function get_data_by_id_lapangan($id) {
        return ratingLapangan::select("user.nama_user", "rating_lapangan.hide", "rating_lapangan.review", "rating_lapangan.rating","rating_lapangan.created_at")
        ->join("user", "rating_lapangan.fk_id_user","=","user.id_user")
        ->where("fk_id_lapangan","=",$id)
        ->orderBy("rating_lapangan.created_at","desc")
        ->get();
    }

    public function get_avg_data($id){
        return ratingLapangan::where('deleted_at',"=",null)->where('fk_id_lapangan',"=",$id)->avg('rating');
    }

    public function get_data_count($id){
        return ratingLapangan::where('deleted_at',"=",null)->where('fk_id_lapangan',"=",$id)->count();
    }
}
