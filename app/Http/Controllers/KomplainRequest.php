<?php

namespace App\Http\Controllers;

use App\Models\komplainRequest as ModelsKomplainRequest;
use Illuminate\Http\Request;

class KomplainRequest extends Controller
{
    public function tambahKomplain(Request $request){
        $request->validate([
            "jenis" => "required",
            "keterangan" => "required",
            "foto.*" => 'required|max:5120'
        ],[
            "foto.*.max" => "ukuran foto alat olahraga tidak boleh melebihi 5MB!",
            "required" => ":attribute komplain tidak boleh kosong!",
            "foto.*.required" => "foto bukti komplain tidak boleh kosong!"
        ]);

        date_default_timezone_set("Asia/Jakarta");
        $tgl_komplain = date("Y-m-d H:i:s");

        $data = [
            "jenis" => $request->jenis,
            "keterangan" => $request->keterangan,
            "id_req" => $request->fk_id_request,
            "req" => $request->jenis_request,
            "waktu" => $tgl_komplain
        ];
        $komp = new ModelsKomplainRequest();
        $id = $komp->insertKomplainReq($data);

        
    }
}
