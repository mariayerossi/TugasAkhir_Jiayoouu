<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\requestPermintaan as ModelsRequestPermintaan;
use DateInterval;
use DateTime;
use App\Models\notifikasiEmail;
use App\Models\pihakTempat;
use App\Models\requestPenawaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RequestPermintaan extends Controller
{
    public function ajukanPermintaan(Request $request){
        // dd($request->all());
        $request->validate([
            "harga" => "required",
            "durasi_pinjam" => "required",
            "lapangan" => "required"
        ],[
            "harga.required" => "harga sewa tidak boleh kosong!",
            "durasi_pinjam.required" => "durasi pinjam tidak boleh kosong!",
            "lapangan.required" => "lapangan tidak boleh kosong!"
        ]);
        // benerin format durasinya
        $array1 = explode(" - ", $request->durasi_pinjam);
        $tgl_mulai = str_replace("/", "-", $array1[0]);
        $tgl_selesai = str_replace("/", "-", $array1[1]);
        
        $array = explode("-", $request->lapangan);

        $harga = intval(str_replace(".", "", $request->harga));

        $alat = new alatOlahraga();
        $komisi_alat = $alat->get_all_data_by_id($request->id_alat)->first()->komisi_alat;

        //cek apakah request harga lebih kecil dari komisi
        if ((int)$harga <= (int)$komisi_alat) {
            // return redirect()->back()->withInput()->with("error", "Harga Sewa Alat Olahraga harus termasuk komisi pemilik!");
            return response()->json(['success' => false, 'message' => "Harga Sewa Alat Olahraga harus termasuk komisi pemilik!"]);
        }

        date_default_timezone_set("Asia/Jakarta");
        $tgl_minta = date("Y-m-d H:i:s");

        $date_mulai = new DateTime($tgl_mulai);
        $date_selesai = new DateTime($tgl_selesai);
        
        if ($date_selesai <= $date_mulai) {
            // return redirect()->back()->withInput()->with("error", "Tanggal kembali tidak sesuai!");
            return response()->json(['success' => false, 'message' => "Tanggal kembali tidak sesuai!"]);
        }
        else if ($date_mulai < new DateTime($tgl_minta) || $date_selesai < new DateTime($tgl_minta)) {
            // return redirect()->back()->withInput()->with("error", "Tanggal tidak valid!");
            return response()->json(['success' => false, 'message' => "Tanggal tidak valid!"]);
        }
        else {
            $data = [
                "harga" => $harga,
                "lapangan" => $array[0],
                "mulai" => $date_mulai,
                "selesai" => $date_selesai,
                "id_alat" => $request->id_alat,
                "id_tempat" => $request->id_tempat,
                "id_pemilik" => $request->id_pemilik,
                "tgl_minta" => $tgl_minta,
                "status" => "Menunggu"
            ];
            $per = new ModelsRequestPermintaan();
            $id = $per->insertPermintaan($data);

            //notif email ke pemilik
            $email_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$request->id_pemilik)->get()->first()->email_pemilik;
            $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$request->id_pemilik)->get()->first()->nama_pemilik;
            $nama_alat = DB::table('alat_olahraga')->where("id_alat","=",$request->id_alat)->get()->first()->nama_alat;
            // $komisi_alat = DB::table('alat_olahraga')->where("id_alat","=",$request->id_alat)->get()->first()->komisi_alat;

            $dataNotif = [
                "subject" => "✨Permintaan Alat Olahraga Baru!✨",
                "judul" => "Permintaan Alat Olahraga Baru",
                "nama_user" => $nama_pemilik,
                "url" => "https://sportiva.my.id/pemilik/permintaan/detailPermintaanNego/".$id,
                "button" => "Lihat dan Terima Permintaan",
                "isi" => "Anda memiliki satu permintaan alat olahraga baru:<br><br>
                        <b>Nama Alat Olahraga: ".$nama_alat."</b><br>
                        <b>Komisi Pemilik Alat: Rp ".number_format($komisi_alat, 0, ',', '.')."</b><br><br>
                        Silahkan Konfirmasi Permintaan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($email_pemilik, $dataNotif);
    
            // return redirect()->back()->with("success", "Berhasil Mengirim Request!");
            return response()->json(['success' => true, 'message' => "Berhasil Mengirim Request!"]);
        }
    }

    public function batalPermintaan(Request $request){
        $req = new ModelsRequestPermintaan();
        $status = $req->get_all_data_by_id($request->id_permintaan)->first()->status_permintaan;

        if ($status == "Menunggu") {
            $per = $req->get_all_data_by_id($request->id_permintaan)->first();

            $temp = DB::table('pihak_tempat')->where("id_tempat","=",$per->fk_id_tempat)->get()->first();
            $alat = DB::table('alat_olahraga')->where("id_alat","=",$per->req_id_alat)->get()->first();

            $dataNotif = [
                "subject" => "🔔Permintaan Alat Olahraga Telah Dibatalkan Pemilik Alat!🔔",
                "judul" => "Permintaan Alat Olahraga Telah Dibatalkan Pemilik Alat",
                "nama_user" => $temp->nama_tempat,
                "url" => "https://sportiva.my.id/tempat/cariAlat",
                "button" => "Lihat dan Temukan Alat Olahraga Menarik Lainnya",
                "isi" => "Permintaan alat olahraga:<br><br>
                        <b>Nama Alat Olahraga  : ".$alat->nama_alat."</b><br>
                        <b>Komisi Pemilik Alat : Rp ".number_format($alat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Telah dibatalkan! Cari dan temukan alat olahraga lain untuk disewakan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($temp->email_tempat,$dataNotif);

            $data = [
                "id" => $request->id_permintaan,
                "status" => "Dibatalkan"
            ];
            $per = new ModelsRequestPermintaan();
            $per->updateStatus($data);
    
            return response()->json(['success' => true, 'message' => "Berhasil membatalkan permintaan!"]);
        }
        else {
            return response()->json(['success' => false, 'message' => "Gagal membatalkan permintaan! status alat sudah $status"]);
        }
    }

    public function editHargaSewa(Request $request){
        $request->validate([
            "harga_sewa"=>"required"
        ],[
            "required"=> "Harga sewa alat olahraga tidak boleh kosong!"
        ]);
        $req = new ModelsRequestPermintaan();
        $fk = $req->get_all_data_by_id($request->id_permintaan)->first()->req_id_alat;

        $alat = new alatOlahraga();
        $komisi_alat = $alat->get_all_data_by_id($fk)->first()->komisi_alat;

        //cek apakah request harga lebih kecil dari komisi
        if ((int)$request->harga_sewa <= (int)$komisi_alat) {
            return redirect()->back()->withInput()->with("error", "Harga Sewa Alat Olahraga harus termasuk komisi pemilik!");
        }

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

                //batalkan permintaan lain yg terkait dgn alat ini
                $minta = DB::table('request_permintaan')->where("req_id_alat","=",$id_alat)->where("status_permintaan","=","Menunggu")->get();
                $tawar = DB::table('request_penawaran')->where("req_id_alat","=",$id_alat)->where("status_penawaran","=","Menunggu")->get();
                if (!$minta->isEmpty()) {
                    foreach ($minta as $key => $value) {
                        $data2 = [
                            "id" => $value->id_permintaan,
                            "status" => "Dibatalkan"
                        ];
                        $per->updateStatus($data2);
                    }
                }
                if (!$tawar->isEmpty()) {
                    foreach ($tawar as $key => $value) {
                        $data3 = [
                            "id" => $value->id_penawaran,
                            "status" => "Dibatalkan"
                        ];
                        $pen = new requestPenawaran();
                        $pen->updateStatus($data3);
                    }
                }

                $email_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$id_tempat)->get()->first()->email_tempat;
                $nama_tempat = DB::table('pihak_tempat')->where("id_tempat","=",$id_tempat)->get()->first()->nama_tempat;
                $nama_alat = DB::table('alat_olahraga')->where("id_alat","=",$id_alat)->get()->first()->nama_alat;
                $pemilik = DB::table('alat_olahraga')->where("id_alat","=",$id_alat)->get()->first()->fk_id_pemilik;
                $nama_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$pemilik)->get()->first()->nama_pemilik;
                $email_pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$pemilik)->get()->first()->email_pemilik;
                $komisi_alat = DB::table('alat_olahraga')->where("id_alat","=",$id_alat)->get()->first()->komisi_alat;

                //notif email ke pihak tempat
                $dataNotif = [
                    "subject" => "🎉Permintaan Alat Olahraga Diterima!🎉",
                    "judul" => "Permintaan Alat Olahraga Diterima",
                    "nama_user" => $nama_tempat,
                    "url" => "https://sportiva.my.id/tempat/permintaan/detailPermintaanNego/".$request->id_permintaan,
                    "button" => "Lihat Detail Permintaan",
                    "isi" => "Yeay! Anda memiliki satu permintaan alat olahraga yang telah diterima:<br><br>
                            <b>Nama Alat Olahraga: ".$nama_alat."</b><br>
                            <b>Komisi Pemilik Alat: Rp ".number_format($komisi_alat, 0, ',', '.')."</b><br><br>
                            Tunggu alat olahraga diantar oleh pemilik alat olahraga ya!"
                ];
                $e = new notifikasiEmail();
                $e->sendEmail($email_tempat,$dataNotif);

                //notif email ke pemilik
                $dataNotif2 = [
                    "subject" => "🎉Berhasil Menerima Permintaan. Segera Antarkan Alat Olahragamu!🎉",
                    "judul" => "Permintaan Alat Olahraga Berhasil Anda Diterima",
                    "nama_user" => $nama_pemilik,
                    "url" => "https://sportiva.my.id/pemilik/permintaan/detailPermintaanNego/".$request->id_permintaan,
                    "button" => "Lihat Detail Permintaan",
                    "isi" => "Yeay! Anda berhasil menerima permintaan alat olahraga:<br><br>
                            <b>Nama Alat Olahraga: ".$nama_alat."</b><br>
                            <b>Diminta oleh: ".$nama_tempat."</b><br><br>
                            Mohon segera antarkan alat olahraga Anda dalam waktu 2 hari ke depan. Ingat! Jika Anda tidak mengantarkannya dalam waktu tersebut, permintaan akan otomatis dibatalkan.<br>
                            Terima kasih atas kerjasama Anda!"
                ];
                $e2 = new notifikasiEmail();
                $e2->sendEmail($email_pemilik,$dataNotif2);
        
                // return redirect()->back()->with("success", "Berhasil menerima permintaan!");
                return response()->json(['success' => true, 'message' => 'Berhasil menerima permintaan!']);
            }
            else {
                // return redirect()->back()->with("error", "Gagal menerima permintaan! status alat sudah $status");
                return response()->json(['success' => false, 'message' => "Gagal menerima permintaan! status alat sudah $status"]);
            }
        }
        else {
            // return redirect()->back()->with("error", "Gagal menerima request! status alat olahraga tidak aktif!");
            return response()->json(['success' => false, 'message' => "Gagal menerima request! status alat olahraga tidak aktif!"]);
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
                "subject" => "😔Permintaan Alat Olahraga Ditolak!😔",
                "judul" => "Permintaan Alat Olahraga Ditolak",
                "nama_user" => $nama_tempat,
                "url" => "https://sportiva.my.id/tempat/cariAlat",
                "button" => "Lihat dan Temukan Alat Olahraga Menarik Lainnya",
                "isi" => "Sayang sekali! Anda memiliki satu permintaan alat olahraga yang ditolak:<br><br>
                        <b>Nama Alat Olahraga: ".$nama_alat."</b><br>
                        <b>Komisi Pemilik Alat: Rp ".number_format($komisi_alat, 0, ',', '.')."</b><br><br>
                        Pilih dan ajukan permintaan alat olahraga lain. Teruslah bersemangat dan inovatif dalam memilih produk terbaik untuk Anda!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($email_tempat, $dataNotif);
    
            return response()->json(['success' => true, 'message' => 'Berhasil menolak permintaan!']);
        }
        else {
            return response()->json(['success' => false, 'message' => 'Gagal menolak permintaan! status alat sudah $status']);
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
                "subject" => "🎉Permintaan Alat Olahraga Telah Dikonfirmasi!🎉",
                "judul" => "Permintaan Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $tempat->nama_tempat,
                "url" => "https://sportiva.my.id/tempat/permintaan/detailPermintaanNego/".$request->id,
                "button" => "Lihat Detail Permintaan",
                "isi" => "Selamat! Permintaan alat olahraga telah mendapatkan konfirmasi:<br><br>
                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga: Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Waktunya bersinar! Alat Olahraga kini siap untuk disewakan dan menghasilkan keuntungan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($tempat->email_tempat,$dataNotif);

            //notif pemilik
            $dataNotif2 = [
                "subject" => "🎉Permintaan Alat Olahraga Telah Dikonfirmasi!🎉",
                "judul" => "Permintaan Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $pemilik->nama_pemilik,
                "url" => "https://sportiva.my.id/pemilik/permintaan/detailPermintaanNego/".$request->id,
                "button" => "Lihat Detail Permintaan",
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
                "subject" => "🎉Pengambilan Alat Olahraga Telah Dikonfirmasi!🎉",
                "judul" => "Pengambilan Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $tempat->nama_tempat,
                "url" => "https://sportiva.my.id/tempat/cariAlat",
                "button" => "Lihat dan Temukan Alat Olahraga Menarik Lainnya",
                "isi" => "Alat olahraga telah dikonfirmasi pengambilannya:<br><br>
                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                        <b>Komisi Alat Olahraga: Rp ".number_format($dataAlat->komisi_alat, 0, ',', '.')."</b><br><br>
                        Cari dan temukan alat olahraga lain untuk disewakan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($tempat->email_tempat,$dataNotif);

            //notif pemilik
            $dataNotif2 = [
                "subject" => "🎉Pengambilan Alat Olahraga Telah Dikonfirmasi!🎉",
                "judul" => "Pengambilan Alat Olahraga Telah Dikonfirmasi",
                "nama_user" => $pemilik->nama_pemilik,
                "url" => "https://sportiva.my.id/pemilik/cariLapangan",
                "button" => "Lihat dan Temukan Lapangan Olahraga Menarik Lainnya",
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
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
                ->orderBy("request_permintaan.tanggal_minta", "desc")
                ->get();
        $param["dikomplain"] = $dikomplain;

        return view("pemilik.permintaan.daftarPermintaan")->with($param);
    }
}
