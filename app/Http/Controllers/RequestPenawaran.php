<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\requestPenawaran as ModelsRequestPenawaran;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;

class RequestPenawaran extends Controller
{
    public function ajukanPenawaran(Request $request) {
        $request->validate([
            "alat" => "required"
        ],[
            "required" => "alat olahraga tidak boleh kosong!"
        ]);

        $array = explode("-", $request->alat);

        date_default_timezone_set("Asia/Jakarta");
        $tgl_tawar = date("Y-m-d H:i:s");
        
        $data = [
            "lapangan" => $request->id_lapangan,
            "id_alat" => $array[0],
            "id_tempat" => $request->id_tempat,
            "id_pemilik" => $request->id_pemilik,
            "tgl_tawar" => $tgl_tawar,
            "status" => "Menunggu"
        ];
        $req = new ModelsRequestPenawaran();
        $req->insertPenawaran($data);

        return redirect()->back()->with("success", "Berhasil Menawarkan Alat!");
    }

    public function batalPenawaran(Request $request) {
        $req = new ModelsRequestPenawaran();
        $status = $req->get_all_data_by_id($request->id_penawaran)->first()->status_penawaran;

        if ($status == "Menunggu") {
            $data = [
                "id" => $request->id_penawaran,
                "status" => "Dibatalkan"
            ];
            $per = new ModelsRequestPenawaran();
            $per->updateStatus($data);
    
            return redirect("/pemilik/penawaran/daftarPenawaran");
        }
        else {
            return redirect()->back()->with("error", "Gagal membatalkan penawaran! status alat sudah $status");
        }
    }

    public function terimaPenawaran(Request $request) {
        $req = new ModelsRequestPenawaran();
        $dataReq = $req->get_all_data_by_id($request->id_penawaran)->first();

        $alat = new alatOlahraga();
        $dataAlat = $alat->get_all_data_by_id($dataReq->req_id_alat)->first();
        if ($dataAlat->status_alat == "Aktif") {
            if ($dataReq->status_penawaran == "Menunggu") {
                //cek dulu apakah harga sewa dan durasi masih null atau tidak
                if ($dataReq->req_harga_sewa != null) {
                    if ($dataReq->req_durasi != null) {
                        $data = [
                            "id" => $request->id_penawaran,
                            "status" => "Setuju"
                        ];
                        $per = new ModelsRequestPenawaran();
                        $per->updateStatusTempat($data);
                
                        return redirect()->back()->with("success", "Menunggu konfirmasi pemilik alat olahraga");
                    }
                    else {
                        return redirect()->back()->with("error", "Masukkan durasi sewa terlebih dahulu!");
                    }
                }
                else {
                    return redirect()->back()->with("error", "Masukkan harga sewa terlebih dahulu!");
                }
            }
            else {
                return redirect()->back()->with("error", "Gagal menerima penawaran! status penawaran sudah $dataReq->status_penawaran");
            }
        }
        else {
            return redirect()->back()->with("error", "Gagal menerima penawaran! status alat tidak aktif!");
        }
    }

    public function tolakPenawaran(Request $request) {
        $req = new ModelsRequestPenawaran();
        $status = $req->get_all_data_by_id($request->id_penawaran)->first()->status_penawaran;

        if ($status == "Menunggu") {
            $data = [
                "id" => $request->id_penawaran,
                "status" => "Ditolak"
            ];
            $per = new ModelsRequestPenawaran();
            $per->updateStatus($data);
    
            return redirect("/tempat/penawaran/daftarPenawaran");
        }
        else {
            return redirect()->back()->with("error", "Gagal menolak penawaran! status alat sudah $status");
        }
    }
    
    public function editHargaSewa(Request $request) {
        $request->validate([
            "harga_sewa" => "required"
        ],[
            "required" => "harga sewa tidak boleh kosong!"
        ]);

        $data = [
            "id" => $request->id_penawaran,
            "harga" => $request->harga_sewa
        ];
        $pen = new ModelsRequestPenawaran();
        $pen->updateHargaSewa($data);

        return redirect()->back()->with("success", "Berhasil mengedit harga sewa!");
    }

    public function editDurasi(Request $request) {
        $request->validate([
            "durasi" => "required"
        ],[
            "required" => "durasi sewa tidak boleh kosong!"
        ]);

        $data = [
            "id" => $request->id_penawaran,
            "durasi" => $request->durasi
        ];
        $pen = new ModelsRequestPenawaran();
        $pen->updateDurasi($data);

        return redirect()->back()->with("success", "Berhasil mengedit durasi sewa!");
    }

    private function hitungTanggalPengembalian($tanggal_mulai, $durasi_bulan) {
        // Membuat objek DateTime dari tanggal mulai
        $tanggal = new DateTime($tanggal_mulai);
        
        // Menambahkan durasi ke tanggal mulai
        $tanggal->add(new DateInterval("P{$durasi_bulan}M"));
        
        // Mengembalikan tanggal pengembalian dalam format Y-m-d
        return $tanggal->format('Y-m-d');
    }

    public function konfirmasiPenawaran(Request $request){
        $req = new ModelsRequestPenawaran();
        $dataReq = $req->get_all_data_by_id($request->id_penawaran)->first();
        if ($dataReq->status_penawaran == "Menunggu") {
            $data = [
                "id" => $request->id_penawaran,
                "status" => "Setuju"
            ];
            $req->updateStatusPemilik($data);

            //ubah status penerimaan diterima
            $data2 = [
                "id" => $request->id_penawaran,
                "status" => "Diterima"
            ];
            $req->updateStatus($data2);

            //isi tgl mulai sewa dan tanggal selesai sewa
            date_default_timezone_set("Asia/Jakarta");
            $tgl_mulai = date("Y-m-d");
            $tgl_kembali = $this->hitungTanggalPengembalian($tgl_mulai, $dataReq->req_durasi);
            // dd($tgl_mulai);
            $data3 = [
                "id" => $request->id_penawaran,
                "mulai" => $tgl_mulai,
                "selesai" => $tgl_kembali
            ];
            $req->updateTanggal($data3);

            //ubah status alat menjadi non aktif
            $data4 = [
                "id" => $dataReq->req_id_alat,
                "status" => "Non Aktif"
            ];
            $alat = new alatOlahraga();
            $alat->updateStatus($data4);
            return redirect("/pemilik/penawaran/daftarPenawaran");
        }
        else {
            return redirect()->back()->with("error", "Gagal mengkonfirmasi penawaran! status penawaran sudah $dataReq->status_penawaran");
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
        $per = new ModelsRequestPenawaran();
        $per->updateKodeMulai($data);

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
            $per = new ModelsRequestPenawaran();
            $per->updateStatus($data);
            return redirect()->back()->with("success", "Berhasil melakukan konfirmasi!");
        }
        else {
            return redirect()->back()->with("error", "Kode Konfirmasi salah!");
        }
    }
}
