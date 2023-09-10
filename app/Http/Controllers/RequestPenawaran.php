<?php

namespace App\Http\Controllers;

use App\Models\requestPenawaran as ModelsRequestPenawaran;
use Illuminate\Http\Request;

class RequestPenawaran extends Controller
{
    public function ajukanPenawaran(Request $request) {
        $request->validate([
            "alat" => "required"
        ],[
            "required" => "alat olahraga tidak boleh kosong!"
        ]);

        $array = explode("-", $request->alat);

        date_default_timezone_set("Asia/Jakarta");
        $tgl_tawar = date("Y-m-d H:i:s");
        
        $data = [
            "lapangan" => $request->id_lapangan,
            "id_alat" => $array[0],
            "id_tempat" => $request->id_tempat,
            "id_pemilik" => $request->id_pemilik,
            "tgl_tawar" => $tgl_tawar,
            "status" => "Menunggu"
        ];
        $req = new ModelsRequestPenawaran();
        $req->insertPenawaran($data);

        return redirect()->back()->with("success", "Berhasil Menawarkan Alat!");
    }

    public function batalPenawaran(Request $request) {
        $req = new ModelsRequestPenawaran();
        $status = $req->get_all_data_by_id($request->id_penawaran)->first()->status_penawaran;

        if ($status == "Menunggu") {
            $data = [
                "id" => $request->id_penawaran,
                "status" => "Dibatalkan"
            ];
            $per = new ModelsRequestPenawaran();
            $per->updateStatus($data);
    
            return redirect("/pemilik/penawaran/daftarPenawaran");
        }
        else {
            return redirect()->back()->with("error", "Gagal membatalkan penawaran! status alat sudah $status");
        }
    }

    public function terimaPenawaran(Request $request) {
        $req = new ModelsRequestPenawaran();
        $dataReq = $req->get_all_data_by_id($request->id_penawaran)->first();
        if ($dataReq->status_penawaran == "Menunggu") {
            //cek dulu apakah harga sewa dan durasi masih null atau tidak
            if ($dataReq->req_harga_sewa != null) {
                if ($dataReq->req_durasi != null) {
                    $data = [
                        "id" => $request->id_penawaran,
                        "status" => "Setuju"
                    ];
                    $per = new ModelsRequestPenawaran();
                    $per->updateStatusTempat($data);
            
                    return redirect()->back()->with("success", "Menunggu konfirmasi pemilik alat olahraga");
                }
                else {
                    return redirect()->back()->with("error", "Masukkan durasi sewa terlebih dahulu!");
                }
            }
            else {
                return redirect()->back()->with("error", "Masukkan harga sewa terlebih dahulu!");
            }
        }
        else {
            return redirect()->back()->with("error", "Gagal menerima penawaran! status alat sudah $dataReq->req_harga_sewa");
        }
    }

    public function tolakPenawaran(Request $request) {
        $req = new ModelsRequestPenawaran();
        $status = $req->get_all_data_by_id($request->id_penawaran)->first()->status_penawaran;

        if ($status == "Menunggu") {
            $data = [
                "id" => $request->id_penawaran,
                "status" => "Ditolak"
            ];
            $per = new ModelsRequestPenawaran();
            $per->updateStatus($data);
    
            return redirect("/tempat/penawaran/daftarPenawaran");
        }
        else {
            return redirect()->back()->with("error", "Gagal menolak penawaran! status alat sudah $status");
        }
    }
    
    public function editHargaSewa(Request $request) {
        $request->validate([
            "harga_sewa" => "required"
        ],[
            "required" => "harga sewa tidak boleh kosong!"
        ]);

        $data = [
            "id" => $request->id_penawaran,
            "harga" => $request->harga_sewa
        ];
        $pen = new ModelsRequestPenawaran();
        $pen->updateHargaSewa($data);

        return redirect()->back()->with("success", "Berhasil mengedit harga sewa!");
    }

    public function editDurasi(Request $request) {
        $request->validate([
            "durasi" => "required"
        ],[
            "required" => "durasi sewa tidak boleh kosong!"
        ]);

        $data = [
            "id" => $request->id_penawaran,
            "durasi" => $request->durasi
        ];
        $pen = new ModelsRequestPenawaran();
        $pen->updateDurasi($data);

        return redirect()->back()->with("success", "Berhasil mengedit durasi sewa!");
    }
}
