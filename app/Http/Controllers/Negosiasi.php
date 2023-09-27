<?php

namespace App\Http\Controllers;

use App\Models\negosiasi as ModelsNegosiasi;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Negosiasi extends Controller
{
    public function tambahNegoPermintaan(Request $request) {
        $request->validate([
            "isi" => "required"
        ],[
            "required" => "isi pesan tidak boleh kosong!"
        ]);

        date_default_timezone_set("Asia/Jakarta");
        $waktu = date("Y-m-d H:i:s");
        // dd(Session::get("role"));
        if (Session::get("role") == "pemilik") {
            $pemilik = Session::get("dataRole")->id_pemilik;

            $data = [
                "isi" => $request->isi,
                "waktu" => $waktu,
                "request" => $request->permintaan,
                "jenis" => "Permintaan",
                "pemilik" => $pemilik,
                "tempat" => null
            ];
        }
        else {
            $pemilik = Session::get("dataRole")->id_tempat;

            $data = [
                "isi" => $request->isi,
                "waktu" => $waktu,
                "request" => $request->permintaan,
                "jenis" => "Permintaan",
                "pemilik" => null,
                "tempat" => $pemilik
            ];
        }
        $nego = new ModelsNegosiasi();
        $nego->insertNegosiasi($data);

        if (Session::get("role") == "pemilik") {
            $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",Session::get("dataRole")->id_pemilik)->get()->first();
            $user = $dataPemilik->nama_pemilik;
        }
        else if (Session::get("role") == "tempat") {
            $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",Session::get("dataRole")->id_tempat)->get()->first();
            $user = $dataTempat->nama_pemilik_tempat;
        }

        $tanggalAwal = $waktu;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');

        return response()->json(['success' => true, 'message' => 'Berhasil Mengirim Pesan!', 'data' => $data, 'user' => $user, 'waktu' => $tanggalBaru]);
    }

    public function tambahNegoPenawaran(Request $request) {
        $request->validate([
            "isi" => "required"
        ],[
            "required" => "isi pesan tidak boleh kosong!"
        ]);

        date_default_timezone_set("Asia/Jakarta");
        $waktu = date("Y-m-d H:i:s");

        if (Session::get("role") == "pemilik") {
            $pemilik = Session::get("dataRole")->id_pemilik;

            $data = [
                "isi" => $request->isi,
                "waktu" => $waktu,
                "request" => $request->penawaran,
                "jenis" => "Penawaran",
                "pemilik" => $pemilik,
                "tempat" => null
            ];
        }
        else {
            $pemilik = Session::get("dataRole")->id_tempat;

            $data = [
                "isi" => $request->isi,
                "waktu" => $waktu,
                "request" => $request->penawaran,
                "jenis" => "Penawaran",
                "pemilik" => null,
                "tempat" => $pemilik
            ];
        }

        $nego = new ModelsNegosiasi();
        $nego->insertNegosiasi($data);

        if (Session::get("role") == "pemilik") {
            $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",Session::get("dataRole")->id_pemilik)->get()->first();
            $user = $dataPemilik->nama_pemilik;
        }
        else if (Session::get("role") == "tempat") {
            $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",Session::get("dataRole")->id_tempat)->get()->first();
            $user = $dataTempat->nama_pemilik_tempat;
        }

        $tanggalAwal = $waktu;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');

        return response()->json(['success' => true, 'message' => 'Berhasil Mengirim Pesan!', 'data' => $data, 'user' => $user, 'waktu' => $tanggalBaru]);
    }
}
