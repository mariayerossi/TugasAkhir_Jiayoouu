<?php

namespace App\Http\Controllers;

use App\Models\negosiasi as ModelsNegosiasi;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        if ($request->role == "Pemilik") {
            $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$request->id_user)->get()->first();
            $user = $dataPemilik->nama_pemilik;
        }
        else if ($request->role == "Tempat") {
            $dataTempat = DB::table('tempat_olahraga')->where("id_tempat","=",$request->id_tempat)->get()->first();
            $user = $dataTempat->nama_tempat;
        }

        $tanggalAwal = $waktu;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');

        return response()->json(['success' => true, 'message' => 'Berhasil Mengirim Pesan!', 'data' => $data, 'user' => $user, 'waktu' => $tanggalBaru]);
    }
}
