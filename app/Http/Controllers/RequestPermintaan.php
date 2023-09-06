<?php

namespace App\Http\Controllers;

use App\Models\requestPermintaan as ModelsRequestPermintaan;
use Illuminate\Http\Request;

class RequestPermintaan extends Controller
{
    public function ajukanPermintaan(Request $request){
        $request->validate([
            "harga" => "required",
            "jumlah" => "required"
        ],[
            "harga.required" => "harga sewa tidak boleh kosong!",
            "jumlah.required" => ucfirst(":attribute")." tidak boleh kosong!"
        ]);

        if ($request->jumlah > $request->stok) {
            return redirect()->back()->with("error", "jumlah tidak boleh lebih dari stok!");
        }
        else {
            $data = [
                "harga" => $request->harga,
                "jumlah" => $request->jumlah,
                "id_alat" => $request->id_alat,
                "id_tempat" => $request->id_tempat,
                "id_pemilik" => $request->id_pemilik
            ];
            $kat = new ModelsRequestPermintaan();
            $kat->insertPermintaan($data);
    
            return redirect()->back()->with("success", "Berhasil Melakukan Request!");
        }
    }
}
