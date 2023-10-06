<?php

namespace App\Http\Controllers;

use App\Models\filesKomplainTrans;
use App\Models\htrans;
use App\Models\komplainTrans as ModelsKomplainTrans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class KomplainTrans extends Controller
{
    public function ajukanKomplain(Request $request) {
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
            "id_htrans" => $request->id_htrans,
            "waktu" => $tgl_komplain,
            "user" => Session::get("dataRole")->id_user
        ];
        $komp = new ModelsKomplainTrans();
        $id = $komp->insertKomplainTrans($data);

        //insert foto alatnya
        $destinasi = "/upload";
        foreach ($request->foto as $key => $value) {
            $foto = uniqid().".".$value->getClientOriginalExtension();
            $value->move(public_path($destinasi),$foto);
            $data2 = [
                "nama"=>$foto,
                "fk"=>$id
            ];
            $file = new filesKomplainTrans();
            $file->insertFilesKomplainTrans($data2);
        }

        //mengubah status request menjadi "Dikomplain"
        $data3 = [
            "id" => $request->id_htrans,
            "status" => "Dikomplain"
        ];
        $trans = new htrans();
        $trans->updateStatus($data3);

        return redirect()->back()->with("success", "Berhasil Mengajukan Komplain!");
    }

    public function terimaKomplain(Request $request) {

    }
}
