<?php

namespace App\Http\Controllers;

use App\Models\kerusakanAlat as ModelsKerusakanAlat;
use Illuminate\Http\Request;

class KerusakanAlat extends Controller
{
    public function ajukanKerusakan(Request $request) {
        // $request->validate([
        //     'rusak' => 'required',
        //     'unsur' => 'required',
        //     'foto' => 'required|max:5120'
        // ],[
        //     "rusak.required" => "input alat olahraga tidak boleh kosong!",
        //     "unsur.required" => "input unsur kesengajaan tidak boleh kosong!",
        //     "foto.max" => "ukuran foto bukti tidak boleh melebihi 5 MB!",
        //     "foto.required" => "input foto tidak boleh kosong!"
        // ]);

        $data = $request->all();

        //form utama
        if (isset($data['rusak'])) {
            $value = $data['rusak'];
            dd($value);
        }

        //form tambahan
        $isi = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'rusak-') === 0) {
                array_push($isi,[
                    "alat" => $value
                ]);
            }
    
            // Anda dapat menambahkan logika serupa untuk input lain yang Anda miliki
        }
        dd($isi);
    }
}
