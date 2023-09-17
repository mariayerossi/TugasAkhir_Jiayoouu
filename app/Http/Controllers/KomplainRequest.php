<?php

namespace App\Http\Controllers;

use App\Models\filesKomplainReq;
use App\Models\komplainRequest as ModelsKomplainRequest;
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

        return redirect()->back()->with("success", "Berhasil Mengajukan Komplain!");
    }
}
