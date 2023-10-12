<?php

namespace App\Http\Controllers;

use App\Models\negosiasi as ModelsNegosiasi;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\notifikasiEmail;

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
                "permintaan" => $request->permintaan,
                "penawaran" => null,
                "pemilik" => $pemilik,
                "tempat" => null
            ];
        }
        else {
            $pemilik = Session::get("dataRole")->id_tempat;

            $data = [
                "isi" => $request->isi,
                "waktu" => $waktu,
                "permintaan" => $request->permintaan,
                "penawaran" => null,
                "pemilik" => null,
                "tempat" => $pemilik
            ];
        }
        $nego = new ModelsNegosiasi();
        $nego->insertNegosiasi($data);

        $tanggalAwal = $waktu;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');

        if (Session::get("role") == "pemilik") {
            //yang kirim pemilik
            $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",Session::get("dataRole")->id_pemilik)->get()->first();
            $user = $dataPemilik->nama_pemilik;

            //kasih notif ke tempat
            $permintaan = DB::table('request_permintaan')->where("id_permintaan","=",$request->permintaan)->get()->first();
            $alat = DB::table('alat_olahraga')->where("id_alat","=",$permintaan->req_id_alat)->get()->first();
            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$permintaan->fk_id_tempat)->get()->first();

            $dataNotif = [
                "subject" => "Negosiasi Permintaan Baru",
                "judul" => "Negosiasi Permintaan Baru Dari ".$user,
                "nama_user" => $tempat->nama_tempat,
                "isi" => "Anda menerima notifikasi ini karena ada sebuah negosiasi baru yang sangat menarik di Sportiva. Berikut detailnya:<br><br>
                        <b>Permintaan Alat: ".$alat->nama_alat."</b><br>
                        <b>Pengirim Negosiasi: ".$user."</b><br>
                        <b>Tanggal Negosiasi: ".$tanggalBaru."</b><br><br>
                        <b>Deskripsi Negosiasi: ".$request->isi."</b><br><br>
                        Jangan lewatkan kesempatan ini untuk mencapai kesepakatan yang menguntungkan!<br>
                        P.S. Pastikan untuk merespons secepat mungkin agar tidak kehilangan peluang!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($tempat->email_tempat,$dataNotif);
        }
        else if (Session::get("role") == "tempat") {
            //yang kirim tempat
            $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",Session::get("dataRole")->id_tempat)->get()->first();
            $user = $dataTempat->nama_pemilik_tempat;

            //kasih notif ke pemilik
            $permintaan = DB::table('request_permintaan')->where("id_permintaan","=",$request->permintaan)->get()->first();
            $alat = DB::table('alat_olahraga')->where("id_alat","=",$permintaan->req_id_alat)->get()->first();
            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$permintaan->fk_id_pemilik)->get()->first();

            $dataNotif = [
                "subject" => "Negosiasi Permintaan Baru",
                "judul" => "Negosiasi Permintaan Baru Dari ".$user,
                "nama_user" => $pemilik->nama_pemilik,
                "isi" => "Anda menerima notifikasi ini karena ada sebuah negosiasi baru yang sangat menarik di Sportiva. Berikut detailnya:<br><br>
                        <b>Permintaan Alat: ".$alat->nama_alat."</b><br>
                        <b>Pengirim Negosiasi: ".$user."</b><br>
                        <b>Tanggal Negosiasi: ".$tanggalBaru."</b><br><br>
                        <b>Deskripsi Negosiasi: ".$request->isi."</b><br><br>
                        Jangan lewatkan kesempatan ini untuk mencapai kesepakatan yang menguntungkan!<br>
                        P.S. Pastikan untuk merespons secepat mungkin agar tidak kehilangan peluang!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($pemilik->email_pemilik,$dataNotif);
        }

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
                "permintaan" => null,
                "penawaran" => $request->penawaran,
                "pemilik" => $pemilik,
                "tempat" => null
            ];
        }
        else {
            $pemilik = Session::get("dataRole")->id_tempat;

            $data = [
                "isi" => $request->isi,
                "waktu" => $waktu,
                "permintaan" => null,
                "penawaran" => $request->penawaran,
                "pemilik" => null,
                "tempat" => $pemilik
            ];
        }

        $nego = new ModelsNegosiasi();
        $nego->insertNegosiasi($data);

        $tanggalAwal = $waktu;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');

        if (Session::get("role") == "pemilik") {
            //yang kirim pemilik
            $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",Session::get("dataRole")->id_pemilik)->get()->first();
            $user = $dataPemilik->nama_pemilik;

            //kirim notif ke tempat
            $penawaran = DB::table('request_penawaran')->where("id_penawaran","=",$request->penawaran)->get()->first();
            $alat = DB::table('alat_olahraga')->where("id_alat","=",$penawaran->req_id_alat)->get()->first();
            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$penawaran->fk_id_tempat)->get()->first();

            $dataNotif = [
                "subject" => "Negosiasi Penawaran Baru",
                "judul" => "Negosiasi Penawaran Baru Dari ".$user,
                "nama_user" => $tempat->nama_tempat,
                "isi" => "Anda menerima notifikasi ini karena ada sebuah negosiasi baru yang sangat menarik di Sportiva. Berikut detailnya:<br><br>
                        <b>Penawaran Alat: ".$alat->nama_alat."</b><br>
                        <b>Pengirim Negosiasi: ".$user."</b><br>
                        <b>Tanggal Negosiasi: ".$tanggalBaru."</b><br><br>
                        <b>Deskripsi Negosiasi: ".$request->isi."</b><br><br>
                        Jangan lewatkan kesempatan ini untuk mencapai kesepakatan yang menguntungkan!<br>
                        P.S. Pastikan untuk merespons secepat mungkin agar tidak kehilangan peluang!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($tempat->email_tempat,$dataNotif);
        }
        else if (Session::get("role") == "tempat") {
            //yang kirim tempat
            $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",Session::get("dataRole")->id_tempat)->get()->first();
            $user = $dataTempat->nama_pemilik_tempat;

            //kirim notif ke pemilik
            $penawaran = DB::table('request_penawaran')->where("id_penawaran","=",$request->penawaran)->get()->first();
            $alat = DB::table('alat_olahraga')->where("id_alat","=",$penawaran->req_id_alat)->get()->first();
            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$penawaran->fk_id_pemilik)->get()->first();

            $dataNotif = [
                "subject" => "Negosiasi Penawaran Baru",
                "judul" => "Negosiasi Penawaran Baru Dari ".$user,
                "nama_user" => $pemilik->nama_pemilik,
                "isi" => "Anda menerima notifikasi ini karena ada sebuah negosiasi baru yang sangat menarik di Sportiva. Berikut detailnya:<br><br>
                        <b>Penawaran Alat: ".$alat->nama_alat."</b><br>
                        <b>Pengirim Negosiasi: ".$user."</b><br>
                        <b>Tanggal Negosiasi: ".$tanggalBaru."</b><br><br>
                        <b>Deskripsi Negosiasi: ".$request->isi."</b><br><br>
                        Jangan lewatkan kesempatan ini untuk mencapai kesepakatan yang menguntungkan!<br>
                        P.S. Pastikan untuk merespons secepat mungkin agar tidak kehilangan peluang!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($pemilik->email_pemilik,$dataNotif);
        }

        return response()->json(['success' => true, 'message' => 'Berhasil Mengirim Pesan!', 'data' => $data, 'user' => $user, 'waktu' => $tanggalBaru]);
    }
}
