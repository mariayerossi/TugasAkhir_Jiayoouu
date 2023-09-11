<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\sewaSendiri as ModelsSewaSendiri;
use Illuminate\Http\Request;

class SewaSendiri extends Controller
{
    public function tambahSewaSendiri(Request $request){
        $request->validate([
            "alat" => "required"
        ],[
            "required" => "alat olahraga tidak boleh kosong!"
        ]);

        $alat = new alatOlahraga();
        $dataAlat = $alat->get_all_data_by_id($request->alat)->first();
        if ($dataAlat->status_alat == "Aktif") {
            $data = [
                "lapangan" => $request->id_lapangan,
                "alat" => $request->alat,
                "tempat" => $request->id_tempat
            ];
            $sewa = new ModelsSewaSendiri();
            $sewa->insertSewa($data);

            return redirect()->back()->with("success", "Berhasil menambahkan alat!");
        }
        else {
            return redirect()->back()->with("error", "Gagal menambah alat! status alat olahraga tidak aktif!");
        }
    }

    public function hapusSewaSendiri(Request $request){
        date_default_timezone_set("Asia/Jakarta");
        $tgl_delete = date("Y-m-d H:i:s");

        $data = [
            "id" => $request->id_sewa,
            "delete" => $tgl_delete
        ];
        $sewa = new ModelsSewaSendiri();
        $sewa->deleteSewa($data);

        return redirect()->back()->with("success", "Berhasil menghapus sewa alat!");
    }
}
