<?php

namespace App\Http\Controllers;

use App\Models\dtrans;
use App\Models\filesLapanganOlahraga;
use App\Models\htrans;
use App\Models\kategori;
use App\Models\lapanganOlahraga;
use App\Models\notifikasi;
use App\Models\ratingAlat;
use App\Models\ratingLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Rating extends Controller
{
    public function tambahRatingLapangan(Request $request) {
        // $request->validate([
        //     "rating" => "required"
        // ],[
        //     "required" => "Rating tidak boleh kosong!"
        // ]);

        // dd($request->all());

        if ($request->rating == null || $request->rating == "") {
            return response()->json(['success' => false, 'message' => 'Rating tidak boleh kosong!']);
        }

        $data = [
            "rating" => $request->rating,
            "review" => $request->review,
            "hide" => $request->status,
            "id_user" => Session::get("dataRole")->id_user,
            "id_lapangan" => $request->id_lapangan,
            "id_htrans" => $request->id_htrans
        ];
        $rate = new ratingLapangan();
        $rate->insertRating($data);

        //kasih notif ke pihak tempat
        $lapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$request->id_lapangan)->get()->first();
        $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$lapangan->pemilik_lapangan)->get()->first();

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");

        //notif web ke pihak tempat
        $dataNotifWeb = [
            "keterangan" => "Rating dan Review Baru Lapangan Olahraga ".$lapangan->nama_lapangan,
            "waktu" => $skrg,
            "link" => "/tempat/lapangan/lihatDetailLapangan/".$request->id_lapangan,
            "user" => null,
            "pemilik" => null,
            "tempat" => $tempat->id_tempat,
            "admin" => null
        ];
        $notifWeb = new notifikasi();
        $notifWeb->insertNotifikasi($dataNotifWeb);

        $isi = "";
        if ($request->review != null || $request->review != "") {
            $isi = $request->review;
        }
        else {
            $isi = "(Tidak ada review)";
        }

        $dataNotif = [
            "subject" => "🎉Rating dan Review Baru Lapangan Olahraga!🎉",
            "judul" => "Rating dan Review Baru Lapangan Olahraga!",
            "nama_user" => $tempat->nama_tempat,
            "url" => "https://sportiva.my.id/tempat/lapangan/lihatDetailLapangan/".$request->id_lapangan,
            "button" => "Lihat Detail Lapangan",
            "isi" => "Yeay! Anda mendapatkan rating dan review dari lapangan:<br><br>
                    <b>Nama Lapangan Olahraga: ".$lapangan->nama_lapangan."</b><br>
                    <b>Rating: ".$request->rating."/5⭐</b><br>
                    <b>Review: ".$isi
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($tempat->email_tempat, $dataNotif);

        return response()->json(['success' => true, 'message' => 'Berhasil Menambah Rating!']);
    }

    public function tambahRatingAlat(Request $request) {
        // $request->validate([
        //     "rating" => "required"
        // ],[
        //     "required" => "Rating tidak boleh kosong!"
        // ]);

        if ($request->rating == null || $request->rating == "") {
            return response()->json(['success' => false, 'message' => 'Rating tidak boleh kosong!']);
        }

        $data = [
            "rating" => $request->rating,
            "review" => $request->review,
            "hide" => $request->status,
            "id_user" => Session::get("dataRole")->id_user,
            "id_alat" => $request->id_alat,
            "id_dtrans" => $request->id_dtrans
        ];
        $rate = new ratingAlat();
        $rate->insertRating($data);

        //kasih notif ke pemilik alat
        $alat = DB::table('alat_olahraga')->where("id_alat","=",$request->id_alat)->get()->first();

        $nama_pemilik = "";
        $email_pemilik = "";
        $url = "";
        if ($alat->fk_id_pemilik != null) {
            $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$alat->fk_id_pemilik)->get()->first()->nama_pemilik;
            $email_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$alat->fk_id_pemilik)->get()->first()->email_pemilik;
            $url = "/pemilik/lihatDetail/".$alat->id_alat;

            date_default_timezone_set("Asia/Jakarta");
            $skrg = date("Y-m-d H:i:s");

            //notif web ke pemilik alat
            $dataNotifWeb = [
                "keterangan" => "Rating dan Review Baru Alat Olahraga ".$alat->nama_alat,
                "waktu" => $skrg,
                "link" => $url,
                "user" => null,
                "pemilik" => $alat->fk_id_pemilik,
                "tempat" => null,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);
        }
        else if ($alat->fk_id_tempat != null) {
            $nama_pemilik = DB::table('pihak_tempat')->where("id_tempat","=",$alat->fk_id_tempat)->get()->first()->nama_tempat;
            $email_pemilik = DB::table('pihak_tempat')->where("id_tempat","=",$alat->fk_id_tempat)->get()->first()->email_tempat;
            $url = "/tempat/alat/lihatDetail/".$alat->id_alat;

            date_default_timezone_set("Asia/Jakarta");
            $skrg = date("Y-m-d H:i:s");

            //notif web ke pihak tempat
            $dataNotifWeb = [
                "keterangan" => "Rating dan Review Baru Alat Olahraga ".$alat->nama_alat,
                "waktu" => $skrg,
                "link" => $url,
                "user" => null,
                "pemilik" => null,
                "tempat" => $alat->fk_id_tempat,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);
        }

        $isi = "";
        if ($request->review != null || $request->review != "") {
            $isi = $request->review;
        }
        else {
            $isi = "(Tidak ada review)";
        }

        $dataNotif = [
            "subject" => "🎉Rating dan Review Baru Alat Olahraga!🎉",
            "judul" => "Rating dan Review Baru Alat Olahraga!",
            "nama_user" => $nama_pemilik,
            "url" => $url,
            "button" => "Lihat Detail Alat",
            "isi" => "Yeay! Anda mendapatkan rating dan review dari:<br><br>
                    <b>Nama Alat Olahraga: ".$alat->nama_alat."</b><br>
                    <b>Rating: ".$request->rating."/5⭐</b><br>
                    <b>Review: ".$isi."</b><br><br>
                    Ingat untuk datang tepat waktu dan nikmati sesi olahraga Anda! 😊"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($email_pemilik, $dataNotif);

        return response()->json(['success' => true, 'message' => 'Berhasil Menambah Rating!']);
    }

    public function detailRatingCustomer($id) {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        $kot = new lapanganOlahraga();
        $param["kota"] = $kot->get_kota();
        $htrans = new htrans();
        $param["htrans"] = $htrans->get_all_data_by_id($id);
        $dtrans = new dtrans();
        $param["dtrans"] = $dtrans->get_all_data_by_id_htrans($id);

        $lapangan = new lapanganOlahraga();
        $id_lapangan = $htrans->get_all_data_by_id($id)->first()->fk_id_lapangan;
        $param["lap"] = $lapangan->get_all_data_by_id($id_lapangan)->first();

        $file_lapangan = new filesLapanganOlahraga();
        $param["fileLap"] = $file_lapangan->get_all_data($id_lapangan)->first();

        $param["ratingLap"] = $ratingLap = DB::table('rating_lapangan')->where("fk_id_lapangan","=",$htrans->first()->fk_id_lapangan)->where("fk_id_htrans","=",$htrans->first()->id_htrans)->get()->first();
        
        return view("customer.ulasan")->with($param);
    }
}
