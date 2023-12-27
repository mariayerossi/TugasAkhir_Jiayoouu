<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\customer;
use App\Models\dtrans;
use App\Models\filesKomplainTrans;
use App\Models\htrans;
use App\Models\kategori;
use App\Models\komplainTrans as ModelsKomplainTrans;
use App\Models\lapanganOlahraga;
use App\Models\pemilikAlat;
use App\Models\pihakTempat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\notifikasiEmail;
use App\Models\requestPenawaran;
use App\Models\requestPermintaan;
use DateInterval;
use DateTime;

class KomplainTrans extends Controller
{
    public function ajukanKomplain(Request $request) {
        $request->validate([
            "jenis" => "required",
            "keterangan" => "required",
            "foto" => 'required|max:5120'
        ],[
            "foto.max" => "ukuran foto alat olahraga tidak boleh melebihi 5MB!",
            "required" => ":attribute komplain tidak boleh kosong!",
            "foto.required" => "foto bukti komplain tidak boleh kosong atau minimal lampirkan 1 foto bukti!"
        ]);

        date_default_timezone_set("Asia/Jakarta");

        //cust hanya boleh ajukan komplain di jam sewa, contoh sewa jam 15:00 ya dikasih waktu ajukan komplain 14:40 - 15:20
        $jam_sewa = DB::table('htrans')->where("deleted_at","=",null)->where("id_htrans","=",$request->id_htrans)->first()->jam_sewa;
        $tgl_sewa = DB::table('htrans')->where("deleted_at","=",null)->where("id_htrans","=",$request->id_htrans)->first()->tanggal_sewa;
        
        $waktu_sewa = $tgl_sewa . " " . $jam_sewa;
        $jam_skrg = date("Y-m-d H:i:s");
        // dd($jam_skrg);
        
        $waktu_sewa2 = new DateTime($waktu_sewa);
        $waktu_sewa2->sub(new DateInterval('PT20M'));//-20 menit
        
        $waktu_sewa3 = new DateTime($waktu_sewa);
        $waktu_sewa3->add(new DateInterval('PT20M'));//+20 menit
        // dd($waktu_sewa3);

        $skrg = new DateTime($jam_skrg);

        if ($skrg < $waktu_sewa2 || $skrg > $waktu_sewa3) {
            // dd("gagal");
            $formattedWaktuSewa2 = $waktu_sewa2->format("H:i");
            $formattedWaktuSewa3 = $waktu_sewa3->format("H:i");
    
            return redirect()->back()->with("error", "Pengajuan Komplain Transaksi ini dapat dilakukan pada jam $formattedWaktuSewa2 - $formattedWaktuSewa3!");
        }

        // dd("berhasil");

        $tgl_komplain = date("Y-m-d H:i:s");

        $data = [
            "jenis" => $request->jenis,
            "keterangan" => $request->keterangan,
            "id_htrans" => $request->id_htrans,
            "waktu" => $tgl_komplain,
            "user" => Session::get("dataRole")->id_user
        ];
        $komp = new ModelsKomplainTrans();
        $id = $komp->insertKomplainTrans($data);

        //insert foto alatnya
        $destinasi = "/upload";
        foreach ($request->foto as $key => $value) {
            $foto = uniqid().".".$value->getClientOriginalExtension();
            $value->move(public_path($destinasi),$foto);
            $data2 = [
                "nama"=>$foto,
                "fk"=>$id
            ];
            $file = new filesKomplainTrans();
            $file->insertFilesKomplainTrans($data2);
        }

        //mengubah status request menjadi "Dikomplain"
        $data3 = [
            "id" => $request->id_htrans,
            "status" => "Dikomplain"
        ];
        $trans = new htrans();
        $trans->updateStatus($data3);

        //notif ke admin
        $tanggalAwal = $tgl_komplain;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');

        $dataNotif = [
            "subject" => "❗❗ Komplain Transaksi Baru ❗❗",
            "judul" => "Komplain Transaksi Baru dari ".Session::get("dataRole")->nama_user,
            "nama_user" => "Admin",
            "url" => "https://sportiva.my.id/admin/komplain/trans/detailKomplain/".$id,
            "button" => "Lihat Detail Komplain",
            "isi" => "Anda memiliki satu komplain transaksi baru:<br><br>
                    <b>Diajukan oleh: ".Session::get("dataRole")->nama_user."</b><br>
                    <b>Diajukan pada: ".$tanggalBaru."</b><br>
                    <b>Jenis Komplain: ".$request->jenis."</b><br><br>
                    Mohon segera masuk dan tangani komplain ini untuk meningkatkan kepuasan pengguna!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail("admin@gmail.com",$dataNotif);

        return redirect()->back()->with("success", "Berhasil Mengajukan Komplain!");
    }

    private function encodePrice($price, $key) {
        $encodedPrice = '';
        $priceLength = strlen($price);
        $keyLength = strlen($key);
    
        for ($i = 0; $i < $priceLength; $i++) {
            $encodedPrice .= $price[$i] ^ $key[$i % $keyLength];
        }
    
        return base64_encode($encodedPrice);
    }

    private function decodePrice($encodedPrice, $key) {
        $encodedPrice = base64_decode($encodedPrice);
        $decodedPrice = '';
        $priceLength = strlen($encodedPrice);
        $keyLength = strlen($key);
    
        for ($i = 0; $i < $priceLength; $i++) {
            $decodedPrice .= $encodedPrice[$i] ^ $key[$i % $keyLength];
        }
    
        return $decodedPrice;
    }

    public function terimaKomplain(Request $request) {
        $penanganan = "";
        if ($request->has('pengembalianCheckbox3')) {
            if ($request->jumlah != "" && $request->akun_dikembalikan != null) {
                $array = explode("-", $request->akun_dikembalikan);

                //saldo user ditambah
                $saldoUser = DB::table('user')->where("id_user","=",$request->user)->get()->first()->saldo_user;
                $saldo = $this->decodePrice($saldoUser, "mysecretkey");
                $saldo += $request->jumlah;

                $enkodeSaldo = $this->encodePrice((string)$saldo, "mysecretkey");

                $data = [
                    "id" => $request->user,
                    "saldo" => $enkodeSaldo
                ];
                $cust = new customer();
                $cust->updateSaldo($data);

                //saldo akun dipotong
                if ($array[1] == "pemilik") {
                    $saldoPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$array[0])->get()->first()->saldo_pemilik;
                    // dd($dataPemilik);
                    $saldo2 = $this->decodePrice($saldoPemilik, "mysecretkey");
                    $saldo2 -= $request->jumlah;

                    $enkodeSaldo2 = $this->encodePrice((string)$saldo2, "mysecretkey");

                    $data2 = [
                        "id" => $array[0],
                        "saldo" => $enkodeSaldo2
                    ];
                    $pemi = new pemilikAlat();
                    $pemi->updateSaldo($data2);

                    $penanganan = "Pengembalian dana dari pemilik alat sebesar $request->jumlah,";
                }
                else {
                    $saldoTempat = DB::table('pihak_tempat')->where("id_tempat","=",$array[0])->get()->first()->saldo_tempat;
                    // dd($dataPemilik);
                    $saldo2 = $this->decodePrice($saldoTempat, "mysecretkey");
                    $saldo2 -= $request->jumlah;

                    $enkodeSaldo2 = $this->encodePrice((string)$saldo2, "mysecretkey");

                    $data2 = [
                        "id" => $array[0],
                        "saldo" => $enkodeSaldo2
                    ];
                    $pemi = new pihakTempat();
                    $pemi->updateSaldo($data2);

                    $penanganan = "Pengembalian dana dari pihak tempat sebesar $request->jumlah,";
                }
            }
            else {
                return redirect()->back()->with("error", "Field nominal dan akun tidak boleh kosong!");
            }
        };

        // Pengecekan checkbox kedua
        if ($request->has('pengembalianCheckbox')) {
            if ($request->produk != "") {
                $array = explode("-", $request->produk);

                date_default_timezone_set("Asia/Jakarta");
                $tanggal = date("Y-m-d H:i:s");
                
                $data3 = [
                    "id" => $array[0],
                    "tanggal" => $tanggal
                ];

                if ($array[1] == "alat") {
                    //buat status request alat jd selesai
                    $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$array[0])->get()->first();
                    $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$dataAlat->fk_id_pemilik)->get()->first();
                    $per = DB::table('request_permintaan')->where("req_id_alat","=",$array[0])->where("status_permintaan","=","Disewakan")->get();
                    $pen = DB::table('request_penawaran')->where("req_id_alat","=",$array[0])->where("status_penawaran","=","Disewakan")->get();
                    if (!$per->isEmpty()) {
                        foreach ($per as $key => $value) {
                            $dataPer1 = [
                                "id" => $value->id_permintaan,
                                "status" => "Selesai"
                            ];
                            $permintaan = new requestPermintaan();
                            $permintaan->updateStatus($dataPer1);

                            //tambahkan seluruh komisi tempat ke saldo
                            $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$value->fk_id_tempat)->get()->first();
                            $saldoTempat = (int)$this->decodePrice($dataTempat->saldo_tempat, "mysecretkey");

                            $transaksi = DB::table('dtrans')
                                        ->select("dtrans.total_komisi_tempat", "extend_dtrans.total_komisi_tempat as total_ext")
                                        ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
                                        ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                                        ->where("dtrans.fk_id_alat","=",$value->req_id_alat)
                                        ->where("htrans.fk_id_tempat","=",$value->fk_id_tempat)
                                        ->where("htrans.status_trans","=","Selesai")
                                        ->where("dtrans.deleted_at","=",null)
                                        ->get();
                            $total = 0;
                            if (!$transaksi->isEmpty()) {
                                foreach ($transaksi as $key => $value2) {
                                    if ($value2->total_ext != null) {
                                        $total += $value2->total_komisi_tempat + $value2->total_ext;
                                    }
                                    else {
                                        $total += $value2->total_komisi_tempat;
                                    }
                                }
                            }

                            $saldoTempat += $total;
                            $enkrip = $this->encodePrice((string)$saldoTempat, "mysecretkey");

                            $temp = new pihakTempat();
                            $dataSaldo3 = [
                                "id" => $value->fk_id_tempat,
                                "saldo" => $enkrip
                            ];
                            $temp->updateSaldo($dataSaldo3);

                            //kirim notif ke pemilik, alat e dihapus dan request selesai
                            $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();
                            $dataNotif2 = [
                                "subject" => "⚠️Persewaan Alat Olahraga Anda Sudah Selesai!⚠️",
                                "judul" => "Persewaan Alat Olahraga Anda Sudah Selesai!",
                                "nama_user" => $dataPemilik->nama_pemilik,
                                "url" => "https://sportiva.my.id/pemilik/permintaan/detailPermintaanNego/".$value->id_permintaan,
                                "button" => "Lihat Detail Permintaan",
                                "isi" => "Masa sewa alat dari:<br><br>
                                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                        <b>Di Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                        Sudah selesai. Silahkan ambil alat olahragamu dan sewakan ditempat lain! Terima kasih telah mempercayai Sportiva! 😊"
                            ];
                            $e2 = new notifikasiEmail();
                            $e2->sendEmail($dataPemilik->email_pemilik,$dataNotif2);

                            //kirim notif ke tempat, request selesai
                            $dataNotif3 = [
                                "subject" => "⚠️Persewaan Alat Olahraga Sudah Selesai!⚠️",
                                "judul" => "Persewaan Alat Olahraga Sudah Selesai!",
                                "nama_user" => $dataTempat->nama_tempat,
                                "url" => "https://sportiva.my.id/tempat/permintaan/detailPermintaanNego/".$value->id_permintaan,
                                "button" => "Lihat Detail Permintaan",
                                "isi" => "Masa sewa alat dari:<br><br>
                                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                        <b>Di Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                        Sudah selesai. Silahkan cari dan temukan alat olahraga lain untuk disewakan! Terima kasih telah mempercayai Sportiva! 😊"
                            ];
                            $e3 = new notifikasiEmail();
                            $e3->sendEmail($dataTempat->email_tempat,$dataNotif3);
                        }
                    }
                    if (!$pen->isEmpty()) {
                        //kalau ada
                        foreach ($pen as $key => $value) {
                            $dataPen1 = [
                                "id" => $value->id_penawaran,
                                "status" => "Selesai"
                            ];
                            $penawaran = new requestPenawaran();
                            $penawaran->updateStatus($dataPen1);

                            //tambahkan seluruh komisi tempat ke saldo
                            $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$value->fk_id_tempat)->get()->first();
                            $saldoTempat = (int)$this->decodePrice($dataTempat->saldo_tempat, "mysecretkey");

                            $transaksi = DB::table('dtrans')
                                        ->select("dtrans.total_komisi_tempat", "extend_dtrans.total_komisi_tempat as total_ext")
                                        ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
                                        ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                                        ->where("dtrans.fk_id_alat","=",$value->req_id_alat)
                                        ->where("htrans.fk_id_tempat","=",$value->fk_id_tempat)
                                        ->where("htrans.status_trans","=","Selesai")
                                        ->where("dtrans.deleted_at","=",null)
                                        ->get();
                            $total = 0;
                            if (!$transaksi->isEmpty()) {
                                foreach ($transaksi as $key => $value2) {
                                    if ($value2->total_ext != null) {
                                        $total += $value2->total_komisi_tempat + $value2->total_ext;
                                    }
                                    else {
                                        $total += $value2->total_komisi_tempat;
                                    }
                                }
                            }

                            $saldoTempat += $total;
                            $enkrip = $this->encodePrice((string)$saldoTempat, "mysecretkey");

                            $temp = new pihakTempat();
                            $dataSaldo3 = [
                                "id" => $value->fk_id_tempat,
                                "saldo" => $enkrip
                            ];
                            $temp->updateSaldo($dataSaldo3);

                            //kirim notif ke pemilik, alat e dihapus dan request selesai
                            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$array[0])->get()->first();
                            $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();
                            $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$dataAlat->fk_id_pemilik)->get()->first();
                            $dataNotif2 = [
                                "subject" => "⚠️Persewaan Alat Olahraga Anda Sudah Selesai!⚠️",
                                "judul" => "Persewaan Alat Olahraga Anda Sudah Selesai!",
                                "nama_user" => $dataPemilik->nama_pemilik,
                                "url" => "https://sportiva.my.id/pemilik/penawaran/detailPenawaranNego/".$value->id_penawaran,
                                "button" => "Lihat Detail Penawaran",
                                "isi" => "Masa sewa alat dari:<br><br>
                                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                        <b>Di Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                        Sudah selesai. Silahkan ambil alat olahragamu dan sewakan ditempat lain! Terima kasih telah mempercayai Sportiva! 😊"
                            ];
                            $e2 = new notifikasiEmail();
                            $e2->sendEmail($dataPemilik->email_pemilik,$dataNotif2);

