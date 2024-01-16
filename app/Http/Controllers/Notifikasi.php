<?php

namespace App\Http\Controllers;

use App\Models\notifikasi as ModelsNotifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Notifikasi extends Controller
{
    public function editStatusDibaca($id, Request $request) {
        $data = [
            "id" => $id,
            "status" => "Dibaca"
        ];
        $notif = new ModelsNotifikasi();
        $notif->updateStatus($data);

        $tujuan = $request->input('tujuan');

        return response()->json(['success' => true, 'redirect' => $tujuan]);
    }

    public function lihatNotifikasiPemilik() {
        $id = Session::get("dataRole")->id_pemilik;

        $data = DB::table('notifikasi')->where("fk_id_pemilik","=",$id)->orderBy("waktu_notifikasi","DESC")->get();

        $param["notif"] = $data;
        return view("pemilik.notifikasi")->with($param);
    }

    public function lihatNotifikasiTempat() {
        $id = Session::get("dataRole")->id_tempat;

        $data = DB::table('notifikasi')->where("fk_id_tempat","=",$id)->orderBy("waktu_notifikasi","DESC")->get();

        $param["notif"] = $data;
        return view("tempat.notifikasi")->with($param);
    }
}
