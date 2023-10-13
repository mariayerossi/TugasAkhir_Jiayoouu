<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\requestPermintaan as ModelsRequestPermintaan;
use DateInterval;
use DateTime;
use App\Models\notifikasiEmail;
use App\Models\pihakTempat;
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

            //notif email
            $email_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$request->id_pemilik)->get()->first()->email_pemilik;
            $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$request->id_pemilik)->get()->first()->nama_pemilik;
            $nama_alat = DB::table('alat_olahraga')->where("id_alat","=",$request->id_alat)->get()->first()->nama_alat;
            $komisi_alat = DB::table('alat_olahraga')->where("id_alat","=",$request->id_alat)->get()->first()->komisi_alat;

            $dataNotif = [
                "subject" => "Permintaan Alat Olahraga Baru",
                "judul" => "Permintaan Alat Olahraga Baru",
                "nama_user" => $nama_pemilik,
                "isi" => "Anda memiliki satu permintaan alat olahraga baru:<br><br>
                        <b>Nama Alat Olahraga: ".$nama_alat."</b><br>
                        <b>Komisi Pemilik Alat: Rp ".number_format($komisi_alat, 0, ',', '.')."</b><br><br>
                        Silahkan Konfirmasi Permintaan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($email_pemilik,$dataNotif);
    
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
        $id_tempat = $req->get_all_data_by_id($request->id_permintaan)->first()->fk_id_tempat;

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

                $email_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$id_tempat)->get()->first()->email_tempat;
                $nama_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$id_tempat)->get()->first()->nama_tempat;
                $nama_alat = DB::table('alat_olahraga')->where("id_alat","=",$id_alat)->get()->first()->nama_alat;
                $pemilik = DB::table('alat_olahraga')->where("id_alat","=",$id_alat)->get()->first()->fk_id_pemilik;
                $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$pemilik)->get()->first()->nama_pemilik;
                $email_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$pemilik)->get()->first()->email_pemilik;
                $komisi_alat = DB::table('alat_olahraga')->where("id_alat","=",$id_alat)->get()->first()->komisi_alat;

                //notif email ke pihak tempat
                $dataNotif = [
                    "subject" => "Permintaan Alat Olahraga Diterima",
                    "judul" => "Permintaan Alat Olahraga Diterima",
                    "nama_user" => $nama_tempat,
                    "isi" => "Yeay! Anda memiliki satu permintaan alat olahraga yang telah diterima:<br><br>
                            <b>Nama Alat Olahraga: ".$nama_alat."</b><br>
                            <b>Komisi Pemilik Alat: Rp ".number_format($komisi_alat, 0, ',', '.')."</b><br><br>
                            Tunggu alat olahraga diantar oleh pemilik alat olahraga ya!"
                ];
                $e = new notifikasiEmail();
                $e->sendEmail($email_tempat,$dataNotif);

                //notif email ke pemilik
                $dataNotif2 = [
                    "subject" => "Berhasil Menerima Permintaan. Segera Antarkan Alat Olahragamu!",
                    "judul" => "Permintaan Alat Olahraga Berhasil Anda Diterima",
                    "nama_user" => $nama_pemilik,
                    "isi" => "Yeay! Anda berhasil menerima permintaan alat olahraga:<br><br>
                            <b>Nama Alat Olahraga: ".$nama_alat."</b><br>
                            <b>Diminta oleh: ".$nama_tempat."</b><br><br>
                            Mohon segera antarkan alat olahraga Anda dalam waktu 2 hari ke depan. Ingat! Jika Anda tidak mengantarkannya dalam waktu tersebut, permintaan akan otomatis dibatalkan.<br>
                            Terima kasih atas kerjasama Anda!"
                ];
                $e2 = new notifikasiEmail();
                $e2->sendEmail($email_pemilik,$dataNotif2);
        
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

            $req = new ModelsRequestPermintaan();
            $id_tempat = $req->get_all_data_by_id($request->id_permintaan)->first()->fk_id_tempat;
            $id_alat = $req->get_all_data_by_id($request->id_permintaan)->first()->req_id_alat;
            $email_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$id_tempat)->get()->first()->email_tempat;
            $nama_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$id_tempat)->get()->first()->nama_tempat;
            $nama_alat = DB::table('alat_olahraga')->where("id_alat","=",$id_alat)->get()->first()->nama_alat;
            $komisi_alat = DB::table('alat_olahraga')->where("id_alat","=",$id_alat)->get()->first()->komisi_alat;

            //notif email ke pihak tempat
            $dataNotif = [
                "subject" => "Permintaan Alat Olahraga Diterima",
                "judul" => "Permintaan Alat Olahraga Diterima",
                "nama_user" => $nama_tempat,
                "isi" => "Sayang sekali! Anda memiliki satu permintaan alat olahraga yang ditolak:<br><br>
                        <b>Nama Alat Olahraga: ".$nama_alat."</b><br>
                        <b>Komisi Pemilik Alat: Rp ".number_format($komisi_alat, 0, ',', '.')."</b><br><br>
                        Pilih dan ajukan permintaan alat olahraga lain. Teruslah bersemangat dan inovatif dalam memilih produk terbaik untuk Anda!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($email_tempat, $dataNotif);
    
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

            $req = new ModelsRequestPermintaan();
            $permintaan = $req->get_all_data_by_id($request->id)->first();
            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$permintaan->fk_id_pemilik)->get()->first();
            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$permintaan->req_id_alat)->get()->first();
            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$permintaan->fk_id_tempat)->get()->first();

            //notif tempat
            $dataNotif = [
                "subject" => "Permintaan Alat Olahraga Telah Dikonfirmasi",
                "judul" => "Permintaan Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $tempat->nama_tempat,
                "isi" => "Selamat! Permintaan alat olahraga telah mendapatkan konfirmasi:<br><br>
                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga: Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Waktunya bersinar! Alat Olahraga kini siap untuk disewakan dan menghasilkan keuntungan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($tempat->email_tempat,$dataNotif);

            //notif pemilik
            $dataNotif2 = [
                "subject" => "Permintaan Alat Olahraga Telah Dikonfirmasi",
                "judul" => "Permintaan Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $pemilik->nama_pemilik,
                "isi" => "Selamat! Permintaan alat olahraga Anda telah mendapatkan konfirmasi:<br><br>
                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga: Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Waktunya bersinar! Alat Olahraga kini siap untuk disewakan dan menghasilkan keuntungan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($pemilik->email_pemilik,$dataNotif2);
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

            $req = new ModelsRequestPermintaan();
            $permintaan = $req->get_all_data_by_id($request->id)->first();
            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$permintaan->fk_id_pemilik)->get()->first();
            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$permintaan->req_id_alat)->get()->first();
            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$permintaan->fk_id_tempat)->get()->first();

            //notif tempat
            $dataNotif = [
                "subject" => "Pengambilan Alat Olahraga Telah Dikonfirmasi",
                "judul" => "Pengambilan Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $tempat->nama_tempat,
                "isi" => "Alat olahraga telah dikonfirmasi pengambilannya:<br><br>
                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga: Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Cari dan temukan alat olahraga lain untuk disewakan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($tempat->email_tempat,$dataNotif);

            //notif pemilik
            $dataNotif2 = [
                "subject" => "Pengambilan Alat Olahraga Telah Dikonfirmasi",
                "judul" => "Pengambilan Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $pemilik->nama_pemilik,
                "isi" => "Alat olahraga anda yang telah dikonfirmasi pengambilannya:<br><br>
                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga: Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Sewakan lagi alat olahragamu di Sportiva dan kumpulkan keuntungannya!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($pemilik->email_pemilik,$dataNotif2);

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

    public function statusSelesai($id) {
        $data = [
            "id" => $id,
            "status" => "Selesai"
        ];
        $pen = new ModelsRequestPermintaan();
        $pen->updateStatus($data);

        //status alat menjadi aktif lagi
        
        
        $permintaan = DB::table('request_permintaan')->where("id_permintaan","=",$id)->get()->first();
        $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$permintaan->fk_id_tempat)->get()->first();
        $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$permintaan->fk_id_pemilik)->get()->first();
        $alat = DB::table('alat_olahraga')->where("id_alat","=",$permintaan->req_id_alat)->get()->first();

        //total komisi tempat (alat sewa) masuk saldo tempat
        $trans = DB::table('dtrans')
                ->select("htrans.fk_id_tempat", "dtrans.id_dtrans", "dtrans.total_komisi_tempat", "dtrans.fk_id_tempat as tempat")
                ->join("htrans", "dtrans.fk_id_htrans","htrans.id_htrans")
                ->where("fk_id_alat","=",$permintaan->req_id_alat)
                ->get();
        if (!$trans->isEmpty()) {
            foreach ($trans as $key => $value) {
                if ($value->fk_id_tempat == $permintaan->fk_id_tempat) {
                    if ($value->tempat == null) {
                        $extend_dtrans = DB::table('extend_dtrans')->where("fk_id_dtrans","=",$value->id_dtrans)->get()->first();

                        $saldo = (int)$this->decodePrice($tempat->saldo_tempat, "mysecretkey");
                        // dd($saldo2);
                        $saldo += (int)$value->total_komisi_tempat + $extend_dtrans->total_komisi_tempat;
                        // dd($saldo2);

                        $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

                        //update db
                        $dataSaldo = [
                            "id" => $value->fk_id_tempat,
                            "saldo" => $enkrip
                        ];
                        $temp = new pihakTempat();
                        $temp->updateSaldo($dataSaldo);
                    }
                }
            }
        }
        
        //notif email tempat
        $dataNotif = [
            "subject" => "Masa Sewa Alat Olahraga Telah Selesai",
            "judul" => "Masa Sewa Alat Olahraga Telah Selesai",
            "nama_user" => $tempat->nama_tempat,
            "isi" => "Anda memiliki satu alat olahraga yang masa sewanya sudah selesai:<br><br>
                    <b>Nama Alat Olahraga: ".$alat->nama_alat."</b><br>
                    <b>Komisi Alat Olahraga: Rp ".number_format($alat->komisi_alat, 0, ',', '.')."</b><br><br>
                    Tunggu alat olahraga diambil oleh pemilik alat. Cari dan temukan lagi alat olahraga lain untuk disewakan!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($tempat->email_tempat,$dataNotif);

        //notif email pemilik
        $dataNotif2 = [
            "subject" => "Masa Sewa Alat Olahraga Telah Selesai",
            "judul" => "Masa Sewa Alat Olahraga Telah Selesai",
            "nama_user" => $pemilik->nama_pemilik,
            "isi" => "Anda memiliki satu alat olahraga yang masa sewanya sudah selesai:<br><br>
                    <b>Nama Alat Olahraga: ".$alat->nama_alat."</b><br>
                    <b>Komisi Alat Olahraga: Rp ".number_format($alat->komisi_alat, 0, ',', '.')."</b><br><br>
                    Anda bisa mengambil alat olahraga Anda dan mencoba untuk menyewakannya di tempat lain. Teruslah berusaha dan berinovasi dalam penawaran Anda!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($pemilik->email_pemilik,$dataNotif2);
    }
}
