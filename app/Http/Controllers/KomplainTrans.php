<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\customer;
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
                    $alat = new alatOlahraga();
                    $alat->softDelete($data3);

                    $penanganan .= "Hapus Alat,";

                    //buat status request alat jd selesai
                    $per = DB::table('request_permintaan')->where("req_id_alat","=",$array[0])->where("status_permintaan","=","Disewakan")->get();
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
                            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$array[0])->get()->first();
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
                    else {
                        $pen = DB::table('request_penawaran')->where("req_id_alat","=",$array[0])->where("status_penawaran","=","Disewakan")->get();
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
                                // dd($value->fk_id_tempat);
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
                    }

                    //hapus dtrans yang berhubungan dengan alat ini & balikin dana cust (status htrans menunggu / diterima) ???
                    //kirim notif ke cust

                    //kirim notif ke pemilik, alatnya dihapus
                }
                else if ($array[1] == "lapangan") {
                    $lapangan = new lapanganOlahraga();
                    $lapangan->softDelete($data3);

                    $penanganan .= "Hapus Lapangan,";

                    //buat status request jd selesai
                    $per = DB::table('request_permintaan')->where("req_lapangan","=",$array[0])->where("status_permintaan","=","Disewakan")->get();
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
                    else {
                        $pen = DB::table('request_penawaran')->where("req_lapangan","=",$array[0])->where("status_penawaran","=","Disewakan")->get();
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
                    }
                }

                //batalkan htrans yang berhubungan dengan alat ini & balikin seluruh dana cust ???
                //kasih notif ke cust

                //kasih notif ke tempat, lapangannya dihapus
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
                    //hapus semua produknya ???

                    //selesai semua request yg berhubungan (ga masuk saldo, wong akun e dihapus)
                    //(disewakan = selesai, menunggu/diterima = dibatalkan)
                    //kirim notif ke pemilik, request selesai

                    //batalkan semua transaksi yang "Menunggu" & balikin semua dana cust

                    //kasih notif ke tempat, akunnya, request, produk semua dihapus

                    $temp = new pihakTempat();
                    $temp->softDelete($data4);

                    $penanganan .= "Hapus Tempat,";
                }
                else if ($array[1] == "pemilik") {
                    //hapus semua produknya???
                    //hapus dtrans produknya & balikin dana dtrans ke cust (status htrans menunggu / diterima)
                    //kasih notif cust

                    //selesaikan semua request yg berhubungan dan masukkan total komisi ke saldo tempat
                    //(disewakan = selesai, menunggu/diterima = dibatalkan)

                    //kasih notif ke pemilik, akunnya, produk, request semua dihapus

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
