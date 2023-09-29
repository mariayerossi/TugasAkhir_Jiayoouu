<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\filesKomplainReq;
use App\Models\komplainRequest as ModelsKomplainRequest;
use App\Models\lapanganOlahraga;
use App\Models\pemilikAlat;
use App\Models\pihakTempat;
use App\Models\requestPenawaran;
use App\Models\requestPermintaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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

        if (Session::get("role") == "pemilik") {
            $pemilik = Session::get("dataRole")->id_pemilik;

            if ($request->jenis_request == "Permintaan") {
                $data = [
                    "jenis" => $request->jenis,
                    "keterangan" => $request->keterangan,
                    "permintaan" => $request->fk_id_request,
                    "penawaran" => null,
                    "waktu" => $tgl_komplain,
                    "pemilik" => $pemilik,
                    "tempat" => null
                ];
            }
            else {
                $data = [
                    "jenis" => $request->jenis,
                    "keterangan" => $request->keterangan,
                    "permintaan" => null,
                    "penawaran" => $request->fk_id_request,
                    "waktu" => $tgl_komplain,
                    "pemilik" => $pemilik,
                    "tempat" => null
                ];
            }
        }
        else {
            $pemilik = Session::get("dataRole")->id_tempat;

            if ($request->jenis_request == "Permintaan") {
                $data = [
                    "jenis" => $request->jenis,
                    "keterangan" => $request->keterangan,
                    "permintaan" => $request->fk_id_request,
                    "penawaran" => null,
                    "waktu" => $tgl_komplain,
                    "pemilik" => null,
                    "tempat" => $pemilik
                ];
            }
            else {
                $data = [
                    "jenis" => $request->jenis,
                    "keterangan" => $request->keterangan,
                    "permintaan" => null,
                    "penawaran" => $request->fk_id_request,
                    "waktu" => $tgl_komplain,
                    "pemilik" => null,
                    "tempat" => $pemilik
                ];
            }
        }
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

    public function terimaKomplain(Request $request) {
        // Pengecekan checkbox pertama
        $penanganan = "";
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

                    $penanganan = "Hapus Alat";
                }
                else if ($array[1] == "lapangan") {
                    $lapangan = new lapanganOlahraga();
                    $lapangan->softDelete($data);

                    $penanganan = "Hapus Lapangan";
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

                date_default_timezone_set("Asia/Jakarta");
                $tanggal = date("Y-m-d H:i:s");
                
                $data = [
                    "id" => $array[0],
                    "tanggal" => $tanggal
                ];

                if ($array[1] == "tempat") {
                    $temp = new pihakTempat();
                    $temp->softDelete($data);

                    $penanganan = "Hapus Tempat";
                }
                else if ($array[1] == "pemilik") {
                    $pemi = new pemilikAlat();
                    $pemi->softDelete($data);

                    $penanganan = "Hapus Pemilik";
                }
            }
            else {
                return redirect()->back()->with("error", "akun yang akan dinonaktifkan tidak boleh kosong!");
            }
        }

        $data2 = [
            "id" => $request->id_request,
            "status" => "Ditolak"
        ];

        //pembatalan request otomatis
        if ($request->fk_id_permintaan != null) {
            $per = new requestPermintaan();
            $per->updateStatus($data2);
        }
        else if ($request->fk_id_penawaran != null) {
            $pen = new requestPenawaran();
            $pen->updateStatus($data2);
        }

        //ganti status komplain menjadi "Diterima"
        $data3 = [
            "id" => $request->id_komplain,
            "status" => "Diterima"
        ];
        $komp = new ModelsKomplainRequest();
        $komp->updateStatus($data3);

        //isi penanganan_komplain di db
        $data4 = [
            "id" => $request->id_komplain,
            "penanganan" => $penanganan
        ];
        $penang = new ModelsKomplainRequest();
        $penang->updatePenanganan($data4);

        return redirect()->back()->with("success", "Berhasil menangani komplain!");
    }

    public function tolakKomplain(Request $request) {
        $data = [
            "id" => $request->id,
            "status" => "Ditolak"
        ];
        $komp = new ModelsKomplainRequest();
        $komp->updateStatus($data);

        return redirect()->back()->with("success", "Berhasil menolak komplain!");
    }
}
