<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\requestPenawaran as ModelsRequestPenawaran;
use DateInterval;
use App\Models\notifikasiEmail;
use App\Models\pihakTempat;
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
        $req->insertPenawaran($data);

        //kasih notif ke pihak tempat klo ada penawaran alat baru
        $email_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$request->id_tempat)->get()->first()->email_tempat;
        $nama_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$request->id_tempat)->get()->first()->nama_tempat;
        $nama_alat = DB::table('alat_olahraga')->where("id_alat","=",$array[0])->get()->first()->nama_alat;
        $komisi_alat = DB::table('alat_olahraga')->where("id_alat","=",$array[0])->get()->first()->komisi_alat;
        // dd($email_tempat);
        $dataNotif = [
            "subject" => "Penawaran Alat Olahraga Baru",
            "judul" => "Penawaran Alat Olahraga Baru",
            "nama_user" => $nama_tempat,
            "isi" => "Anda memiliki satu penawaran alat olahraga baru:<br><br>
                    <b>Nama Alat Olahraga  : ".$nama_alat."</b><br>
                    <b>Komisi Pemilik Alat : Rp ".number_format($komisi_alat, 0, ',', '.')."</b><br><br>
                    Silahkan Konfirmasi Penawaran!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($email_tempat,$dataNotif);

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
                //(BELOM) KASIH PENGECEKAN APAKAH TANGGAL MULAI DAN SELESAI NULL TIDAK, KASIH PENGECEKAN APAKAH TANGGAL SELESAI LEBIH BESAR DARI TANGGAL SELESAI?

                //cek dulu apakah harga sewa dan tanggal masih null atau tidak
                if ($dataReq->req_harga_sewa != null) {
                    if ($dataReq->req_tanggal_mulai != null && $dataReq->req_tanggal_selesai != null) {
                        //cek apakah tanggal awal lbh awal dari tanggal berakhir?
                        $date_mulai = new DateTime($dataReq->req_tanggal_mulai);
                        $date_selesai = new DateTime($dataReq->req_tanggal_selesai);
                        
                        if ($date_selesai <= $date_mulai) {
                            return redirect()->back()->with("error", "Tanggal kembali tidak sesuai!");
                        }
                        else {
                            $data = [
                                "id" => $request->id_penawaran,
                                "status" => "Setuju"
                            ];
                            $per = new ModelsRequestPenawaran();
                            $per->updateStatusTempat($data);

                            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$dataAlat->fk_id_pemilik)->get()->first();

                            $dataNotif = [
                                "subject" => "Penawaran Alat Olahraga Anda Telah Diterima",
                                "judul" => "Penawaran Alat Olahraga Anda Telah Diterima",
                                "nama_user" => $pemilik->nama_pemilik,
                                "isi" => "Yeay! Anda memiliki satu penawaran alat olahraga yang telah diterima:<br><br>
                                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                                        Silahkan konfirmasi detail penawaran!"
                            ];
                            $e = new notifikasiEmail();
                            $e->sendEmail($pemilik->email_pemilik,$dataNotif);
                    
                            return redirect()->back()->with("success", "Menunggu konfirmasi pemilik alat olahraga");
                        }
                    }
                    else {
                        return redirect()->back()->with("error", "Masukkan tanggal mulai dan tanggal selesai terlebih dahulu!");
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

            $dataNotif = [
                "subject" => "Penawaran Alat Olahraga Anda Telah Ditolak",
                "judul" => "Penawaran Alat Olahraga Anda Telah Ditolak",
                "nama_user" => $pemilik->nama_pemilik,
                "isi" => "Sayang sekali! Anda memiliki satu penawaran alat olahraga yang ditolak:<br><br>
                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Tawarkan alat olahragamu di tempat lain. Tetap semangat dan terus berinovasi dalam menawarkan produkmu!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($pemilik->email_pemilik,$dataNotif);
    
            return redirect("/tempat/penawaran/daftarPenawaran");
        }
        else {
            return redirect()->back()->with("error", "Gagal menolak penawaran! status alat sudah $penawaran->status_penawaran");
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

    public function editTanggalMulai(Request $request) {
        $request->validate([
            "tanggal_mulai" => "required"
        ],[
            "required" => "tanggal mulai peminjaman tidak boleh kosong!"
        ]);

        $data = [
            "id" => $request->id_penawaran,
            "tanggal" => $request->tanggal_mulai
        ];
        $pen = new ModelsRequestPenawaran();
        $pen->updateTanggalMulai($data);

        return redirect()->back()->with("success", "Berhasil mengedit tanggal mulai sewa!");
    }

    public function editTanggalSelesai(Request $request) {
        $request->validate([
            "tanggal_selesai" => "required"
        ],[
            "required" => "tanggal mulai peminjaman tidak boleh kosong!"
        ]);

        $data = [
            "id" => $request->id_penawaran,
            "tanggal" => $request->tanggal_selesai
        ];
        $pen = new ModelsRequestPenawaran();
        $pen->updateTanggalSelesai($data);

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


            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$dataReq->fk_id_tempat)->get()->first();
            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$dataReq->req_id_alat)->get()->first();

            $dataNotif = [
                "subject" => "Detail Penawaran Alat Olahraga Anda Telah Dikonfirmasi",
                "judul" => "Detail Penawaran Alat Olahraga Anda Telah Dikonfirmasi",
                "nama_user" => $tempat->nama_tempat,
                "isi" => "Anda memiliki satu penawaran alat olahraga yang detailnya telah dikonfirmasi pemilik:<br><br>
                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Tunggu pemilik alat olahraga mengantarkan alat olahraganya ke anda!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($tempat->email_tempat, $dataNotif);

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
            
            $req = new ModelsRequestPenawaran();
            $penawaran = $req->get_all_data_by_id($request->id)->first();
            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$penawaran->fk_id_pemilik)->get()->first();
            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$penawaran->req_id_alat)->get()->first();
            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$penawaran->fk_id_tempat)->get()->first();

            //notif tempat
            $dataNotif = [
                "subject" => "Penawaran Alat Olahraga Telah Dikonfirmasi",
                "judul" => "Penawaran Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $tempat->nama_tempat,
                "isi" => "Selamat! Penawaran alat olahraga telah mendapatkan konfirmasi:<br><br>
                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Waktunya bersinar! Alat Olahraga kini siap untuk disewakan dan menghasilkan keuntungan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($tempat->email_tempat,$dataNotif);

            //notif pemilik
            $dataNotif2 = [
                "subject" => "Penawaran Alat Olahraga Telah Dikonfirmasi",
                "judul" => "Penawaran Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $pemilik->nama_pemilik,
                "isi" => "Selamat! Penawaran alat olahraga Anda telah mendapatkan konfirmasi:<br><br>
                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
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
            $per = new ModelsRequestPenawaran();
            $per->updateStatusAlat($data);

            $req = new ModelsRequestPenawaran();
            $penawaran = $req->get_all_data_by_id($request->id)->first();
            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$penawaran->fk_id_pemilik)->get()->first();
            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$penawaran->req_id_alat)->get()->first();
            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$penawaran->fk_id_tempat)->get()->first();

            //blom mari
            //notif tempat
            $dataNotif = [
                "subject" => "Pengambilan Alat Olahraga Telah Dikonfirmasi",
                "judul" => "Pengambilan Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $tempat->nama_tempat,
                "isi" => "Alat olahraga telah dikonfirmasi pengambilannya:<br><br>
                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
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
                        <b>Nama Alat Olahraga   : ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga : Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
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
                ->get();
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
                ->get();
        $param["dikomplain"] = $dikomplain;

        return view("tempat.penawaran.daftarPenawaran")->with($param);
    }

    public function statusSelesai($id) {
        //select semua table request_penawaran lalu where tgl selesai > now, jd jgn pake $id

        $data = [
            "id" => $id,
            "status" => "Selesai"
        ];
        $pen = new ModelsRequestPenawaran();
        $pen->updateStatus($data);

        //status alat menjadi aktif lagi
        

        $penawaran = DB::table('request_penawaran')->where("id_penawaran","=",$id)->get()->first();
        $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$penawaran->fk_id_tempat)->get()->first();
        $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$penawaran->fk_id_pemilik)->get()->first();
        $alat = DB::table('alat_olahraga')->where("id_alat","=",$penawaran->req_id_alat)->get()->first();

        //total komisi tempat (alat sewa) masuk saldo tempat
        $trans = DB::table('dtrans')
                ->select("htrans.fk_id_tempat", "dtrans.id_dtrans", "dtrans.total_komisi_tempat", "dtrans.fk_id_tempat as tempat")
                ->join("htrans", "dtrans.fk_id_htrans","htrans.id_htrans")
                ->where("fk_id_alat","=",$penawaran->req_id_alat)
                ->get();
        if (!$trans->isEmpty()) {
            foreach ($trans as $key => $value) {
                if ($value->fk_id_tempat == $penawaran->fk_id_tempat) {
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
                    <b>Nama Alat Olahraga   : ".$alat->nama_alat."</b><br>
                    <b>Komisi Alat Olahraga : Rp ".number_format($alat->komisi_alat, 0, ',', '.')."</b><br><br>
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
                    <b>Nama Alat Olahraga   : ".$alat->nama_alat."</b><br>
                    <b>Komisi Alat Olahraga : Rp ".number_format($alat->komisi_alat, 0, ',', '.')."</b><br><br>
                    Anda bisa mengambil alat olahraga Anda dan mencoba untuk menyewakannya di tempat lain. Teruslah berusaha dan berinovasi dalam penawaran Anda!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($pemilik->email_pemilik,$dataNotif2);
    }
}
