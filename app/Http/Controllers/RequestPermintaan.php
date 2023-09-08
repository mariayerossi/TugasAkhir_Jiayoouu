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

        $harga = intval(str_replace(".", "", $request->harga));

        date_default_timezone_set("Asia/Jakarta");
        $tgl_minta = date("Y-m-d H:i:s");

        $data = [
            "harga" => $harga,
            "durasi" => $request->durasi,
            "lapangan" => $array[0],
            "id_alat" => $request->id_alat,
            "id_tempat" => $request->id_tempat,
            "id_pemilik" => $request->id_pemilik,
            "tgl_minta" => $tgl_minta,
            "status" => "Menunggu"
        ];
        $per = new ModelsRequestPermintaan();
        $per->insertPermintaan($data);

        return redirect()->back()->with("success", "Berhasil Mengirim Request!");
    }

    public function batalPermintaan(Request $request){
        $req = new ModelsRequestPermintaan();
        $status = $req->get_all_data_by_id($request->id)->first()->status_permintaan;

        if ($status == "Menunggu") {
            $data = [
                "id" => $request->id,
                "status" => "Dibatalkan"
            ];
            $per = new ModelsRequestPermintaan();
            $per->updateStatus($data);
    
            return redirect("/tempat/permintaan/daftarPermintaan");
        }
        else {
            return redirect()->back()->with("error", "Gagal membatalkan permintaan! status alat sudah $status");
        }
    }

    public function editHargaSewa(Request $request){
        $request->validate([
            "harga_sewa"=>"required"
        ],[
            "required"=> "Harga sewa alat olahraga tidak boleh kosong!"
        ]);
        $req = new ModelsRequestPermintaan();
        $status = $req->get_all_data_by_id($request->id)->first()->status_permintaan;

        if ($status == "Menunggu") {
            $data = [
                "id" => $request->id,
                "harga" => $request->harga_sewa
            ];
            $per = new ModelsRequestPermintaan();
            $per->updateHargaSewa($data);
    
            return redirect()->back()->with("success", "Berhasil mengubah harga sewa!");
        }
        else {
            return redirect()->back()->with("error", "Gagal mengedit harga sewa! status alat sudah $status");
        }
    }
}
