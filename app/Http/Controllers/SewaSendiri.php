<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SewaSendiri extends Controller
{
    public function tambahSewaSendiri(Request $request){
        $request->validate([
            "alat" => "required"
        ],[
            "required" => "alat olahraga tidak boleh kosong!"
        ]);

        $alat = new alatOlahraga();
        $dataAlat = $alat->get_all_data_by_id($request->alat)->first();
        if ($dataAlat->status_alat == "Aktif") {
            $data = [
                "lapangan" => $request->id_lapangan,
                "alat" => $request->alat,
                ""
            ];
        }
    }
}
