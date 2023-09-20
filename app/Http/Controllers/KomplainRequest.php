<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\filesKomplainReq;
use App\Models\komplainRequest as ModelsKomplainRequest;
use App\Models\lapanganOlahraga;
use App\Models\requestPenawaran;
use App\Models\requestPermintaan;
use Illuminate\Http\Request;

class KomplainRequest extends Controller
{
    public function tambahKomplain(Request $request){
        $request->validate([
            "jenis" => "required",
            "keterangan" => "required",
            "foto" => 'required|max:5120'
        ],[
            "foto.max" => "ukuran foto alat olahraga tidak boleh melebihi 5MB!",
            "required" => ":attribute komplain tidak boleh kosong!",
            "foto.required" => "foto bukti komplain tidak boleh kosong atau minimal lampirkan 1 foto bukti!"
        ]);

        date_default_timezone_set("Asia/Jakarta");
        $tgl_komplain = date("Y-m-d H:i:s");

        $data = [
            "jenis" => $request->jenis,
            "keterangan" => $request->keterangan,
            "id_req" => $request->fk_id_request,
            "req" => $request->jenis_request,
            "waktu" => $tgl_komplain,
            "user" => $request->id_user,
            "role" => $request->role_user
        ];
        $komp = new ModelsKomplainRequest();
        $id = $komp->insertKomplainReq($data);

        //insert foto alatnya
        $destinasi = "/upload";
        foreach ($request->foto as $key => $value) {
            $foto = uniqid().".".$value->getClientOriginalExtension();
            $value->move(public_path($destinasi),$foto);
            $data2 = [
                "nama"=>$foto,
                "fk"=>$id
            ];
            $file = new filesKomplainReq();
            $file->insertFilesKomplainReq($data2);
        }

        //mengubah status request menjadi "Dikomplain"
        if ($request->jenis_request == "Permintaan") {
            $data3 = [
                "id" => $request->fk_id_request,
                "status" => "Dikomplain"
            ];
            $per = new requestPermintaan();
            $per->updateStatus($data3);
        }
        else if ($request->jenis_request == "Penawaran") {
            $data3 = [
                "id" => $request->fk_id_request,
                "status" => "Dikomplain"
            ];
            $pen = new requestPenawaran();
            $pen->updateStatus($data3);
        }

        return redirect()->back()->with("success", "Berhasil Mengajukan Komplain!");
    }

    public function penangananKomplain(Request $request) {
        // Pengecekan checkbox pertama
        if ($request->has('pengembalianCheckbox')) {
            if ($request->produk != "") {
                $array = explode("-", $request->produk);

                date_default_timezone_set("Asia/Jakarta");
                $tanggal = date("Y-m-d H:i:s");
                
                $data = [
                    "id" => $array[0],
                    "tanggal" => $tanggal
                ];

                if ($array[1] == "alat") {
                    $alat = new alatOlahraga();
                    $alat->softDelete($data);
                }
                else if ($array[1] == "lapangan") {
                    $lapangan = new lapanganOlahraga();
                    $lapangan->softDelete($data);
                }
            }
            else {
                return redirect()->back()->with("error", "produk yang akan dihapus tidak boleh kosong!");
            }
        }

        // Pengecekan checkbox kedua
        if ($request->has('pengembalianCheckbox2')) {
            if ($request->akun != "") {
                $array = explode("-", $request->akun);

                if ($array[1] == "tempat") {
                    
                }
                else if ($array[1] == "pemilik") {

                }
            }
            else {
                return redirect()->back()->with("error", "akun yang akan dinonaktifkan tidak boleh kosong!");
            }
        }

        //pembatalan request otomatis
    }
}
