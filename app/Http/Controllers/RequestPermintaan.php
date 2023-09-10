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
            "durasi" => "required",
            "lapangan" => "required"
        ],[
            "harga.required" => "harga sewa tidak boleh kosong!",
            "durasi.required" => "durasi peminjaman tidak boleh kosong!",
            "lapangan.required" => "lapangan tidak boleh kosong!"
        ]);
        
        $array = explode("-", $request->lapangan);

        $harga = intval(str_replace(".", "", $request->harga));

        date_default_timezone_set("Asia/Jakarta");
        $tgl_minta = date("Y-m-d H:i:s");

        $data = [
            "harga" => $harga,
            "durasi" => $request->durasi,
            "lapangan" => $array[0],
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

    private function hitungTanggalPengembalian($tanggal_mulai, $durasi_bulan) {
        // Membuat objek DateTime dari tanggal mulai
        $tanggal = new DateTime($tanggal_mulai);
        
        // Menambahkan durasi ke tanggal mulai
        $tanggal->add(new DateInterval("P{$durasi_bulan}M"));
        
        // Mengembalikan tanggal pengembalian dalam format Y-m-d
        return $tanggal->format('Y-m-d');
    }

    public function terimaPermintaan(Request $request){
        $req = new ModelsRequestPermintaan();
        $status = $req->get_all_data_by_id($request->id_permintaan)->first()->status_permintaan;
        $durasi = $req->get_all_data_by_id($request->id_permintaan)->first()->req_durasi;
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

                //isi tgl mulai sewa dan tanggal selesai sewa
                date_default_timezone_set("Asia/Jakarta");
                $tgl_mulai = date("Y-m-d");

                $tgl_kembali = $this->hitungTanggalPengembalian($tgl_mulai, $durasi);
                $data2 = [
                    "id" => $request->id_permintaan,
                    "mulai" => $tgl_mulai,
                    "selesai" => $tgl_kembali
                ];
                $per2 = new ModelsRequestPermintaan();
                $per2->updateTanggal($data2);

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
}
