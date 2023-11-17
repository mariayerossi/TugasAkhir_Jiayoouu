<?php

namespace App\Http\Controllers;

use App\Models\ratingAlat;
use App\Models\ratingLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Rating extends Controller
{
    public function tambahRatingLapangan(Request $request) {
        $request->validate([
            "rating" => "required"
        ],[
            "required" => "Rating tidak boleh kosong!"
        ]);

        $data = [
            "rating" => $request->rating,
            "review" => $request->review,
            "id_user" => Session::get("dataRole")->id_user,
            "id_lapangan" => $request->id_lapangan,
            "id_htrans" => $request->id_htrans
        ];
        $rate = new ratingLapangan();
        $rate->insertRating($data);

        return response()->json(['success' => true, 'message' => 'Berhasil Menambah Rating!']);
    }

    public function tambahRatingAlat(Request $request) {
        $request->validate([
            "rating" => "required"
        ],[
            "required" => "Rating tidak boleh kosong!"
        ]);

        $data = [
            "rating" => $request->rating,
            "review" => $request->review,
            "id_user" => Session::get("dataRole")->id_user,
            "id_alat" => $request->id_alat,
            "id_dtrans" => $request->id_dtrans
        ];
        $rate = new ratingAlat();
        $rate->insertRating($data);

        return response()->json(['success' => true, 'message' => 'Berhasil Menambah Rating!']);
    }
}
