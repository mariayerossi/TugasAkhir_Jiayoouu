<?php

namespace App\Http\Controllers;

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

        if ($request->rating == null || $request->rating == "") {
            return response()->json(['success' => false, 'message' => 'Rating tidak boleh kosong!']);
        }

        $data = [
            "rating" => $request->rating,
            "review" => $request->review,
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
            "id_user" => Session::get("dataRole")->id_user,
            "id_alat" => $request->id_alat,
            "id_dtrans" => $request->id_dtrans
        ];
        $rate = new ratingAlat();
        $rate->insertRating($data);

        //kasih notif ke pemilik alat
        $alat = DB::table('alat_olahraga')->where("id_alat","=",$request->id_alat)->get()->first();

        $nama_pemilik = "";
        $url = "";
        if ($alat->fk_id_pemilik != null) {
            $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$alat->fk_id_pemilik)->get()->first()->$nama_pemilik;
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
                    <b>Nama Alat Olahraga: ".$lapangan->nama_lapangan."</b><br>
                    <b>Rating: ".$request->rating."/5⭐</b><br>
                    <b>Review: ".$isi."</b><br><br>
                    Ingat untuk datang tepat waktu dan nikmati sesi olahraga Anda! 😊"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($tempat->email_tempat, $dataNotif);

        return response()->json(['success' => true, 'message' => 'Berhasil Menambah Rating!']);
    }
}
