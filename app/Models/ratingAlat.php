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
        $rate->fk_id_user = $data["id_user"];
        $rate->fk_id_alat = $data["id_alat"];
        $rate->fk_id_dtrans = $data["id_dtrans"];
        $rate->save();
    }
}
