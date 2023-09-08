<?php

namespace App\Http\Controllers;

use App\Models\negosiasi as ModelsNegosiasi;
use Illuminate\Http\Request;

class Negosiasi extends Controller
{
    public function tambahNego(Request $request) {
        $request->validate([
            "isi" => "required"
        ],[
            "required" => "isi pesan tidak boleh kosong!"
        ]);

        date_default_timezone_set("Asia/Jakarta");
        $waktu = date("Y-m-d H:i:s");

        $data = [
            "isi" => $request->isi,
            "waktu" => $waktu,
            "permintaan" => $request->permintaan,
            "id_user" => $request->id_user,
            "role" => $request->role
        ];

        $nego = new ModelsNegosiasi();
        $nego->insertNegosiasi($data);

        return redirect()->back();
        // return redirect()->back()->with("success", "Berhasil Mengirim Request!");
    }
}
