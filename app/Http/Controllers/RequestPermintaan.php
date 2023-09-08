<?php

namespace App\Http\Controllers;

use App\Models\requestPermintaan as ModelsRequestPermintaan;
use Illuminate\Http\Request;

class RequestPermintaan extends Controller
{
    public function ajukanPermintaan(Request $request){
        $request->validate([
            "harga" => "required",
            "durasi" => "required",
            "lapangan" => "required"
        ],[
            "harga.required" => "harga sewa tidak boleh kosong!",
            "durasi.required" => "durasi peminjaman tidak boleh kosong!",
            "lapangan.required" => "lapangan tidak boleh kosong!"
        ]);

        $array = explode("-", $request->lapangan);

        date_default_timezone_set("Asia/Jakarta");
        $tgl_minta = date("Y-m-d H:i:s");

        $data = [
            "harga" => $request->harga,
            "durasi" => $request->durasi,
            "lapangan" => $array[0],
            "id_alat" => $request->id_alat,
            "id_tempat" => $request->id_tempat,
            "id_pemilik" => $request->id_pemilik,
            "tgl_minta" => $tgl_minta,
            "status" => "Menunggu"
        ];
        $kat = new ModelsRequestPermintaan();
        $kat->insertPermintaan($data);

        return redirect()->back()->with("success", "Berhasil Mengirim Request!");
    }
}
