<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\requestPermintaan as ModelsRequestPermintaan;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RequestPermintaan extends Controller
{
    public function ajukanPermintaan(Request $request){
        $request->validate([
            "harga" => "required",
            "tgl_mulai" => "required",
            "tgl_selesai" => "required",
            "lapangan" => "required"
        ],[
            "harga.required" => "harga sewa tidak boleh kosong!",
            "tgl_mulai.required" => "tanggal pinjam tidak boleh kosong!",
            "tgl_selesai.required" => "tanggal kembali tidak boleh kosong!",
            "lapangan.required" => "lapangan tidak boleh kosong!"
        ]);
        // dd($request->id_pemilik);
        
        $array = explode("-", $request->lapangan);

        $harga = intval(str_replace(".", "", $request->harga));

        date_default_timezone_set("Asia/Jakarta");
        $tgl_minta = date("Y-m-d H:i:s");

        $date_mulai = new DateTime($request->tgl_mulai);
        $date_selesai = new DateTime($request->tgl_selesai);
        
        if ($date_selesai <= $date_mulai) {
            return redirect()->back()->with("error", "Tanggal kembali tidak sesuai!");
        }
        else {
            $data = [
                "harga" => $harga,
                "lapangan" => $array[0],
                "mulai" => $request->tgl_mulai,
                "selesai" => $request->tgl_selesai,
                "id_alat" => $request->id_alat,
                "id_tempat" => $request->id_tempat,
                "id_pemilik" => $request->id_pemilik,
                "tgl_minta" => $tgl_minta,
                "status" => "Menunggu"
            ];
            $per = new ModelsRequestPermintaan();
            $per->insertPermintaan($data);
    
            return redirect()->back()->with("success", "Berhasil Mengirim Request!");
        }
    }

    public function batalPermintaan(Request $request){
        $req = new ModelsRequestPermintaan();
        $status = $req->get_all_data_by_id($request->id_permintaan)->first()->status_permintaan;

        if ($status == "Menunggu") {
            $data = [
                "id" => $request->id_permintaan,
                "status" => "Dibatalkan"
            ];
            $per = new ModelsRequestPermintaan();
            $per->updateStatus($data);
    
            return redirect("/tempat/permintaan/daftarPermintaan");
        }
        else {
            return redirect()->back()->with("error", "Gagal membatalkan permintaan! status alat sudah $status");
        }
    }

    public function editHargaSewa(Request $request){
        $request->validate([
            "harga_sewa"=>"required"
        ],[
            "required"=> "Harga sewa alat olahraga tidak boleh kosong!"
        ]);
        $req = new ModelsRequestPermintaan();
        $status = $req->get_all_data_by_id($request->id_permintaan)->first()->status_permintaan;

        if ($status == "Menunggu") {
            $data = [
                "id" => $request->id_permintaan,
                "harga" => $request->harga_sewa
            ];
            $per = new ModelsRequestPermintaan();
            $per->updateHargaSewa($data);
    
            return redirect()->back()->with("success", "Berhasil mengubah harga sewa!");
        }
        else {
            return redirect()->back()->with("error", "Gagal mengedit harga sewa! status alat sudah $status");
        }
    }

    public function terimaPermintaan(Request $request){
        $req = new ModelsRequestPermintaan();
        $status = $req->get_all_data_by_id($request->id_permintaan)->first()->status_permintaan;
        $id_alat = $req->get_all_data_by_id($request->id_permintaan)->first()->req_id_alat;

        $alat = new alatOlahraga();
        $dataAlat = $alat->get_all_data_by_id($id_alat)->first();
        if ($dataAlat->status_alat == "Aktif") {

            if ($status == "Menunggu") {
                $data = [
                    "id" => $request->id_permintaan,
                    "status" => "Diterima"
                ];
                $per = new ModelsRequestPermintaan();
                $per->updateStatus($data);

                $data3 = [
                    "id" => $id_alat,
                    "status" => "Non Aktif"
                ];
                $alat = new alatOlahraga();
                $alat->updateStatus($data3);
        
                return redirect("/pemilik/permintaan/daftarPermintaan");
            }
            else {
                return redirect()->back()->with("error", "Gagal menerima permintaan! status alat sudah $status");
            }
        }
        else {
            return redirect()->back()->with("error", "Gagal menerima request! status alat olahraga tidak aktif!");
        }
    }

    public function tolakPermintaan(Request $request){
        $req = new ModelsRequestPermintaan();
        $status = $req->get_all_data_by_id($request->id_permintaan)->first()->status_permintaan;

        if ($status == "Menunggu") {
            $data = [
                "id" => $request->id_permintaan,
                "status" => "Ditolak"
            ];
            $per = new ModelsRequestPermintaan();
            $per->updateStatus($data);
    
            return redirect("/pemilik/permintaan/daftarPermintaan");
        }
        else {
            return redirect()->back()->with("error", "Gagal menolak permintaan! status alat sudah $status");
        }
    }

    public function simpanKodeMulai(Request $request){
        $kode = $request->input('kode');
        $id = $request->input('id');
    
        // Contoh simpel untuk menyimpan kode:
        $data = [
            "id" => $id,
            "kode" => $kode
        ];
        $per = new ModelsRequestPermintaan();
        $per->updateKodeMulai($data);

        return response()->json(['success' => true, 'message' => 'Kode berhasil disimpan']);
    }

    public function simpanKodeSelesai(Request $request){
        $kode = $request->input('kode');
        $id = $request->input('id');
    
        // Contoh simpel untuk menyimpan kode:
        $data = [
            "id" => $id,
            "kode" => $kode
        ];
        $per = new ModelsRequestPermintaan();
        $per->updateKodeSelesai($data);

        return response()->json(['success' => true, 'message' => 'Kode berhasil disimpan']);
    }

    public function confirmKodeMulai(Request $request){
        $request->validate([
            "isi" => "required"
        ],[
            "required" => "kode konfirmasi tidak boleh kosong!"
        ]);

        if ($request->isi == $request->kode) {
            $data = [
                "id" => $request->id,
                "status" => "Disewakan"
            ];
            $per = new ModelsRequestPermintaan();
            $per->updateStatus($data);
            return redirect()->back()->with("success", "Berhasil melakukan konfirmasi!");
        }
        else {
            return redirect()->back()->with("error", "Kode Konfirmasi salah!");
        }
    }

    public function confirmKodeSelesai(Request $request){
        $request->validate([
            "isi" => "required"
        ],[
            "required" => "kode konfirmasi tidak boleh kosong!"
        ]);

        if ($request->isi == $request->kode) {
            $data = [
                "id" => $request->id,
                "status" => "Dikembalikan"
            ];
            $per = new ModelsRequestPermintaan();
            $per->updateStatusAlat($data);
            return redirect()->back()->with("success", "Berhasil melakukan konfirmasi!");
        }
        else {
            return redirect()->back()->with("error", "Kode Konfirmasi salah!");
        }
    }

    public function daftarPermintaanTempat() {
        $role = Session::get("dataRole")->id_tempat;

        //baru
        $baru = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_tempat", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Menunggu")
                ->get();
        $param["baru"] = $baru;

        //diterima
        $diterima = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_tempat", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Diterima")
                ->get();
        $param["diterima"] = $diterima;

        //disewakan
        $disewakan = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_tempat", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Disewakan")
                ->get();
        $param["disewakan"] = $disewakan;

        //ditolak
        $ditolak = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_tempat", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Ditolak")
                ->get();
        $param["ditolak"] = $ditolak;

        //selesai
        $selesai = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta", "request_permintaan.status_alat")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_tempat", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Selesai")
                ->get();
        $param["selesai"] = $selesai;

        //dibatalkan
        $dibatalkan = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_tempat", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Dibatalkan")
                ->get();
        $param["dibatalkan"] = $dibatalkan;

        //dikomplain
        $dikomplain = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_tempat", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Dikomplain")
                ->get();
        $param["dikomplain"] = $dikomplain;

        return view("tempat.permintaan.daftarPermintaan")->with($param);
    }

    public function daftarPermintaanPemilik() {
        $role = Session::get("dataRole")->id_pemilik;

        //baru
        $baru = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta", "pihak_tempat.nama_tempat")
                ->join("pihak_tempat","request_permintaan.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_pemilik", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Menunggu")
                ->get();
                // dd($baru);
        $param["baru"] = $baru;

        //diterima
        $diterima = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta", "pihak_tempat.nama_tempat")
                ->join("pihak_tempat","request_permintaan.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_pemilik", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Diterima")
                ->get();
        $param["diterima"] = $diterima;

        //disewakan
        $disewakan = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta", "pihak_tempat.nama_tempat")
                ->join("pihak_tempat","request_permintaan.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_pemilik", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Disewakan")
                ->get();
        $param["disewakan"] = $disewakan;

        //ditolak
        $ditolak = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta", "pihak_tempat.nama_tempat")
                ->join("pihak_tempat","request_permintaan.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_pemilik", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Ditolak")
                ->get();
        $param["ditolak"] = $ditolak;

        //selesai
        $selesai = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta", "pihak_tempat.nama_tempat", "request_permintaan.status_alat")
                ->join("pihak_tempat","request_permintaan.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_pemilik", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Selesai")
                ->get();
        $param["selesai"] = $selesai;

        //dibatalkan
        $dibatalkan = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta", "pihak_tempat.nama_tempat")
                ->join("pihak_tempat","request_permintaan.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_pemilik", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Dibatalkan")
                ->get();
        $param["dibatalkan"] = $dibatalkan;

        //dikomplain
        $dikomplain = DB::table('request_permintaan')
                ->select("request_permintaan.id_permintaan","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.tanggal_minta", "pihak_tempat.nama_tempat")
                ->join("pihak_tempat","request_permintaan.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_permintaan.fk_id_pemilik", "=", $role)
                ->where("request_permintaan.status_permintaan","=","Dikomplain")
                ->get();
        $param["dikomplain"] = $dikomplain;

        return view("pemilik.permintaan.daftarPermintaan")->with($param);
    }
}
