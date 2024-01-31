<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ratingAlat extends Model
{
    use HasFactory;
    //-------- !HARUS ADA! ------------
    protected $table ="rating_alat";//diganti dengan nama table di database
    protected $primaryKey ="id_rating_alat";//diganti dengan nama primary key dari table
    public $timestamp = true; //klo true otomatis akan nambah field create_at dan update_at
    public $incrementing = true;//utk increment
    //---------------------------------

    public function insertRating($data)
    {
        $rate = new ratingAlat();
        $rate->rating = $data["rating"];
        $rate->review = $data["review"];
        $rate->hide = $data["hide"];
        $rate->fk_id_user = $data["id_user"];
        $rate->fk_id_alat = $data["id_alat"];
        $rate->fk_id_dtrans = $data["id_dtrans"];
        $rate->save();
    }

    public function get_data_by_id_alat($id) {
        return ratingAlat::select("user.nama_user", "rating_alat.hide", "rating_alat.review", "rating_alat.rating","rating_alat.created_at")
        ->join("user", "rating_alat.fk_id_user","=","user.id_user")
        ->where("fk_id_alat","=",$id)
        ->orderBy("rating_alat.created_at","desc")
        ->get();
    }

    public function get_avg_data($id){
        return ratingAlat::where('deleted_at',"=",null)->where('fk_id_alat',"=",$id)->avg('rating');
    }

    public function get_data_count($id){
        return ratingAlat::where('deleted_at',"=",null)->where('fk_id_alat',"=",$id)->count();
    }
}
