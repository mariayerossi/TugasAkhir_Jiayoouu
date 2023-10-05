<?php

namespace App\Http\Controllers;

use App\Models\ratingReviewLapangan as ModelsRatingReviewLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RatingReviewLapangan extends Controller
{
    public function tambahRating(Request $request) {
        $request->validate([
            "rating" => "required"
        ],[
            "required" => "Rating tidak boleh kosong!"
        ]);

        $data = [
            "rating" => $request->rating,
            "review" => $request->review,
            "id_user" => Session::get("dataRole")->id_user,
            "id_lapangan" => $request->id_lapangan
        ];
        $rate = new ModelsRatingReviewLapangan();
        $rate->insertRating($data);

        return response()->json(['success' => true, 'message' => 'Berhasil Mengirim Rating!']);
    }
}