                            //kirim notif ke tempat, request selesai
                            $dataNotif3 = [
                                "subject" => "⚠️Persewaan Alat Olahraga Sudah Selesai!⚠️",
                                "judul" => "Persewaan Alat Olahraga Sudah Selesai!",
                                "nama_user" => $dataTempat->nama_tempat,
                                "url" => "https://sportiva.my.id/tempat/penawaran/detailPenawaranNego/".$value->id_penawaran,
                                "button" => "Lihat Detail Penawaran",
                                "isi" => "Masa sewa alat dari:<br><br>
                                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                        <b>Di Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                        Sudah selesai. Silahkan cari dan temukan alat olahraga lain untuk disewakan! Terima kasih telah mempercayai Sportiva! 😊"
                            ];
                            $e3 = new notifikasiEmail();
                            $e3->sendEmail($dataTempat->email_tempat,$dataNotif3);
                        }
                    }

                    //hapus dtrans yang berhubungan dengan alat ini & balikin dana cust (status htrans menunggu / diterima)
                    $dtr = DB::table('dtrans')
                        ->select("dtrans.id_dtrans", "htrans.fk_id_user","htrans.kode_trans","dtrans.subtotal_alat")
                        ->join("htrans", "dtrans.fk_id_htrans", "=", "htrans.id_htrans")
                        ->where("dtrans.deleted_at", "=", null)
                        ->where(function ($query) {
                            $query->where("htrans.status_trans", "=", "Menunggu")
                                ->orWhere("htrans.status_trans", "=", "Diterima");
                        })
                        ->where("dtrans.fk_id_alat", "=", $array[0])
                        ->get();
                    if (!$dtr->isEmpty()) {
                        foreach ($dtr as $key => $value2) {
                            $dataCust2 = DB::table('user')->where("id_user","=",$value2->fk_id_user)->get()->first();
                            //balikin dananya
                            $saldoCust2 = (int)$this->decodePrice($dataCust2->saldo_user, "mysecretkey");
                            $saldoCust2 += $value2->subtotal_alat;
                            $enkrip3 = $this->encodePrice((string)$saldoCust2, "mysecretkey");

                            $dataSaldo4 = [
                                "id" => $value2->fk_id_user,
                                "saldo" => $enkrip3
                            ];
                            $cust = new customer();
                            $cust->updateSaldo($dataSaldo4);

                            $data8 = [
                                "id" => $value2->id_dtrans,
                                "tanggal" => $tanggal
                            ];
                            $dtra = new dtrans();
                            $dtra->softDelete($data8);

                            //kasih notif cust
                            $dataAl = DB::table('alat_olahraga')->where("id_alat","=",$array[0])->get()->first();
                            $dataNotif4 = [
                                "subject" => "😔Alat Olahraga yang Anda Sewa Dibatalkan!😔",
                                "judul" => "Maaf! Alat Olahraga yang Anda Sewa Dibatalkan!",
                                "nama_user" => $dataCust2->nama_user,
                                "url" => "https://sportiva.my.id/customer/daftarRiwayat",
                                "button" => "Lihat Riwayat Transaksi",
                                "isi" => "Detail Transaksi:<br><br>
                                <b>Nama Alat Olahraga: ".$dataAl->nama_alat."</b><br>
                                <b>yang Disewa pada Transaksi: ".$value2->kode_trans."</b><br><br>
                                Telah dibatalkan! Jangan Khawatir, dana telah kami kembalikan ke saldo anda! 😊"
                            ];
                            $e3 = new notifikasiEmail();
                            $e3->sendEmail($dataCust2->email_user,$dataNotif4);
                        }
                    }

                    //kirim notif ke pemilik, alatnya dihapus
                    $dataNotif5 = [
                        "subject" => "😔Alat Olahraga ".$dataAlat->nama_alat." Telah Dihapus!😔",
                        "judul" => "Alat Olahraga ".$dataAlat->nama_alat." Telah Dihapus!",
                        "nama_user" => $dataPemilik->nama_pemilik,
                        "url" => "https://sportiva.my.id/pemilik/daftarAlat",
                        "button" => "Lihat Daftar Alat Olahraga",
                        "isi" => "Maaf! Alat Olahraga:<br><br>
                                <b>Nama: ".$dataAlat->nama_alat."</b><br>
                                <b>Komisi: ".$dataAlat->komisi_alat."</b><br><br>
                                Telah dihapus sebagai penanganan komplain yang diajukan!"
                    ];
                    $e = new notifikasiEmail();
                    $e->sendEmail($dataPemilik->email_pemilik,$dataNotif5);

                    $alat = new alatOlahraga();
                    $alat->softDelete($data3);

                    $penanganan .= "Hapus Alat,";
                }
                else if ($array[1] == "lapangan") {
                    //buat status request jd selesai
                    $per = DB::table('request_permintaan')->where("req_lapangan","=",$array[0])->where("status_permintaan","=","Disewakan")->get();
                    $pen = DB::table('request_penawaran')->where("req_lapangan","=",$array[0])->where("status_penawaran","=","Disewakan")->get();
                    if (!$per->isEmpty()) {
                        //kalau ada
                        foreach ($per as $key => $value) {
                            $dataPer1 = [
                                "id" => $value->id_permintaan,
                                "status" => "Selesai"
                            ];
                            $permintaan = new requestPermintaan();
                            $permintaan->updateStatus($dataPer1);

                            //tambahkan seluruh komisi tempat ke saldo
                            $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$value->fk_id_tempat)->get()->first();
                            $saldoTempat = (int)$this->decodePrice($dataTempat->saldo_tempat, "mysecretkey");

                            $transaksi = DB::table('dtrans')
                                        ->select("dtrans.total_komisi_tempat", "extend_dtrans.total_komisi_tempat as total_ext")
                                        ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
                                        ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                                        ->where("dtrans.fk_id_alat","=",$value->req_id_alat)
                                        ->where("htrans.fk_id_tempat","=",$value->fk_id_tempat)
                                        ->where("htrans.status_trans","=","Selesai")
                                        ->where("dtrans.deleted_at","=",null)
                                        ->get();
                            $total = 0;
                            if (!$transaksi->isEmpty()) {
                                foreach ($transaksi as $key => $value2) {
                                    if ($value2->total_ext != null) {
                                        $total += $value2->total_komisi_tempat + $value2->total_ext;
                                    }
                                    else {
                                        $total += $value2->total_komisi_tempat;
                                    }
                                }
                            }

                            $saldoTempat += $total;
                            $enkrip = $this->encodePrice((string)$saldoTempat, "mysecretkey");

                            $temp = new pihakTempat();
                            $dataSaldo3 = [
                                "id" => $value->fk_id_tempat,
                                "saldo" => $enkrip
                            ];
                            $temp->updateSaldo($dataSaldo3);

                            //kirim notif ke pemilik, request selesai
                            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                            $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();
                            $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$dataAlat->fk_id_pemilik)->get()->first();
                            $dataNotif2 = [
                                "subject" => "⚠️Persewaan Alat Olahraga Anda Sudah Selesai!⚠️",
                                "judul" => "Persewaan Alat Olahraga Anda Sudah Selesai!",
                                "nama_user" => $dataPemilik->nama_pemilik,
                                "url" => "https://sportiva.my.id/pemilik/permintaan/detailPermintaanNego/".$value->id_permintaan,
                                "button" => "Lihat Detail Permintaan",
                                "isi" => "Masa sewa alat dari:<br><br>
                                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                        <b>Di Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                        Sudah selesai. Silahkan ambil alat olahragamu dan sewakan ditempat lain! Terima kasih telah mempercayai Sportiva! 😊"
                            ];
                            $e2 = new notifikasiEmail();
                            $e2->sendEmail($dataPemilik->email_pemilik,$dataNotif2);

                            //kirim notif ke tempat, request selesai
                            $dataNotif3 = [
                                "subject" => "⚠️Persewaan Alat Olahraga Sudah Selesai!⚠️",
                                "judul" => "Persewaan Alat Olahraga Sudah Selesai!",
                                "nama_user" => $dataTempat->nama_tempat,
                                "url" => "https://sportiva.my.id/tempat/permintaan/detailPermintaanNego/".$value->id_permintaan,
                                "button" => "Lihat Detail Permintaan",
                                "isi" => "Masa sewa alat dari:<br><br>
                                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                        <b>Di Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                        Sudah selesai. Silahkan cari dan temukan alat olahraga lain untuk disewakan! Terima kasih telah mempercayai Sportiva! 😊"
                            ];
                            $e3 = new notifikasiEmail();
                            $e3->sendEmail($dataTempat->email_tempat,$dataNotif3);
                        }
                    }
                    if (!$pen->isEmpty()) {
                        //kalau ada
                        foreach ($pen as $key => $value) {
                            $dataPen1 = [
                                "id" => $value->id_penawaran,
                                "status" => "Selesai"
                            ];
                            $penawaran = new requestPenawaran();
                            $penawaran->updateStatus($dataPen1);

                            //tambahkan seluruh komisi tempat ke saldo
                            $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$value->fk_id_tempat)->get()->first();
                            $saldoTempat = (int)$this->decodePrice($dataTempat->saldo_tempat, "mysecretkey");

                            $transaksi = DB::table('dtrans')
                                        ->select("dtrans.total_komisi_tempat", "extend_dtrans.total_komisi_tempat as total_ext")
                                        ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
                                        ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                                        ->where("dtrans.fk_id_alat","=",$value->req_id_alat)
                                        ->where("htrans.fk_id_tempat","=",$value->fk_id_tempat)
                                        ->where("htrans.status_trans","=","Selesai")
                                        ->where("dtrans.deleted_at","=",null)
                                        ->get();
                            $total = 0;
                            if (!$transaksi->isEmpty()) {
                                foreach ($transaksi as $key => $value2) {
                                    if ($value2->total_ext != null) {
                                        $total += $value2->total_komisi_tempat + $value2->total_ext;
                                    }
                                    else {
                                        $total += $value2->total_komisi_tempat;
                                    }
                                }
                            }

                            $saldoTempat += $total;
                            $enkrip = $this->encodePrice((string)$saldoTempat, "mysecretkey");

                            $temp = new pihakTempat();
                            $dataSaldo3 = [
                                "id" => $value->fk_id_tempat,
                                "saldo" => $enkrip
                            ];
                            $temp->updateSaldo($dataSaldo3);

                            //kirim notif ke pemilik, request selesai
                            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                            $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();
                            $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$dataAlat->fk_id_pemilik)->get()->first();
                            $dataNotif2 = [
                                "subject" => "⚠️Persewaan Alat Olahraga Anda Sudah Selesai!⚠️",
                                "judul" => "Persewaan Alat Olahraga Anda Sudah Selesai!",
                                "nama_user" => $dataPemilik->nama_pemilik,
                                "url" => "https://sportiva.my.id/pemilik/penawaran/detailPenawaranNego/".$value->id_penawaran,
                                "button" => "Lihat Detail Penawaran",
                                "isi" => "Masa sewa alat dari:<br><br>
                                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                        <b>Di Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                        Sudah selesai. Silahkan ambil alat olahragamu dan sewakan ditempat lain! Terima kasih telah mempercayai Sportiva! 😊"
                            ];
                            $e2 = new notifikasiEmail();
                            $e2->sendEmail($dataPemilik->email_pemilik,$dataNotif2);

                            //kirim notif ke tempat, request selesai
                            $dataNotif3 = [
                                "subject" => "⚠️Persewaan Alat Olahraga Sudah Selesai!⚠️",
                                "judul" => "Persewaan Alat Olahraga Sudah Selesai!",
                                "nama_user" => $dataTempat->nama_tempat,
                                "url" => "https://sportiva.my.id/tempat/penawaran/detailPenawaranNego/".$value->id_penawaran,
                                "button" => "Lihat Detail Penawaran",
                                "isi" => "Masa sewa alat dari:<br><br>
                                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                        <b>Di Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                        Sudah selesai. Silahkan cari dan temukan alat olahraga lain untuk disewakan! Terima kasih telah mempercayai Sportiva! 😊"
                            ];
                            $e3 = new notifikasiEmail();
                            $e3->sendEmail($dataTempat->email_tempat,$dataNotif3);
                        }
                    }

                    $dataLap = DB::table('lapangan_olahraga')->where("id_lapangan","=",$array[0])->get()->first();
                    $dataTemp = DB::table('pihak_tempat')->where("id_tempat","=",$dataLap->pemilik_lapangan)->get()->first();
                    // dd($dataTemp);

                    //batalkan htrans yang berhubungan dengan lapangan ini & balikin seluruh dana cust
                    $cekTrans = DB::table('htrans')
                                ->where("fk_id_lapangan","=",$array[0])
                                ->where(function ($query) {
                                    $query->where("status_trans", "=", "Menunggu")
                                        ->orWhere("status_trans", "=", "Diterima");
                                })
                                ->get();
                    if (!$cekTrans->isEmpty()) {
                        foreach ($cekTrans as $key => $value) {
                            $data5 = [
                                "id" => $value->id_htrans,
                                "status" => "Dibatalkan"
                            ];
                            $htr = new htrans();
                            $htr->updateStatus($data5);

                            $dataCus = DB::table('user')->where("id_user","=",$value->fk_id_user)->get()->first();
                            $saldoCus = (int)$this->decodePrice($dataCus->saldo_user, "mysecretkey");

                            $saldoCus += $value->total_trans;

                            $enkrip = $this->encodePrice((string)$saldoCus, "mysecretkey");

                            $dataSaldo = [
                                "id" => $value->fk_id_user,
                                "saldo" => $enkrip
                            ];
                            $cust = new customer();
                            $cust->updateSaldo($dataSaldo);

                            //kasih notif ke customer, transaksi dibatalkan
                            $tanggalAwal3 = $value->tanggal_sewa;
                            $tanggalObjek3 = DateTime::createFromFormat('Y-m-d', $tanggalAwal3);
                            $tanggalBaru3 = $tanggalObjek3->format('d-m-Y');
                            $dataNotif4 = [
                                "subject" => "😔Booking Lapangan ".$dataLap->nama_lapangan." Telah Dibatalkan!😔",
                                "judul" => "Booking Lapangan ".$dataLap->nama_lapangan." Telah Dibatalkan!",
                                "nama_user" => $dataCus->nama_user,
                                "url" => "https://sportiva.my.id/customer/daftarRiwayat",
                                "button" => "Lihat Riwayat Transaksi",
                                "isi" => "Detail Sewa Lapangan:<br><br>
                                <b>Nama Lapangan Olahraga: ".$dataLap->nama_lapangan."</b><br>
                                <b>Tanggal Sewa: ".$tanggalBaru3."</b><br>
                                <b>Jam Sewa: ".$value->jam_sewa." WIB - ".\Carbon\Carbon::parse($value->jam_sewa)->addHours($value->durasi_sewa)->format('H:i:s')." WIB</b><br><br>
                                Telah dibatalkan, dana anda telah kami kembalikan ke saldo wallet! Terus jaga kesehatanmu bersama Sportiva! 😊"
                            ];
                            $e = new notifikasiEmail();
                            $e->sendEmail($dataCus->email_user, $dataNotif4);
                        }
                    }

                    //kasih notif ke tempat, lapangannya dihapus, transaksi semua dibatalkan
                    $dataNotif5 = [
                        "subject" => "😔Lapangan ".$dataLap->nama_lapangan." Telah Dihapus!😔",
                        "judul" => "Lapangan ".$dataLap->nama_lapangan." Telah Dihapus!",
                        "nama_user" => $dataTemp->nama_tempat,
                        "url" => "https://sportiva.my.id/tempat/lapangan/daftarLapangan",
                        "button" => "Lihat Daftar Lapangan",
                        "isi" => "Detail Lapangan Olahraga:<br><br>
                        <b>Nama Lapangan Olahraga: ".$dataLap->nama_lapangan."</b><br>
                        <b>Harga Sewa: Rp ".number_format($dataLap->harga_sewa_lapangan, 0, ',', '.')."</b><br><br>
                        Telah dihapus. Semua request permintaan dan penawaran dari lapangan ini yang masih disewakan akan otomatis selesai dan dana komisi alat olahraga telah masuk ke saldo anda! Sedangkan transaksi yang diterima dan menunggu konfirmasi akan otomatis dibatalkan! 😊"
                    ];
                    $e = new notifikasiEmail();
                    $e->sendEmail($dataTemp->email_tempat, $dataNotif5);

                    $lapangan = new lapanganOlahraga();
                    $lapangan->softDelete($data3);

                    $penanganan .= "Hapus Lapangan,";
                }
            }
            else {
                return redirect()->back()->with("error", "produk yang akan dihapus tidak boleh kosong!");
            }
        }

        // Pengecekan checkbox ketiga
        if ($request->has('pengembalianCheckbox2')) {
            if ($request->akun != "") {
                $array = explode("-", $request->akun);

                date_default_timezone_set("Asia/Jakarta");
                $tanggal = date("Y-m-d H:i:s");
                
                $data4 = [
                    "id" => $array[0],
                    "tanggal" => $tanggal
                ];

                if ($array[1] == "tempat") {
                    //hapus semua produknya
                    $lap = DB::table('lapangan_olahraga')->where("pemilik_lapangan","=",$array[0])->get();
                    $al = DB::table('alat_olahraga')->where("fk_id_tempat","=",$array[0])->get();
                    if (!$lap->isEmpty()) {
                        foreach ($lap as $key => $value) {
                            $data2 = [
                                "id" => $value->id_lapangan,
                                "tanggal" => $tanggal
                            ];
                            $lapa = new lapanganOlahraga();
                            $lapa->softDelete($data2);
                        }
                    }
                    if (!$al->isEmpty()) {
                        foreach ($al as $key => $value) {
                            $data3 = [
                                "id" => $value->id_alat,
                                "tanggal" => $tanggal
                            ];
                            $ala = new alatOlahraga();
                            $ala->softDelete($data3);
                        }
                    }

                    //selesai semua request yg berhubungan (ga masuk saldo, wong akun e dihapus)
                    //(disewakan = selesai, menunggu/diterima = dibatalkan)
                    $per = DB::table('request_permintaan')->where("fk_id_tempat","=",$array[0])->get();
                    $pen = DB::table('request_penawaran')->where("fk_id_tempat","=",$array[0])->get();
                    if (!$per->isEmpty()) {
                        foreach ($per as $key => $value) {
                            $dataPem = DB::table('pemilik_alat')->where("id_pemilik","=",$value->fk_id_pemilik)->get()->first();
                            $dataAla = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                            $dataLapa = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();
                            if ($value->status_permintaan == "Disewakan") {
                                $data40 = [
                                    "id" => $value->id_permintaan,
                                    "status" => "Selesai"
                                ];
                                $permin = new requestPermintaan();
                                $permin->updateStatus($data40);
                                //ga ada penambahan dana tempat, wong dihapus akun e

                                //kasih notif ke pemilik alat
                                $dataNotif9 = [
                                    "subject" => "⏳Masa Sewa Alat Olahraga Sudah Selesai!⏳",
                                    "judul" => "Masa Sewa Alat Olahraga Sudah Selesai!",
                                    "nama_user" => $dataPem->nama_pemilik,
                                    "url" => "https://sportiva.my.id/pemilik/permintaan/detailPermintaanNego/".$value->id_permintaan,
                                    "button" => "Lihat Detail Permintaan",
                                    "isi" => "Masa sewa alat dari:<br><br>
                                            <b>Nama Alat Olahraga: ".$dataAla->nama_alat."</b><br>
                                            <b>Di Lapangan Olahraga: ".$dataLapa->nama_lapangan."</b><br><br>
                                            Sudah selesai. Silahkan ambil alat olahragamu dan sewakan ditempat lain! Terima kasih telah mempercayai Sportiva! 😊"
                                ];
                                $e = new notifikasiEmail();
                                $e->sendEmail($dataPem->email_pemilik, $dataNotif9);

                            }
                            else if ($value->status_permintaan == "Menunggu" || $value->status_permintaan == "Diterima") {
                                $data40 = [
                                    "id" => $value->id_permintaan,
                                    "status" => "Dibatalkan"
                                ];
                                $permin = new requestPermintaan();
                                $permin->updateStatus($data40);

                                //kasih notif ke pemilik alat
                                $dataNotif9 = [
                                    "subject" => "⏳Masa Sewa Alat Olahraga Telah Dibatalkan!⏳",
                                    "judul" => "Masa Sewa Alat Olahraga Telah Dibatalkan!",
                                    "nama_user" => $dataPem->nama_pemilik,
                                    "url" => "https://sportiva.my.id/pemilik/permintaan/detailPermintaanNego/".$value->id_permintaan,
                                    "button" => "Lihat Detail Permintaan",
                                    "isi" => "Masa sewa alat dari:<br><br>
                                            <b>Nama Alat Olahraga: ".$dataAla->nama_alat."</b><br>
                                            <b>Di Lapangan Olahraga: ".$dataLapa->nama_lapangan."</b><br><br>
                                            Telah Dibatalkan. Silahkan ambil alat olahragamu dan sewakan ditempat lain! Terima kasih telah mempercayai Sportiva! 😊"
                                ];
                                $e = new notifikasiEmail();
                                $e->sendEmail($dataPem->email_pemilik, $dataNotif9);
                            }
                        }
                    }
                    if (!$pen->isEmpty()) {
                        foreach ($pen as $key => $value) {
                            $dataPem2 = DB::table('pemilik_alat')->where("id_pemilik","=",$value->fk_id_pemilik)->get()->first();
                            $dataAla2 = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                            $dataLapa2 = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();
                            if ($value->status_penawaran == "Disewakan") {
                                $data5 = [
                                    "id" => $value->id_penawaran,
                                    "status" => "Selesai"
                                ];
                                $pena = new requestPenawaran();
                                $pena->updateStatus($data5);
                                //ga ada penambahan dana tempat, wong dihapus akun e

                                //kasih notif ke pemilik alat
                                $dataNotif10 = [
                                    "subject" => "⏳Masa Sewa Alat Olahraga Sudah Selesai!⏳",
                                    "judul" => "Masa Sewa Alat Olahraga Sudah Selesai!",
                                    "nama_user" => $dataPem2->nama_pemilik,
                                    "url" => "https://sportiva.my.id/pemilik/penawaran/detailPenawaranNego/".$value->id_penawaran,
                                    "button" => "Lihat Detail Penawaran",
                                    "isi" => "Masa sewa alat dari:<br><br>
                                            <b>Nama Alat Olahraga: ".$dataAla2->nama_alat."</b><br>
                                            <b>Di Lapangan Olahraga: ".$dataLapa2->nama_lapangan."</b><br><br>
                                            Sudah selesai. Silahkan ambil alat olahragamu dan sewakan ditempat lain! Terima kasih telah mempercayai Sportiva! 😊"
                                ];
                                $e = new notifikasiEmail();
                                $e->sendEmail($dataPem2->email_pemilik, $dataNotif10);
                            }
                            else if ($value->status_penawaran == "Menunggu" || $value->status_penawaran == "Diterima") {
                                $data5 = [
                                    "id" => $value->id_penawaran,
                                    "status" => "Dibatalkan"
                                ];
                                $pena = new requestPenawaran();
                                $pena->updateStatus($data5);

                                //kasih notif ke pemilik alat
                                $dataNotif10 = [
                                    "subject" => "⏳Masa Sewa Alat Olahraga Telah Dibatalkan!⏳",
                                    "judul" => "Masa Sewa Alat Olahraga Telah Dibatalkan!",
                                    "nama_user" => $dataPem2->nama_pemilik,
                                    "url" => "https://sportiva.my.id/pemilik/penawaran/detailPenawaranNego/".$value->id_penawaran,
                                    "button" => "Lihat Detail Penawaran",
                                    "isi" => "Masa sewa alat dari:<br><br>
                                            <b>Nama Alat Olahraga: ".$dataAla2->nama_alat."</b><br>
                                            <b>Di Lapangan Olahraga: ".$dataLapa2->nama_lapangan."</b><br><br>
                                            Telah Dibatalkan. Silahkan ambil alat olahragamu dan sewakan ditempat lain! Terima kasih telah mempercayai Sportiva! 😊"
                                ];
                                $e = new notifikasiEmail();
                                $e->sendEmail($dataPem2->email_pemilik, $dataNotif10);
                            }
                        }
                    }

                    //batalkan semua transaksi yang "Menunggu"/"Diterima" & balikin semua dana cust
                    $trans = DB::table('htrans')->where("fk_id_tempat","=",$array[0])->get();
                    if (!$trans->isEmpty()) {
                        foreach ($trans as $key => $value) {
                            if ($value->status_trans == "Menunggu" || $value->status_trans == "Diterima") {
                                $data6 = [
                                    "id" => $value->id_htrans,
                                    "status" => "Dibatalkan"
                                ];
                                $ht = new htrans();
                                $ht->updateStatus($data6);

                                $dataLapa3 = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->fk_id_lapangan)->get()->first();
                                $dataCust1 = DB::table('user')->where("id_user","=",$value->fk_id_user)->get()->first();
                                $saldoCust1 = (int)$this->decodePrice($dataCust1->saldo_user, "mysecretkey");

                                $saldoCust1 += $value->total_trans;

                                $enkrip2 = $this->encodePrice((string)$saldoCust1, "mysecretkey");
                                $dataSaldo2 = [
                                    "id" => $value->fk_id_user,
                                    "saldo" => $enkrip2
                                ];
                                $Cuzz = new customer();
                                $Cuzz->updateSaldo($dataSaldo2);

                                //kasih notif ke cust
                                $tanggalAwal4 = $value->tanggal_sewa;
                                $tanggalObjek4 = DateTime::createFromFormat('Y-m-d', $tanggalAwal4);
                                $tanggalBaru4 = $tanggalObjek4->format('d-m-Y');
                                $dataNotif11 = [
                                    "subject" => "😔Booking Lapangan ".$dataLapa3->nama_lapangan." Telah Dibatalkan!😔",
                                    "judul" => "Booking Lapangan ".$dataLapa3->nama_lapangan." Telah Dibatalkan!",
                                    "nama_user" => $dataCust1->nama_user,
                                    "url" => "https://sportiva.my.id/customer/daftarRiwayat",
                                    "button" => "Lihat Riwayat Transaksi",
                                    "isi" => "Detail Sewa Lapangan:<br><br>
                                            <b>Nama Lapangan Olahraga: ".$dataLapa3->nama_lapangan."</b><br>
                                            <b>Tanggal Sewa: ".$tanggalBaru4."</b><br>
                                            <b>Jam Sewa: ".$value->jam_sewa." WIB - ".\Carbon\Carbon::parse($value->jam_sewa)->addHours($value->durasi_sewa)->format('H:i:s')." WIB</b><br><br>
                                            Telah dibatalkan, dana anda telah kami kembalikan ke saldo wallet! Terus jaga kesehatanmu bersama Sportiva! 😊"
                                ];
                                $e = new notifikasiEmail();
                                $e->sendEmail($dataCust1->email_user, $dataNotif11);
                            }
                        }
                    }

                    //kasih notif ke tempat, akunnya, request, produk semua dihapus
                    $dataTemp2 = DB::table('pihak_tempat')->where("id_tempat","=",$array[0])->get()->first();
                    $dataNotif12= [
                        "subject" => "😔Akun Anda Telah Dihapus!😔",
                        "judul" => "Maaf! Akun Anda Telah Dihapus!",
                        "nama_user" => $dataTemp2->nama_tempat,
                        "url" => "https://sportiva.my.id/registerTempat",
                        "button" => "Silahkan Register Lagi",
                        "isi" => "Detail Akun:<br><br>
                                <b>Nama Akun: ".$dataTemp2->nama_tempat."</b><br>
                                <b>Email Akun: ".$dataTemp2->email_tempat."</b><br><br>
                                Telah dihapus sebagai penanganan komplain yang diajukan! Semua transaksi yang sudah diterima dan menunggu konfirmasi akan otomatis dibatalkan. Gunakan email yang berbeda untuk melakukan pendaftaran lagi! 😊"
                    ];
                    $e = new notifikasiEmail();
                    $e->sendEmail($dataTemp2->email_tempat, $dataNotif12);

                    $temp = new pihakTempat();
                    $temp->softDelete($data4);

                    $penanganan .= "Hapus Tempat,";
                }
                else if ($array[1] == "pemilik") {
                    //hapus semua produknya
                    $dataAla3 = DB::table('alat_olahraga')->where("fk_id_pemilik","=",$array[0])->get();

                    date_default_timezone_set("Asia/Jakarta");
                    $tanggal = date("Y-m-d H:i:s");
                    
                    if (!$dataAla3->isEmpty()) {
                        foreach ($dataAla3 as $key => $value) {
                            //hapus dtransnya (status htrans menunggu / diterima)
                            $dtr = DB::table('dtrans')
                                ->select("dtrans.id_dtrans", "htrans.fk_id_user","htrans.kode_trans")
                                ->join("htrans", "dtrans.fk_id_htrans", "=", "htrans.id_htrans")
                                ->where("dtrans.deleted_at", "=", null)
                                ->where(function ($query) {
                                    $query->where("htrans.status_trans", "=", "Menunggu")
                                        ->orWhere("htrans.status_trans", "=", "Diterima");
                                })
                                ->where("dtrans.fk_id_alat", "=", $value->id_alat)
                                ->get();
                            if (!$dtr->isEmpty()) {
                                foreach ($dtr as $key => $value2) {
                                    $dataCust2 = DB::table('user')->where("id_user","=",$value2->fk_id_user)->get()->first();
                                    //balikin dananya
                                    $saldoCust2 = (int)$this->decodePrice($dataCust2->saldo_user, "mysecretkey");
                                    $saldoCust2 += $value2->subtotal_alat;
                                    $enkrip3 = $this->encodePrice((string)$saldoCust2, "mysecretkey");

                                    $dataSaldo4 = [
                                        "id" => $value2->fk_id_user,
                                        "saldo" => $enkrip3
                                    ];
                                    $cust = new customer();
                                    $cust->updateSaldo($dataSaldo4);

                                    $data8 = [
                                        "id" => $value2->id_dtrans,
                                        "tanggal" => $tanggal
                                    ];
                                    $dtra = new dtrans();
                                    $dtra->softDelete($data8);

                                    //kasih notif cust
                                    $dataAl = DB::table('alat_olahraga')->where("id_alat","=",$value2->fk_id_alat)->get()->first();
                                    $dataNotif13= [
                                        "subject" => "😔Alat Olahraga yang Anda Sewa Dibatalkan!😔",
                                        "judul" => "Maaf! Alat Olahraga yang Anda Sewa Dibatalkan!",
                                        "nama_user" => $dataCust2->nama_user,
                                        "url" => "https://sportiva.my.id/customer/daftarRiwayat",
                                        "button" => "Lihat Riwayat Transaksi",
                                        "isi" => "Detail Transaksi:<br><br>
                                                <b>Nama Alat Olahraga: ".$dataAl->nama_alat."</b><br>
                                                <b>yang Disewa pada Transaksi: ".$value2->kode_trans."</b><br><br>
                                                Telah dibatalkan! Jangan Khawatir, dana telah kami kembalikan ke saldo anda! 😊"
                                    ];
                                    $e = new notifikasiEmail();
                                    $e->sendEmail($dataCust2->email_user, $dataNotif13);
                                }
                            }
                            $data7 = [
                                "id" => $value->id_alat,
                                "tanggal" => $tanggal
                            ];
                            $ala = new alatOlahraga();
                            $ala->softDelete($data7);
                        }
                    }

                    //selesaikan semua request yg berhubungan dan masukkan total komisi ke saldo tempat
                    //(disewakan = selesai, menunggu/diterima = dibatalkan)
                    $per = DB::table('request_permintaan')->where("fk_id_pemilik","=",$array[0])->get();
                    $pen = DB::table('request_penawaran')->where("fk_id_pemilik","=",$array[0])->get();
                    if (!$per->isEmpty()) {
                        foreach ($per as $key => $value) {
                            $dataTemp3 = DB::table('pihak_tempat')->where("id_tempat","=",$value->fk_id_tempat)->get()->first();
                            $dataAla4 = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                            $dataLapa4 = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();
                            if ($value->status_permintaan == "Menunggu" || $value->status_permintaan == "Diterima") {
                                $data9 = [
                                    "id" => $value->id_permintaan,
                                    "status" => "Dibatalkan"
                                ];
                                $pena = new requestPenawaran();
                                $pena->updateStatus($data9);

                                //kasih notif ke tempat, request dibatalkan
                                $dataNotif14= [
                                    "subject" => "⚠️Request Anda Telah Dibatalkan!⚠️",
                                    "judul" => "Maaf! Request Anda Telah Dibatalkan!",
                                    "nama_user" => $dataTemp3->nama_tempat,
                                    "url" => "https://sportiva.my.id/tempat/permintaan/detailPermintaanNego/".$value->id_permintaan,
                                    "button" => "Lihat Detail Permintaan",
                                    "isi" => "Sayang sekali! Request Permintaan dari:<br><br>
                                            <b>Nama Alat Olahraga: ".$dataAla4->nama_alat."</b><br>
                                            <b>Diantar ke Lapangan: ".$dataLapa4->nama_lapangan."</b><br><br>
                                            Telah dibatalkan! Cari dan temukan alat olarhaga lain di Sportiva! 😊"
                                ];
                                $e = new notifikasiEmail();
                                $e->sendEmail($dataTemp3->email_tempat, $dataNotif14);
                            }
                            else if ($value->status_permintaan == "Disewakan") {
                                //tambahkan saldo tempat
                                $saldoTempat1 = (int)$this->decodePrice($dataTemp3->saldo_tempat, "mysecretkey");

                                $transaksi = DB::table('dtrans')
                                            ->select("dtrans.total_komisi_tempat", "extend_dtrans.total_komisi_tempat as total_ext")
                                            ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
                                            ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                                            ->where("dtrans.fk_id_alat","=",$value->req_id_alat)
                                            ->where("htrans.fk_id_tempat","=",$value->fk_id_tempat)
                                            ->where("htrans.status_trans","=","Selesai")
                                            ->where("dtrans.deleted_at","=",null)
                                            ->get();
                                $total = 0;
                                if (!$transaksi->isEmpty()) {
                                    foreach ($transaksi as $key => $value2) {
                                        if ($value2->total_ext != null) {
                                            $total += $value2->total_komisi_tempat + $value2->total_ext;
                                        }
                                        else {
                                            $total += $value2->total_komisi_tempat;
                                        }
                                    }
                                }

                                $saldoTempat1 += $total;
                                $enkrip4 = $this->encodePrice((string)$saldoTempat1, "mysecretkey");
                                
                                $temp = new pihakTempat();
                                $dataSaldo5 = [
                                    "id" => $value->fk_id_tempat,
                                    "saldo" => $enkrip4
                                ];
                                $temp->updateSaldo($dataSaldo5);
                                
                                //kasih notif ke tempat, request selesai
                                $dataNotif14= [
                                    "subject" => "⏳Masa Sewa Alat Olahraga Sudah Selesai!⏳",
                                    "judul" => "Masa Sewa Alat Olahraga Sudah Selesai!",
                                    "nama_user" => $dataTemp3->nama_tempat,
                                    "url" => "https://sportiva.my.id/tempat/permintaan/detailPermintaanNego/".$value->id_permintaan,
                                    "button" => "Lihat Detail Permintaan",
                                    "isi" => "Masa sewa alat dari:<br><br>
                                    <b>Nama Alat Olahraga: ".$dataAla4->nama_alat."</b><br>
                                    <b>Di Lapangan Olahraga: ".$dataLapa4->nama_lapangan."</b><br><br>
                                    Sudah selesai. Tunggu pemilik alat olahraga mengambil alatnya ya! Terima kasih telah mempercayai Sportiva! 😊"
                                ];
                                $e = new notifikasiEmail();
                                $e->sendEmail($dataTemp3->email_tempat, $dataNotif14);

                                $data9 = [
                                    "id" => $value->id_permintaan,
                                    "status" => "Selesai"
                                ];
                                $pena = new requestPenawaran();
                                $pena->updateStatus($data9);
                            }
                        }
                    }

                    //kasih notif ke pemilik, akunnya, produk, request semua dihapus
                    $dataPemi3 = DB::table('pemilik_alat')->where("id_pemilik","=",$array[0])->get()->first();
                    $dataNotif15 = [
                        "subject" => "😔Akun Anda Telah Dihapus!😔",
                        "judul" => "Maaf! Akun Anda Telah Dihapus!",
                        "nama_user" => $dataPemi3->nama_pemilik,
                        "url" => "https://sportiva.my.id/registerTempat",
                        "button" => "Silahkan Register Lagi",
                        "isi" => "Detail Akun:<br><br>
                                    <b>Nama Akun: ".$dataPemi3->nama_pemilik."</b><br>
                                    <b>Email Akun: ".$dataPemi3->email_pemilik."</b><br><br>
                                    Telah dihapus sebagai penanganan komplain yang diajukan! Semua request yang sudah diterima dan menunggu konfirmasi akan otomatis dibatalkan. Gunakan email yang berbeda untuk melakukan pendaftaran lagi! 😊"
                    ];
                    $e = new notifikasiEmail();
                    $e->sendEmail($dataPemi3->email_pemilik, $dataNotif15);

                    $pemi = new pemilikAlat();
                    $pemi->softDelete($data4);

                    $penanganan .= "Hapus Pemilik,";
                }
            }
            else {
                return redirect()->back()->with("error", "akun yang akan dinonaktifkan tidak boleh kosong!");
            }
        }

        $penanganan .= "Pembatalan Transaksi oleh Admin";

        //pengembalian dana ke cust
        $htrans = DB::table('htrans')->where("id_htrans","=",$request->id_htrans)->get()->first();
        $komplain = DB::table('komplain_trans')->where("id_komplain_trans","=",$request->id_komplain)->get()->first();
        $user = DB::table('user')->where("id_user","=",$komplain->fk_id_user)->get()->first();

        $saldo = (int)$this->decodePrice($user->saldo_user, "mysecretkey");
        $saldo += (int)$htrans->total_trans;
        // dd($saldo);

        //enkripsi kembali saldo
        $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

        //update db user
        $dataSaldo = [
            "id" => $user->id_user,
            "saldo" => $enkrip
        ];
        $cust = new customer();
        $cust->updateSaldo($dataSaldo);

        //status transaksi menjadi dibatalkan
        $data5 = [
            "id" => $request->id_htrans,
            "status" => "Dibatalkan"
        ];
        $trans = new htrans();
        $trans->updateStatus($data5);

        //ganti status komplain menjadi "Diterima"
        $data6 = [
            "id" => $request->id_komplain,
            "status" => "Diterima"
        ];
        $komp = new ModelsKomplainTrans();
        $komp->updateStatus($data6);

        //isi penanganan_komplain di db
        $data7 = [
            "id" => $request->id_komplain,
            "penanganan" => $penanganan
        ];
        $penang = new ModelsKomplainTrans();
        $penang->updatePenanganan($data7);

        //notif ke cust
        $tanggalAwal = $komplain->waktu_komplain;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');

        $dataNotif = [
            "subject" => "🎉Komplain Transaksi Anda Telah Diterima!🎉",
            "judul" => "Komplain Transaksi Anda Telah Diterima!",
            "nama_user" => $user->nama_user,
            "url" => "https://sportiva.my.id/customer/daftarKomplain",
            "button" => "Lihat Komplain",
            "isi" => "Yeay! Komplain Transaksi yang Anda ajukan telah diterima Admin:<br><br>
                    <b>Jenis Komplain: ".$komplain->jenis_komplain."</b><br>
                    <b>Diajukan pada: ".$tanggalBaru."</b><br><br>
                    Komplain ini telah disetujui Admin dengan penanganan berupa ".$penanganan."!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($user->email_user,$dataNotif);

        //notif ke tempat
        $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$htrans->fk_id_tempat)->get()->first();
        $lap = DB::table('lapangan_olahraga')->where("id_lapangan","=",$htrans->fk_id_lapangan)->get()->first();
        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$htrans->id_htrans)->where("deleted_at","=",null)->get();

        $dtransStr = "";
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->fk_id_alat)->get()->first();
                $dtransStr .= "<b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>";
            }
        }

        $dataNotif2 = [
            "subject" => "⚠️Komplain dari Customer Telah Diterima Admin!⚠️",
            "judul" => "Komplain Transaksi dari Customer Telah Diterima Admin!",
            "nama_user" => $tempat->nama_tempat,
            "url" => "https://sportiva.my.id/tempat/transaksi/detailTransaksi/".$request->id_htrans,
            "button" => "Lihat Transaksi",
            "isi" => "Komplain Transaksi dari:<br><br>
                    <b>Kode Transaksi: ".$htrans->kode_trans."</b><br>
                    <b>Lapangan Olahraga: ".$lap->nama_lapangan."</b><br>
                    ".$dtransStr."<br><br>
                    Komplain ini telah disetujui Admin dan dana transaksi telah dikembalikan sepenuhnya kepada customer!"
        ];
        $e2 = new notifikasiEmail();
        $e2->sendEmail($tempat->email_tempat,$dataNotif2);

        return redirect()->back()->with("success", "Berhasil menangani komplain!");
    }

    public function tolakKomplain(Request $request) {
        // dd($request->alasan);
        $data = [
            "id" => $request->id,
            "status" => "Ditolak"
        ];
        $komp = new ModelsKomplainTrans();
        $komp->updateStatus($data);

        $data2 = [
            "id" => $request->id,
            "alasan" => $request->alasan
        ];
        $komp->updateAlasan($data2);

        //ubah status transaksi menjadi "Selesai" dan masukkan uang ke saldo pemilik alat dan pihak tempat
        $data3 = [
            "id" => $request->id2,
            "status" => "Selesai"
        ];
        $trans = new htrans();
        $trans->updateStatus($data3);

        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$request->id2)->get()->first();
        $extend = DB::table('extend_htrans')->where("fk_id_htrans","=",$dataHtrans->id_htrans)->get()->first();
        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$request->id2)->where("deleted_at","=",null)->get();

        //total komisi dari alat miliknya sendiri
        $total_komisi_tempat = 0;
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                if ($value->fk_id_tempat == $dataHtrans->fk_id_tempat) {
                    $extend_dtrans = DB::table('extend_dtrans')->where("fk_id_dtrans","=",$value->id_dtrans)->get()->first();
                    if ($extend_dtrans != null) {
                        $total_komisi_tempat += $extend_dtrans->total_komisi_tempat;
                    }
                    $total_komisi_tempat += $value->total_komisi_tempat;
                }
            }
        }

        $extend_subtotal = 0;
        $extend_total = 0;
        if ($extend != null) {
            $extend_subtotal = $extend->subtotal_lapangan;
            $extend_total = $extend->total;
        }

        //subtotal lapangan masuk ke saldo tempat & total komisi tempat ditahan dulu
        //klo masa sewa sdh selesai baru dimasukin saldo tempat
        $temp = DB::table('pihak_tempat')->where("id_tempat","=",$dataHtrans->fk_id_tempat)->get()->first();
        $saldo = (int)$this->decodePrice($temp->saldo_tempat, "mysecretkey");
        // dd($extend->subtotal_lapangan);
        $saldo += (int)$dataHtrans->subtotal_lapangan + $extend_subtotal + $total_komisi_tempat - $dataHtrans->pendapatan_website_lapangan;

        // dd($dataHtrans->subtotal_lapangan + $extend_subtotal + $total_komisi_tempat);
        //enkripsi kembali saldo
        $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

        //update db tempat
        $dataSaldo = [
            "id" => $dataHtrans->fk_id_tempat,
            "saldo" => $enkrip
        ];
        $temp = new pihakTempat();
        $temp->updateSaldo($dataSaldo);

        // total komisi pemilik masuk ke saldo pemilik
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                if ($value->fk_id_pemilik != null) {
                    $extend_dtrans2 = DB::table('extend_dtrans')->where("fk_id_dtrans","=",$value->id_dtrans)->get()->first();

                    $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$value->fk_id_pemilik)->get()->first();
                    // dd($value->total_komisi_pemilik);

                    $saldo2 = (int)$this->decodePrice($pemilik->saldo_pemilik, "mysecretkey");
                    // dd($saldo2);
                    $extend_total_komisi = 0;
                    if ($extend_dtrans2 != null) {
                        $extend_total_komisi = $extend_dtrans2->total_komisi_pemilik - $extend_dtrans2->pendapatan_website_alat;
                    }
                    $saldo2 += (int)$value->total_komisi_pemilik - $value->pendapatan_website_alat + $extend_total_komisi;
                    // dd($saldo2);

                    $enkrip2 = $this->encodePrice((string)$saldo2, "mysecretkey");

                    //update db
                    $dataSaldo2 = [
                        "id" => $value->fk_id_pemilik,
                        "saldo" => $enkrip2
                    ];
                    $pem = new pemilikAlat();
                    $pem->updateSaldo($dataSaldo2);
                }
            }
        }

        //notif ke cust
        $komplain = DB::table('komplain_trans')->where("id_komplain_trans","=",$request->id)->get()->first();
        $user = DB::table('user')->where("id_user","=",$komplain->fk_id_user)->get()->first();

        $tanggalAwal = $komplain->waktu_komplain;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');

        $dataNotif = [
            "subject" => "😔Komplain Transaksi Anda Telah Ditolak!😔",
            "judul" => "Komplain Transaksi Anda Telah Ditolak!",
            "nama_user" => $user->nama_user,
            "url" => "https://sportiva.my.id/customer/daftarKomplain",
            "button" => "Lihat Komplain",
            "isi" => "Maaf! Komplain Transaksi yang Anda ajukan belum bisa kami terima.<br><br>
                    <b>Jenis Komplain: ".$komplain->jenis_komplain."</b><br>
                    <b>Diajukan pada: ".$tanggalBaru."</b><br><br>
                    Komplain kami tolak karena ".$request->alasan."<br>
                    Kami menghargai umpan balik Anda! Kami akan berusaha lebih baik di masa depan. Terima kasih atas pengertiannya!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($user->email_user,$dataNotif);

        return redirect()->back()->with("success", "Berhasil menolak komplain!");
    }

    public function daftarKomplain() {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $kot = new lapanganOlahraga();
        $param["kota"] = $kot->get_kota();
        
        $komplain = DB::table('komplain_trans')
                    ->select("komplain_trans.id_komplain_trans","htrans.kode_trans","files_komplain_trans.nama_file_komplain","komplain_trans.jenis_komplain","komplain_trans.keterangan_komplain","komplain_trans.waktu_komplain","komplain_trans.status_komplain","komplain_trans.penanganan_komplain","komplain_trans.alasan_komplain")
                    ->join("htrans","komplain_trans.fk_id_htrans","=","htrans.id_htrans")
                    ->joinSub(function($query) {
                        $query->select("fk_id_komplain_trans", "nama_file_komplain")
                            ->from('files_komplain_trans')
                            ->whereRaw('id_file_komplain_trans = (select min(id_file_komplain_trans) from files_komplain_trans as f2 where f2.fk_id_komplain_trans = files_komplain_trans.fk_id_komplain_trans)');
                    }, 'files_komplain_trans', 'komplain_trans.id_komplain_trans', '=', 'files_komplain_trans.fk_id_komplain_trans')
                    ->where("komplain_trans.fk_id_user","=",Session::get("dataRole")->id_user)
                    ->orderBy("komplain_trans.waktu_komplain","desc")
                    ->get();
        
        $param["komplain"] = $komplain;
        return view("customer.daftarKomplain")->with($param);
    }
}
