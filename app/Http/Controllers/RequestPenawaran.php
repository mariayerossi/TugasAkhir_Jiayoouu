<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\filesAlatOlahraga;
use App\Models\filesLapanganOlahraga;
use App\Models\komplainRequest;
use App\Models\lapanganOlahraga;
use App\Models\negosiasi;
use App\Models\notifikasi;
use App\Models\requestPenawaran as ModelsRequestPenawaran;
use DateInterval;
use App\Models\notifikasiEmail;
use App\Models\pemilikAlat;
use App\Models\pihakTempat;
use App\Models\requestPermintaan;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
        $id = $req->insertPenawaran($data);

        $nama_alat = DB::table('alat_olahraga')->where("id_alat","=",$array[0])->get()->first()->nama_alat;

        $dataNotifWeb = [
            "keterangan" => "Penawaran Baru Alat Olahraga ".$nama_alat,
            "waktu" => $tgl_tawar,
            "link" => "/tempat/penawaran/detailPenawaranNego/".$id,
            "user" => null,
            "pemilik" => null,
            "tempat" => $request->id_tempat,
            "admin" => null
        ];
        $notifWeb = new notifikasi();
        $notifWeb->insertNotifikasi($dataNotifWeb);

        //kasih notif ke pihak tempat klo ada penawaran alat baru
        $email_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$request->id_tempat)->get()->first()->email_tempat;
        $nama_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$request->id_tempat)->get()->first()->nama_tempat;
        $komisi_alat = DB::table('alat_olahraga')->where("id_alat","=",$array[0])->get()->first()->komisi_alat;
        // dd($email_tempat);
        $dataNotif = [
            "subject" => "✨Penawaran Alat Olahraga Baru!✨",
            "judul" => "Penawaran Alat Olahraga Baru",
            "nama_user" => $nama_tempat,
            "url" => "https://sportiva.my.id/tempat/penawaran/detailPenawaranNego/".$id,
            "button" => "Lihat dan Terima Penawaran",
            "isi" => "Anda memiliki satu penawaran alat olahraga baru:<br><br>
                    <b>Nama Alat Olahraga  : ".$nama_alat."</b><br>
                    <b>Komisi Pemilik Alat : Rp ".number_format($komisi_alat, 0, ',', '.')."</b><br><br>
                    Silahkan Konfirmasi Penawaran!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($email_tempat,$dataNotif);

        // return redirect()->back()->with("success", "Berhasil Menawarkan Alat!");
        return response()->json(['success' => true, 'message' => "Berhasil Menawarkan Alat!"]);
    }

    public function batalPenawaran(Request $request) {
        $req = new ModelsRequestPenawaran();
        $status = $req->get_all_data_by_id($request->id_penawaran)->first()->status_penawaran;

        if ($status == "Menunggu") {
            $pen = $req->get_all_data_by_id($request->id_penawaran)->first();

            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$pen->fk_id_pemilik)->get()->first();
            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$pen->fk_id_tempat)->get()->first();
            $alat = DB::table('alat_olahraga')->where("id_alat","=",$pen->req_id_alat)->get()->first();

            date_default_timezone_set("Asia/Jakarta");
            $skrg = date("Y-m-d H:i:s");

            //notif web ke pihak tempat
            $dataNotifWeb = [
                "keterangan" => "Penawaran Alat Olahraga ".$alat->nama_alat." Dibatalkan Pemilik Alat",
                "waktu" => $skrg,
                "link" => "/tempat/penawaran/detailPenawaranNego/".$request->id_penawaran,
                "user" => null,
                "pemilik" => null,
                "tempat" => $pen->fk_id_tempat,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);

            $dataNotif = [
                "subject" => "🔔Penawaran Alat Olahraga Telah Dibatalkan Pemilik Alat Olahraga!🔔",
                "judul" => "Penawaran Alat Olahraga Telah Dibatalkan Pemilik Alat Olahraga",
                "nama_user" => $tempat->nama_tempat,
                "url" => "https://sportiva.my.id/tempat/penawaran/detailPenawaranNego/".$request->id_penawaran,
                "button" => "Lihat Detail Penawaran",
                "isi" => "Penawaran alat olahraga:<br><br>
                        <b>Nama Alat Olahraga  : ".$alat->nama_alat."</b><br>
                        <b>Komisi Pemilik Alat : Rp ".number_format($alat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Telah dibatalkan! Cari dan temukan lapangan olahraga lain!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($tempat->email_tempat,$dataNotif);

            $data = [
                "id" => $request->id_penawaran,
                "status" => "Dibatalkan"
            ];
            $per = new ModelsRequestPenawaran();
            $per->updateStatus($data);
    
            return response()->json(['success' => true, 'message' => "Berhasil membatalkan penawaran!"]);
        }
        else {
            return response()->json(['success' => false, 'message' => "Gagal membatalkan penawaran! status alat sudah $status"]);
        }
    }

    public function tawarLagi(Request $request) {
        $req = new ModelsRequestPenawaran();
        $dataReq = $req->get_all_data_by_id($request->id_penawaran)->first();

        date_default_timezone_set("Asia/Jakarta");
        $tgl_tawar = date("Y-m-d H:i:s");

        $data = [
            "id" => $request->id_penawaran,
            "status" => "Menunggu"
        ];
        $per = new ModelsRequestPenawaran();
        $per->updateTawarLagi($data);

        //kasih notif ke pihak tempat klo ada penawaran alat ditawar lagi
        $email_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$dataReq->fk_id_tempat)->get()->first()->email_tempat;
        $nama_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$dataReq->fk_id_tempat)->get()->first()->nama_tempat;
        $nama_alat = DB::table('alat_olahraga')->where("id_alat","=",$dataReq->req_id_alat)->get()->first()->nama_alat;
        $komisi_alat = DB::table('alat_olahraga')->where("id_alat","=",$dataReq->req_id_alat)->get()->first()->komisi_alat;
        // dd($email_tempat);
        $dataNotif = [
            "subject" => "🔔Penawaran Alat Olahraga Diajukan Kembali!🔔",
            "judul" => "Penawaran Alat Olahraga Diajukan Kembali",
            "nama_user" => $nama_tempat,
            "url" => "https://sportiva.my.id/tempat/penawaran/daftarPenawaran",
            "button" => "Lihat dan Terima Penawaran",
            "isi" => "Anda memiliki satu penawaran alat olahraga yang diajukan kembali oleh pemilik:<br><br>
                    <b>Nama Alat Olahraga  : ".$nama_alat."</b><br>
                    <b>Komisi Pemilik Alat : Rp ".number_format($komisi_alat, 0, ',', '.')."</b><br><br>
                    Silahkan Konfirmasi Penawaran!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($email_tempat,$dataNotif);

        return redirect()->back()->with("success", "Berhasil Menawarkan Alat!");
    }

    public function terimaPenawaran(Request $request) {
        $req = new ModelsRequestPenawaran();
        $dataReq = $req->get_all_data_by_id($request->id_penawaran)->first();

        $alat = new alatOlahraga();
        $dataAlat = $alat->get_all_data_by_id($dataReq->req_id_alat)->first();
        if ($dataAlat->status_alat == "Aktif") {
            if ($dataReq->status_penawaran == "Menunggu") {
                //cek dulu apakah harga sewa dan tanggal masih null atau tidak
                if ($request->harga_sewa != null) {
                    //cek apakah request harga lebih kecil dari komisi
                    if ((int)$request->harga_sewa <= (int)$request->komisi) {
                        return response()->json(['success' => false, 'message' => "Harga Sewa Alat Olahraga harus termasuk komisi pemilik!"]);
                    }

                    //update harga sewa
                    $dataH = [
                        "id" => $request->id_penawaran,
                        "harga" => $request->harga_sewa
                    ];
                    $pen = new ModelsRequestPenawaran();
                    $pen->updateHargaSewa($dataH);

                    if ($request->durasi_pinjam != null) {
                        //cek apakah tanggal awal lbh awal dari tanggal berakhir?
                        $array1 = explode(" - ", $request->durasi_pinjam);
                        $tgl_mulai = str_replace("/", "-", $array1[0]);
                        $tgl_selesai = str_replace("/", "-", $array1[1]);

                        $date_mulai = new DateTime($tgl_mulai);
                        $date_selesai = new DateTime($tgl_selesai);
                        
                        if ($date_selesai <= $date_mulai) {
                            return response()->json(['success' => false, 'message' => "Tanggal kembali tidak sesuai!"]);
                        }
                        else {
                            //update tanggal mulai
                            date_default_timezone_set("Asia/Jakarta");
                            $tgl_minta = date("Y-m-d H:i:s");

                            if ($request->status_penawaran == "Menunggu") {
                                if ($date_mulai > new DateTime($tgl_minta)) {
                                    $data = [
                                        "id" => $request->id_penawaran,
                                        "tanggal" => $date_mulai
                                    ];
                                    $pen = new ModelsRequestPenawaran();
                                    $pen->updateTanggalMulai($data);
                                }
                                else {
                                    return response()->json(['success' => false, 'message' => "Gagal mengedit tanggal mulai sewa! Tanggal mulai sewa tidak valid!"]);
                                }
                            }
                            else {
                                return response()->json(['success' => false, 'message' => "Gagal mengedit tanggal mulai sewa! Status penawaran telah $request->status_penawaran"]);
                            }

                            //update tanggal selesai
                            date_default_timezone_set("Asia/Jakarta");
                            $tgl_minta = date("Y-m-d H:i:s");

                            if ($request->status_penawaran == "Menunggu") {
                                if ($date_selesai > new DateTime($tgl_minta)) {
                                    $data = [
                                        "id" => $request->id_penawaran,
                                        "tanggal" => $date_selesai
                                    ];
                                    $pen = new ModelsRequestPenawaran();
                                    $pen->updateTanggalSelesai($data);
                                }
                                else {
                                    return response()->json(['success' => false, 'message' => "Gagal mengedit tanggal mulai sewa! Tanggal mulai sewa tidak valid!"]);
                                }
                            }
                            else {
                                return response()->json(['success' => false, 'message' => "Gagal mengedit tanggal selesai sewa! Status penawaran telah $request->status_penawaran"]);
                            }

                            $data = [
                                "id" => $request->id_penawaran,
                                "status" => "Setuju"
                            ];
                            $per = new ModelsRequestPenawaran();
                            $per->updateStatusTempat($data);

                            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$dataAlat->fk_id_pemilik)->get()->first();

                            date_default_timezone_set("Asia/Jakarta");
                            $skrg = date("Y-m-d H:i:s");

                            //notif web ke pemilik alat
                            $dataNotifWeb = [
                                "keterangan" => "Penawaran Alat Olahraga ".$dataAlat->nama_alat." Telah Diterima Pihak Tempat Olahraga",
                                "waktu" => $skrg,
                                "link" => "/pemilik/penawaran/detailPenawaranNego/".$request->id_penawaran,
                                "user" => null,
                                "pemilik" => $dataAlat->fk_id_pemilik,
                                "tempat" => null,
                                "admin" => null
                            ];
                            $notifWeb = new notifikasi();
                            $notifWeb->insertNotifikasi($dataNotifWeb);

                            $dataNotif = [
                                "subject" => "🎉Penawaran Alat Olahraga Anda Telah Diterima!🎉",
                                "judul" => "Penawaran Alat Olahraga Anda Telah Diterima",
                                "nama_user" => $pemilik->nama_pemilik,
                                "url" => "https://sportiva.my.id/pemilik/penawaran/detailPenawaranNego/".$request->id_penawaran,
                                "button" => "Lihat dan Konfirmasi Penawaran",
                                "isi" => "Yeay! Anda memiliki satu penawaran alat olahraga yang telah diterima:<br><br>
                                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                                        Silahkan konfirmasi detail penawaran!"
                            ];
                            $e = new notifikasiEmail();
                            $e->sendEmail($pemilik->email_pemilik,$dataNotif);
                    
                            // return redirect()->back()->with("success", "Menunggu konfirmasi pemilik alat olahraga");
                            return response()->json(['success' => true, 'message' => 'Menunggu konfirmasi pemilik alat olahraga']);
                        }
                    }
                    else {
                        // return redirect()->back()->withInput()->with("error", "Masukkan tanggal mulai dan tanggal selesai terlebih dahulu!");
                        return response()->json(['success' => false, 'message' => "Masukkan tanggal mulai dan tanggal selesai terlebih dahulu!"]);
                    }
                }
                else {
                    // return redirect()->back()->with("error", "Masukkan harga sewa terlebih dahulu!");
                    return response()->json(['success' => false, 'message' => "Masukkan harga sewa terlebih dahulu!"]);
                }
            }
            else {
                // return redirect()->back()->with("error", "Gagal menerima penawaran! status penawaran sudah $dataReq->status_penawaran");
                return response()->json(['success' => false, 'message' => "Gagal menerima penawaran! status penawaran sudah $dataReq->status_penawaran"]);
            }
        }
        else {
            // return redirect()->back()->with("error", "Gagal menerima penawaran! status alat tidak aktif!");
            return response()->json(['success' => false, 'message' => "Gagal menerima penawaran! status alat tidak aktif!"]);
        }
    }

    public function tolakPenawaran(Request $request) {
        $req = new ModelsRequestPenawaran();
        $penawaran = $req->get_all_data_by_id($request->id_penawaran)->first();

        if ($penawaran->status_penawaran == "Menunggu") {
            $data = [
                "id" => $request->id_penawaran,
                "status" => "Ditolak"
            ];
            $per = new ModelsRequestPenawaran();
            $per->updateStatus($data);

            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$penawaran->fk_id_pemilik)->get()->first();
            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$penawaran->req_id_alat)->get()->first();

            date_default_timezone_set("Asia/Jakarta");
            $skrg = date("Y-m-d H:i:s");

            //notif web ke pemilik alat
            $dataNotifWeb = [
                "keterangan" => "Penawaran Alat Olahraga ".$dataAlat->nama_alat." Ditolak Pihak Tempat Olahraga",
                "waktu" => $skrg,
                "link" => "/pemilik/penawaran/detailPenawaranNego/".$request->id_penawaran,
                "user" => null,
                "pemilik" => $penawaran->fk_id_pemilik,
                "tempat" => null,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);

            $dataNotif = [
                "subject" => "😔Penawaran Alat Olahraga Anda Telah Ditolak!😔",
                "judul" => "Penawaran Alat Olahraga Anda Telah Ditolak",
                "nama_user" => $pemilik->nama_pemilik,
                "url" => "https://sportiva.my.id/pemilik/penawaran/detailPenawaranNego/".$request->id_penawaran,
                "button" => "Lihat Detail Penawaran",
                "isi" => "Sayang sekali! Anda memiliki satu penawaran alat olahraga yang ditolak:<br><br>
                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Tawarkan alat olahragamu di tempat lain. Tetap semangat dan terus berinovasi dalam menawarkan produkmu!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($pemilik->email_pemilik,$dataNotif);
    
            return response()->json(['success' => true, 'message' => 'Berhasil menolak penawaran!']);
        }
        else {
            return response()->json(['success' => false, 'message' => "Gagal menolak penawaran! status alat sudah $penawaran->status_penawaran"]);
        }
    }
    
    public function editHargaSewa(Request $request) {
        $request->validate([
            "harga_sewa" => "required"
        ],[
            "required" => "harga sewa tidak boleh kosong!"
        ]);

        if ($request->status_penawaran == "Menunggu") {
            $data = [
                "id" => $request->id_penawaran,
                "harga" => $request->harga_sewa
            ];
            $pen = new ModelsRequestPenawaran();
            $pen->updateHargaSewa($data);
        }
        else {
            return redirect()->back()->with("error", "Gagal mengedit harga sewa! Status penawaran telah $request->status_penawaran");
        }

        return redirect()->back()->with("success", "Berhasil mengedit harga sewa!");
    }

    public function editTanggalMulai(Request $request) {
        $request->validate([
            "tanggal_mulai" => "required"
        ],[
            "required" => "tanggal mulai peminjaman tidak boleh kosong!"
        ]);

        date_default_timezone_set("Asia/Jakarta");
        $tgl_minta = date("Y-m-d H:i:s");

        if ($request->status_penawaran == "Menunggu") {
            if (new DateTime($request->tanggal_mulai) > new DateTime($tgl_minta)) {
                $data = [
                    "id" => $request->id_penawaran,
                    "tanggal" => $request->tanggal_mulai
                ];
                $pen = new ModelsRequestPenawaran();
                $pen->updateTanggalMulai($data);
            }
            else {
                return redirect()->back()->with("error", "Gagal mengedit tanggal mulai sewa! Tanggal mulai sewa tidak valid!");
            }
        }
        else {
            return redirect()->back()->with("error", "Gagal mengedit tanggal mulai sewa! Status penawaran telah $request->status_penawaran");
        }

        return redirect()->back()->with("success", "Berhasil mengedit tanggal mulai sewa!");
    }

    public function editTanggalSelesai(Request $request) {
        $request->validate([
            "tanggal_selesai" => "required"
        ],[
            "required" => "tanggal mulai peminjaman tidak boleh kosong!"
        ]);

        date_default_timezone_set("Asia/Jakarta");
        $tgl_minta = date("Y-m-d H:i:s");

        if ($request->status_penawaran == "Menunggu") {
            if (new DateTime($request->tanggal_selesai) > new DateTime($tgl_minta)) {
                $data = [
                    "id" => $request->id_penawaran,
                    "tanggal" => $request->tanggal_selesai
                ];
                $pen = new ModelsRequestPenawaran();
                $pen->updateTanggalSelesai($data);
            }
            else {
                return redirect()->back()->with("error", "Gagal mengedit tanggal mulai sewa! Tanggal mulai sewa tidak valid!");
            }
        }
        else {
            return redirect()->back()->with("error", "Gagal mengedit tanggal selesai sewa! Status penawaran telah $request->status_penawaran");
        }

        return redirect()->back()->with("success", "Berhasil mengedit tanggal selesai sewa!");
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

            //ubah status alat menjadi non aktif
            $data4 = [
                "id" => $dataReq->req_id_alat,
                "status" => "Non Aktif"
            ];
            $alat = new alatOlahraga();
            $alat->updateStatus($data4);

            //batalkan penawaran lain yg terkait dgn alat ini
            $minta = DB::table('request_permintaan')->where("req_id_alat","=",$dataReq->req_id_alat)->where("status_permintaan","=","Menunggu")->get();
            $tawar = DB::table('request_penawaran')->where("req_id_alat","=",$dataReq->req_id_alat)->where("status_penawaran","=","Menunggu")->get();
            if (!$minta->isEmpty()) {
                foreach ($minta as $key => $value) {
                    $data2 = [
                        "id" => $value->id_permintaan,
                        "status" => "Dibatalkan"
                    ];
                    $per = new requestPermintaan();
                    $per->updateStatus($data2);
                }
            }
            if (!$tawar->isEmpty()) {
                foreach ($tawar as $key => $value) {
                    $data3 = [
                        "id" => $value->id_penawaran,
                        "status" => "Dibatalkan"
                    ];
                    $req->updateStatus($data3);
                }
            }

            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$dataReq->fk_id_tempat)->get()->first();
            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$dataReq->req_id_alat)->get()->first();
            $pemilik = DB::table('alat_olahraga')->where("id_alat","=",$dataReq->req_id_alat)->get()->first()->fk_id_pemilik;
            $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$pemilik)->get()->first()->nama_pemilik;
            $email_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$pemilik)->get()->first()->email_pemilik;

            date_default_timezone_set("Asia/Jakarta");
            $skrg = date("Y-m-d H:i:s");

            //notif web ke pihak tempat
            $dataNotifWeb = [
                "keterangan" => "Penawaran Alat Olahraga ".$dataAlat->nama_alat." Telah Dikonfirmasi oleh Pemilik Alat",
                "waktu" => $skrg,
                "link" => "/tempat/penawaran/detailPenawaranNego/".$request->id_penawaran,
                "user" => null,
                "pemilik" => null,
                "tempat" => $dataReq->fk_id_tempat,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);

            $dataNotif = [
                "subject" => "🎉Detail Penawaran Alat Olahraga Telah Dikonfirmasi!🎉",
                "judul" => "Detail Penawaran Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $tempat->nama_tempat,
                "url" => "https://sportiva.my.id/tempat/penawaran/detailPenawaranNego/".$request->id_penawaran,
                "button" => "Lihat Detail Penawaran",
                "isi" => "Anda memiliki satu penawaran alat olahraga yang detailnya telah dikonfirmasi pemilik:<br><br>
                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Tunggu pemilik alat olahraga mengantarkan alat olahraganya ke tempat anda!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($tempat->email_tempat, $dataNotif);

            //notif email ke pemilik
            $dataNotif2 = [
                "subject" => "🎉Berhasil Mengkonfirmasi Detail. Segera Antarkan Alat Olahragamu!🎉",
                "judul" => "Berhasil Mengkonfirmasi Detail",
                "nama_user" => $nama_pemilik,
                "url" => "https://sportiva.my.id/pemilik/penawaran/detailPenawaranNego/".$request->id_penawaran,
                "button" => "Lihat Detail Penawaran",
                "isi" => "Yeay! Anda berhasil mengkonfirmasi detail permintaan alat olahraga:<br><br>
                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                        <b>Diminta oleh: ".$tempat->nama_tempat."</b><br><br>
                        Mohon segera antarkan alat olahraga Anda dalam waktu 2 hari ke depan. Ingat! Jika Anda tidak mengantarkannya dalam waktu tersebut, permintaan akan otomatis dibatalkan.<br>
                        Terima kasih atas kerjasama Anda!"
            ];
            $e2 = new notifikasiEmail();
            $e2->sendEmail($email_pemilik,$dataNotif2);

            return response()->json(['success' => true, 'message' => 'Berhasil mengkonfirmasi penawaran']);
        }
        else {
            return response()->json(['success' => false, 'message' => 'Gagal mengkonfirmasi penawaran! status penawaran sudah $dataReq->status_penawaran']);
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

    public function simpanKodeSelesai(Request $request){
        $kode = $request->input('kode');
        $id = $request->input('id');
    
        // Contoh simpel untuk menyimpan kode:
        $data = [
            "id" => $id,
            "kode" => $kode
        ];
        $per = new ModelsRequestPenawaran();
        $per->updateKodeSelesai($data);

        return response()->json(['success' => true, 'message' => 'Kode berhasil disimpan']);
    }

    public function confirmKodeMulai(Request $request){
        // $request->validate([
        //     "isi" => "required"
        // ],[
        //     "required" => "kode konfirmasi tidak boleh kosong!"
        // ]);

        if ($request->isi == null || $request->isi == "") {
            return response()->json(['success' => false, 'message' => 'Input kode tidak boleh kosong!']);
        }

        if ($request->isi == $request->kode) {
            $data = [
                "id" => $request->id,
                "status" => "Disewakan"
            ];
            $per = new ModelsRequestPenawaran();
            $per->updateStatus($data);
            
            $req = new ModelsRequestPenawaran();
            $penawaran = $req->get_all_data_by_id($request->id)->first();
            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$penawaran->fk_id_pemilik)->get()->first();
            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$penawaran->req_id_alat)->get()->first();
            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$penawaran->fk_id_tempat)->get()->first();

            date_default_timezone_set("Asia/Jakarta");
            $skrg = date("Y-m-d H:i:s");

            //notif web ke pihak tempat
            $dataNotifWeb = [
                "keterangan" => "Penawaran Alat Olahraga ".$dataAlat->nama_alat." Telah Dikonfirmasi dan Mulai Disewakan",
                "waktu" => $skrg,
                "link" => "/tempat/penawaran/detailPenawaranNego/".$request->id,
                "user" => null,
                "pemilik" => null,
                "tempat" => $penawaran->fk_id_tempat,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);

            //notif tempat
            $dataNotif = [
                "subject" => "🎉Penawaran Alat Olahraga Telah Dikonfirmasi!🎉",
                "judul" => "Penawaran Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $tempat->nama_tempat,
                "url" => "https://sportiva.my.id/tempat/penawaran/detailPenawaranNego/".$request->id,
                "button" => "Lihat Detail Penawaran",
                "isi" => "Selamat! Penawaran alat olahraga telah mendapatkan konfirmasi:<br><br>
                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Waktunya bersinar! Alat Olahraga kini siap untuk disewakan dan menghasilkan keuntungan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($tempat->email_tempat,$dataNotif);

            //notif pemilik
            $dataNotif2 = [
                "subject" => "🎉Penawaran Alat Olahraga Telah Dikonfirmasi!🎉",
                "judul" => "Penawaran Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $pemilik->nama_pemilik,
                "url" => "https://sportiva.my.id/pemilik/penawaran/detailPenawaranNego/".$request->id,
                "button" => "Lihat Detail Penawaran",
                "isi" => "Selamat! Penawaran alat olahraga Anda telah mendapatkan konfirmasi:<br><br>
                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Waktunya bersinar! Alat Olahraga kini siap untuk disewakan dan menghasilkan keuntungan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($pemilik->email_pemilik,$dataNotif2);

            return response()->json(['success' => true, 'message' => 'Berhasil melakukan konfirmasi!']);
        }
        else {
            return response()->json(['success' => false, 'message' => 'Kode konfirmasi salah!']);
        }
    }

    public function confirmKodeSelesai(Request $request){
        // $request->validate([
        //     "isi" => "required"
        // ],[
        //     "required" => "kode konfirmasi tidak boleh kosong!"
        // ]);

        if ($request->isi == null || $request->isi == "") {
            return response()->json(['success' => false, 'message' => 'Input kode tidak boleh kosong!']);
        }

        if ($request->isi == $request->kode) {
            $data = [
                "id" => $request->id,
                "status" => "Dikembalikan"
            ];
            $per = new ModelsRequestPenawaran();
            $per->updateStatusAlat($data);

            $req = new ModelsRequestPenawaran();
            $penawaran = $req->get_all_data_by_id($request->id)->first();
            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$penawaran->fk_id_pemilik)->get()->first();
            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$penawaran->req_id_alat)->get()->first();
            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$penawaran->fk_id_tempat)->get()->first();

            date_default_timezone_set("Asia/Jakarta");
            $skrg = date("Y-m-d H:i:s");

            //notif web ke pihak tempat
            $dataNotifWeb = [
                "keterangan" => "Penawaran Alat Olahraga ".$dataAlat->nama_alat." Telah Dikonfirmasi Pengembaliannya",
                "waktu" => $skrg,
                "link" => "/tempat/penawaran/detailPenawaranNego/".$request->id,
                "user" => null,
                "pemilik" => null,
                "tempat" => $penawaran->fk_id_tempat,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);

            //notif tempat
            $dataNotif = [
                "subject" => "🎉Pengambilan Alat Olahraga Telah Dikonfirmasi!🎉",
                "judul" => "Pengambilan Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $tempat->nama_tempat,
                "url" => "https://sportiva.my.id/tempat/penawaran/detailPenawaranNego/".$request->id,
                "button" => "Lihat Detail Penawaran",
                "isi" => "Alat olahraga telah dikonfirmasi pengambilannya:<br><br>
                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Cari dan temukan alat olahraga lain untuk disewakan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($tempat->email_tempat,$dataNotif);

            //notif pemilik
            $dataNotif2 = [
                "subject" => "🎉Pengambilan Alat Olahraga Telah Dikonfirmasi!🎉",
                "judul" => "Pengambilan Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $pemilik->nama_pemilik,
                "url" => "https://sportiva.my.id/pemilik/penawaran/detailPenawaranNego/".$request->id,
                "button" => "Lihat Detail Penawaran",
                "isi" => "Alat olahraga anda yang telah dikonfirmasi pengambilannya:<br><br>
                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Sewakan lagi alat olahragamu di Sportiva dan kumpulkan keuntungannya!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($pemilik->email_pemilik,$dataNotif2);

            return response()->json(['success' => true, 'message' => 'Berhasil melakukan konfirmasi!']);
        }
        else {
            return response()->json(['success' => false, 'message' => 'Kode konfirmasi salah!']);
        }
    }

    public function daftarPenawaranPemilik(){
        $role = Session::get("dataRole")->id_pemilik;

        //baru
        $baru = DB::table('request_penawaran')
                // ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pihak_tempat.nama_tempat")
                ->join("pihak_tempat","request_penawaran.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_pemilik", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Menunggu")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        // dd($baru);
        $param["baru"] = $baru;

        //diterima
        $diterima = DB::table('request_penawaran')
                ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pihak_tempat.nama_tempat")
                ->join("pihak_tempat","request_penawaran.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_pemilik", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Diterima")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        $param["diterima"] = $diterima;

        //disewakan
        $disewakan = DB::table('request_penawaran')
                ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pihak_tempat.nama_tempat")
                ->join("pihak_tempat","request_penawaran.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_pemilik", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Disewakan")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        $param["disewakan"] = $disewakan;

        //ditolak
        $ditolak = DB::table('request_penawaran')
                ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pihak_tempat.nama_tempat")
                ->join("pihak_tempat","request_penawaran.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_pemilik", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Ditolak")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        $param["ditolak"] = $ditolak;

        //selesai
        $selesai = DB::table('request_penawaran')
                ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pihak_tempat.nama_tempat", "request_penawaran.status_alat")
                ->join("pihak_tempat","request_penawaran.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_pemilik", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Selesai")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        $param["selesai"] = $selesai;

        //dibatalkan
        $dibatalkan = DB::table('request_penawaran')
                ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pihak_tempat.nama_tempat")
                ->join("pihak_tempat","request_penawaran.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_pemilik", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Dibatalkan")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        // dd($dibatalkan);
        $param["dibatalkan"] = $dibatalkan;

        //dikomplain
        $dikomplain = DB::table('request_penawaran')
                ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pihak_tempat.nama_tempat")
                ->join("pihak_tempat","request_penawaran.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_pemilik", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Dikomplain")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        $param["dikomplain"] = $dikomplain;

        return view("pemilik.penawaran.daftarPenawaran")->with($param);
    }

    public function daftarPenawaranTempat(){
        $role = Session::get("dataRole")->id_tempat;

        //baru
        $baru = DB::table('request_penawaran')
                ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pemilik_alat.nama_pemilik")
                ->join("pemilik_alat","request_penawaran.fk_id_pemilik","=","pemilik_alat.id_pemilik")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_tempat", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Menunggu")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        $param["baru"] = $baru;

        //diterima
        $diterima = DB::table('request_penawaran')
                ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pemilik_alat.nama_pemilik")
                ->join("pemilik_alat","request_penawaran.fk_id_pemilik","=","pemilik_alat.id_pemilik")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_tempat", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Diterima")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        $param["diterima"] = $diterima;

        //disewakan
        $disewakan = DB::table('request_penawaran')
                ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pemilik_alat.nama_pemilik")
                ->join("pemilik_alat","request_penawaran.fk_id_pemilik","=","pemilik_alat.id_pemilik")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_tempat", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Disewakan")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        $param["disewakan"] = $disewakan;

        //ditolak
        $ditolak = DB::table('request_penawaran')
                ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pemilik_alat.nama_pemilik")
                ->join("pemilik_alat","request_penawaran.fk_id_pemilik","=","pemilik_alat.id_pemilik")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_tempat", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Ditolak")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        $param["ditolak"] = $ditolak;

        //selesai
        $selesai = DB::table('request_penawaran')
            ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pemilik_alat.nama_pemilik", "request_penawaran.status_alat")
            ->join("pemilik_alat","request_penawaran.fk_id_pemilik","=","pemilik_alat.id_pemilik")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_tempat", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Selesai")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        $param["selesai"] = $selesai;

        //dibatalkan
        $dibatalkan = DB::table('request_penawaran')
            ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pemilik_alat.nama_pemilik")
            ->join("pemilik_alat","request_penawaran.fk_id_pemilik","=","pemilik_alat.id_pemilik")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_tempat", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Dibatalkan")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        $param["dibatalkan"] = $dibatalkan;

        //dikomplain
        $dikomplain = DB::table('request_penawaran')
                ->select("request_penawaran.id_penawaran","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_penawaran.tanggal_tawar", "pemilik_alat.nama_pemilik")
                ->join("pemilik_alat","request_penawaran.fk_id_pemilik","=","pemilik_alat.id_pemilik")
                ->join("alat_olahraga","request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("request_penawaran.fk_id_tempat", "=", $role)
                ->where("request_penawaran.status_penawaran","=","Dikomplain")
                ->orderBy("request_penawaran.tanggal_tawar", "desc")
                ->get();
        $param["dikomplain"] = $dikomplain;

        return view("tempat.penawaran.daftarPenawaran")->with($param);
    }

    public function detailPenawaranPemilik($id) {
        $role = Session::get("dataRole")->id_pemilik;
        $req = new ModelsRequestPenawaran();
        $param["penawaran"] = $req->get_all_data_by_id($id);
        $nego = new negosiasi();
        $param["nego"] = $nego->get_all_data_by_id_penawaran($id);
        $komplain = new komplainRequest();
        $param["komplain"] = $komplain->get_all_data_by_id_req_pemilik_penawaran($id, $role);

        $alat = new alatOlahraga();
        $id_alat = $req->get_all_data_by_id($id)->first()->req_id_alat;
        $param["dataAlat"] = $alat->get_all_data_by_id($id_alat)->first();

        $file_alat = new filesAlatOlahraga();
        $param["dataFileAlat"] = $file_alat->get_all_data($id_alat)->first();

        $tempat = new pihakTempat();
        $id_tempat = $req->get_all_data_by_id($id)->first()->fk_id_tempat;
        $param["dataTempat"] = $tempat->get_all_data_by_id($id_tempat)->first();

        $lapangan = new lapanganOlahraga();
        $id_lapangan = $req->get_all_data_by_id($id)->first()->req_lapangan;
        $param["dataLapangan"] = $lapangan->get_all_data_by_id($id_lapangan)->first();

        $files_lapangan = new filesLapanganOlahraga();
        $param["dataFileLapangan"] = $files_lapangan->get_all_data($id_lapangan)->first();

        return view("pemilik.penawaran.detailPenawaranNego")->with($param);
    }

    public function detailPenawaranTempat($id) {
        $role = Session::get("dataRole")->id_tempat;
        $req = new ModelsRequestPenawaran();
        $param["penawaran"] = $req->get_all_data_by_id($id);
        $nego = new negosiasi();
        $param["nego"] = $nego->get_all_data_by_id_penawaran($id);
        $komplain = new komplainRequest();
        $param["komplain"] = $komplain->get_all_data_by_id_req_tempat_penawaran($id, $role);

        $alat = new alatOlahraga();
        $id_alat = $req->get_all_data_by_id($id)->first()->req_id_alat;
        $param["dataAlat"] = $alat->get_all_data_by_id($id_alat)->first();

        $file_alat = new filesAlatOlahraga();
        $param["dataFileAlat"] = $file_alat->get_all_data($id_alat)->first();

        $pemilik = new pemilikAlat();
        $id_pemilik = $req->get_all_data_by_id($id)->first()->fk_id_pemilik;
        $param["dataPemilik"] = $pemilik->get_all_data_by_id($id_pemilik)->first();

        $lapangan = new lapanganOlahraga();
        $id_lapangan = $req->get_all_data_by_id($id)->first()->req_lapangan;
        $param["dataLapangan"] = $lapangan->get_all_data_by_id($id_lapangan)->first();

        $files_lapangan = new filesLapanganOlahraga();
        $param["dataFileLapangan"] = $files_lapangan->get_all_data($id_lapangan)->first();

        return view("tempat.penawaran.detailPenawaranNego")->with($param);
    }
}
