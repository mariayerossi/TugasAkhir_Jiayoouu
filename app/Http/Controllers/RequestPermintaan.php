<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\requestPermintaan as ModelsRequestPermintaan;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;

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
}
