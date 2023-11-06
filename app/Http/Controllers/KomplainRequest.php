<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\customer;
use App\Models\dtrans;
use App\Models\filesKomplainReq;
use App\Models\htrans;
use App\Models\komplainRequest as ModelsKomplainRequest;
use App\Models\lapanganOlahraga;
use App\Models\pemilikAlat;
use App\Models\pihakTempat;
use App\Models\requestPenawaran;
use App\Models\requestPermintaan;
use Illuminate\Http\Request;
use App\Models\notifikasiEmail;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class KomplainRequest extends Controller
{
    public function tambahKomplain(Request $request){
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

        if (Session::get("role") == "pemilik") {
            $pemilik = Session::get("dataRole")->id_pemilik;

            if ($request->jenis_request == "Permintaan") {
                $data = [
                    "jenis" => $request->jenis,
                    "keterangan" => $request->keterangan,
                    "permintaan" => $request->fk_id_request,
                    "penawaran" => null,
                    "waktu" => $tgl_komplain,
                    "pemilik" => $pemilik,
                    "tempat" => null
                ];
            }
            else {
                $data = [
                    "jenis" => $request->jenis,
                    "keterangan" => $request->keterangan,
                    "permintaan" => null,
                    "penawaran" => $request->fk_id_request,
                    "waktu" => $tgl_komplain,
                    "pemilik" => $pemilik,
                    "tempat" => null
                ];
            }
        }
        else {
            $pemilik = Session::get("dataRole")->id_tempat;

            if ($request->jenis_request == "Permintaan") {
                $data = [
                    "jenis" => $request->jenis,
                    "keterangan" => $request->keterangan,
                    "permintaan" => $request->fk_id_request,
                    "penawaran" => null,
                    "waktu" => $tgl_komplain,
                    "pemilik" => null,
                    "tempat" => $pemilik
                ];
            }
            else {
                $data = [
                    "jenis" => $request->jenis,
                    "keterangan" => $request->keterangan,
                    "permintaan" => null,
                    "penawaran" => $request->fk_id_request,
                    "waktu" => $tgl_komplain,
                    "pemilik" => null,
                    "tempat" => $pemilik
                ];
            }
        }
        $komp = new ModelsKomplainRequest();
        $id = $komp->insertKomplainReq($data);
        
        //insert foto alatnya
        $destinasi = "/upload";
        foreach ($request->foto as $key => $value) {
            $foto = uniqid().".".$value->getClientOriginalExtension();
            $value->move(public_path($destinasi),$foto);
            $data2 = [
                "nama"=>$foto,
                "fk"=>$id
            ];
            $file = new filesKomplainReq();
            $file->insertFilesKomplainReq($data2);
        }

        $jenis = "";
        //mengubah status request menjadi "Dikomplain"
        if ($request->jenis_request == "Permintaan") {
            $data3 = [
                "id" => $request->fk_id_request,
                "status" => "Dikomplain"
            ];
            $per = new requestPermintaan();
            $per->updateStatus($data3);

            $jenis = "Permintaan";
        }
        else if ($request->jenis_request == "Penawaran") {
            $data3 = [
                "id" => $request->fk_id_request,
                "status" => "Dikomplain"
            ];
            $pen = new requestPenawaran();
            $pen->updateStatus($data3);

            $jenis = "Penawaran";
        }

        //kirim notif ke admin
        $namaUser = "";
        if (Session::get("role") == "pemilik") {
            $namaUser = Session::get("dataRole")->nama_pemilik;
        }
        else {
            $namaUser = Session::get("dataRole")->nama_tempat;
        }

        $tanggalAwal = $tgl_komplain;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');

        $dataNotif = [
            "subject" => "❗❗ Komplain ".$jenis." Baru ❗❗",
            "judul" => "Komplain ".$jenis." Baru dari ".$namaUser,
            "nama_user" => "Admin",
            "url" => "https://sportiva.my.id/admin/komplain/request/detailKomplain/".$id,
            "button" => "Lihat Detail Komplain",
            "isi" => "Anda memiliki satu komplain ".$jenis." baru:<br><br>
                    <b>Diajukan oleh: ".$namaUser."</b><br>
                    <b>Diajukan pada: ".$tanggalBaru."</b><br>
                    <b>Jenis Komplain: ".$request->jenis."</b><br><br>
                    Mohon segera masuk dan tangani komplain ini untuk meningkatkan kepuasan pengguna!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail("admin@gmail.com",$dataNotif);

        return redirect()->back()->with("success", "Berhasil Mengajukan Komplain!");
    }

    public function terimaKomplain(Request $request) {
        // Pengecekan checkbox pertama
        $komplain = DB::table('komplain_request')->where("id_komplain_req","=",$request->id_komplain)->get()->first();
        $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$komplain->fk_id_pemilik)->get()->first();

        $penanganan = "";
        if ($request->has('pengembalianCheckbox')) {
            if ($request->produk != "") {
                $array = explode("-", $request->produk);

                date_default_timezone_set("Asia/Jakarta");
                $tanggal = date("Y-m-d H:i:s");
                
                $data = [
                    "id" => $array[0],
                    "tanggal" => $tanggal
                ];

                if ($array[1] == "alat") {
                    //kasih notif ke pemilik, alatnya dihapus
                    $alat = DB::table('alat_olahraga')->where("id_alat","=",$array[0])->get()->first();
                    $dataNotif4 = [
                        "subject" => "😔Alat Olahraga Anda Telah Dihapus!😔",
                        "judul" => "Alat Olahraga Anda Telah Dihapus!",
                        "nama_user" => $pemilik->nama_pemilik,
                        "url" => "https://sportiva.my.id/pemilik/daftarAlat",
                        "button" => "Lihat Daftar Alat Olahraga",
                        "isi" => "Maaf! Alat Olahraga:<br><br>
                                <b>Nama: ".$alat->nama_alat."</b><br>
                                <b>Komisi: ".$alat->komisi_alat."</b><br><br>
                                Telah dihapus sebagai penanganan komplain yang diajukan!"
                    ];
                    $e = new notifikasiEmail();
                    $e->sendEmail($pemilik->email_pemilik, $dataNotif4);

                    $alat = new alatOlahraga();
                    $alat->softDelete($data);

                    $penanganan = "Hapus Alat,";
                    //baru mau disewakan, blm ada dtrans baru
                }
                else if ($array[1] == "lapangan") {
                    //hapus semua request yg berhubungan
                    $per = DB::table('request_permintaan')->where("req_lapangan","=",$array[0])->get();
                    $pen = DB::table('request_penawaran')->where("req_lapangan","=",$array[0])->get();
                    if (!$per->isEmpty()) {
                        foreach ($per as $key => $value) {
                            $dataPemi = DB::table('pemilik_alat')->where("id_pemilik","=",$value->fk_id_pemilik)->get()->first();
                            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                            $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();
                            if ($value->status_permintaan == "Menunggu" || $value->status_permintaan == "Diterima") {
                                $data1 = [
                                    "id" => $value->id_permintaan,
                                    "status" => "Dibatalkan"
                                ];
                                $perm = new requestPermintaan();
                                $perm->updateStatus($data1);

                                //kirim notif ke pemilik
                                $dataNotif5 = [
                                    "subject" => "⚠️Request Alat Olahraga Dibatalkan!⚠️",
                                    "judul" => "Request Alat Olahraga Dibatalkan!",
                                    "nama_user" => $dataPemi->nama_pemilik,
                                    "url" => "https://sportiva.my.id/pemilik/permintaan/detailPermintaanNego/".$value->id_penawaran,
                                    "button" => "Lihat Detail Permintaan",
                                    "isi" => "Masa sewa alat dari:<br><br>
                                            <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                            <b>Di Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                            Telah Dibatalkan. Silahkan sewakan alat olahragamu di lapangan lain! 😊"
                                ];
                                $e = new notifikasiEmail();
                                $e->sendEmail($dataPemi->email_pemilik, $dataNotif5);
                            }
                            else if ($value->status_permintaan == "Disewakan") {
                                $data1 = [
                                    "id" => $value->id_permintaan,
                                    "status" => "Selesai"
                                ];
                                $perm = new requestPermintaan();
                                $perm->updateStatus($data1);

                                //masukkan dana ke saldo tempat
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

                                $dataSaldo3 = [
                                    "id" => $value->fk_id_tempat,
                                    "saldo" => $enkrip
                                ];
                                $temp = new pihakTempat();
                                $temp->updateSaldo($dataSaldo3);
                            }
                            //kasih notif ke pemilik, request e selesai
                            $dataNotif5 = [
                                "subject" => "⏳Masa Sewa Alat Olahraga Sudah Selesai!⏳",
                                "judul" => "Masa Sewa Alat Olahraga Sudah Selesai!",
                                "nama_user" => $dataPemi->nama_pemilik,
                                "url" => "https://sportiva.my.id/pemilik/permintaan/detailPermintaanNego/".$value->id_penawaran,
                                "button" => "Lihat Detail Permintaan",
                                "isi" => "Masa sewa alat dari:<br><br>
                                        <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                        <b>Di Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                        Sudah selesai. Silahkan ambil alat olahragamu dan sewakan ditempat lain! Terima kasih telah mempercayai Sportiva! 😊"
                            ];
                            $e = new notifikasiEmail();
                            $e->sendEmail($dataPemi->email_pemilik, $dataNotif5);
                        }
                    }
                    if (!$pen->isEmpty()) {
                        foreach ($pen as $key => $value) {
                            $dataPemi2 = DB::table('pemilik_alat')->where("id_pemilik","=",$value->fk_id_pemilik)->get()->first();
                            $dataAlat2 = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                            $dataLapangan2 = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();
                            if ($value->status_penawaran == "Menunggu" || $value->status_penawaran == "Diterima") {
                                $data4 = [
                                    "id" => $value->id_penawaran,
                                    "status" => "Dibatalkan"
                                ];
                                $penaw = new requestPenawaran();
                                $penaw->updateStatus($data4);

                                //kirim notif ke pemilik
                                $dataNotif5 = [
                                    "subject" => "⚠️Request Alat Olahraga Dibatalkan!⚠️",
                                    "judul" => "Request Alat Olahraga Dibatalkan!",
                                    "nama_user" => $dataPemi2->nama_pemilik,
                                    "url" => "https://sportiva.my.id/pemilik/permintaan/detailPermintaanNego/".$value->id_penawaran,
                                    "button" => "Lihat Detail Permintaan",
                                    "isi" => "Masa sewa alat dari:<br><br>
                                            <b>Nama Alat Olahraga: ".$dataAlat2->nama_alat."</b><br>
                                            <b>Di Lapangan Olahraga: ".$dataLapangan2->nama_lapangan."</b><br><br>
                                            Telah Dibatalkan. Silahkan sewakan alat olahragamu di lapangan lain! 😊"
                                ];
                                $e = new notifikasiEmail();
                                $e->sendEmail($dataPemi2->email_pemilik, $dataNotif5);
                            }
                            else if ($value->status_penawaran == "Disewakan") {
                                $data4 = [
                                    "id" => $value->id_penawaran,
                                    "status" => "Selesai"
                                ];
                                $penaw = new requestPenawaran();
                                $penaw->updateStatus($data4);

                                //masukkan dana ke saldo tempat
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

                                $dataSaldo3 = [
                                    "id" => $value->fk_id_tempat,
                                    "saldo" => $enkrip
                                ];
                                $temp = new pihakTempat();
                                $temp->updateSaldo($dataSaldo3);
                            }
                            //kasih notif ke pemilik, request e selesai
                            $dataNotif6 = [
                                "subject" => "⏳Masa Sewa Alat Olahraga Sudah Selesai!⏳",
                                "judul" => "Masa Sewa Alat Olahraga Sudah Selesai!",
                                "nama_user" => $dataPemi2->nama_pemilik,
                                "url" => "https://sportiva.my.id/pemilik/penawaran/detailPenawaranNego/".$value->id_penawaran,
                                "button" => "Lihat Detail Penawaran",
                                "isi" => "Masa sewa alat dari:<br><br>
                                        <b>Nama Alat Olahraga: ".$dataAlat2->nama_alat."</b><br>
                                        <b>Di Lapangan Olahraga: ".$dataLapangan2->nama_lapangan."</b><br><br>
                                        Sudah selesai. Silahkan ambil alat olahragamu dan sewakan ditempat lain! Terima kasih telah mempercayai Sportiva! 😊"
                            ];
                            $e = new notifikasiEmail();
                            $e->sendEmail($dataPemi2->email_pemilik, $dataNotif6);
                        }
                    }

                    //batalkan semua transaksi yang "Menunggu"/"Diterima" & kembalikan dana cust
                    $cekTrans = DB::table('htrans')->where("fk_id_lapangan","=",$array[0])->get();
                    if (!$cekTrans->isEmpty()) {
                        foreach ($cekTrans as $key => $value) {
                            $dataLap = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->fk_id_lapangan)->get()->first();
                            $dataTemp = DB::table('pihak_tempat')->where("id_tempat")->where("id_tempat","=",$value->fk_id_tempat)->get()->first();
                            if ($value->status_trans == "Menunggu" || $value->status_trans == "Diterima") {
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
                                $dataNotif7 = [
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
                                $e->sendEmail($dataCus->email_user, $dataNotif7);
                            }
                        }
                    }

                    //kasih notif ke pihak tempat, lapangan e dihapus, request semua dibatalkan
                    $dataNotif8 = [
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
                    $e->sendEmail($dataTemp->email_tempat, $dataNotif8);

                    $lapangan = new lapanganOlahraga();
                    $lapangan->softDelete($data);

                    $penanganan = "Hapus Lapangan,";
                }
            }
            else {
                return redirect()->back()->with("error", "produk yang akan dihapus tidak boleh kosong!");
            }
        }

        // Pengecekan checkbox kedua
        if ($request->has('pengembalianCheckbox2')) {
            if ($request->akun != "") {
                $array = explode("-", $request->akun);

                date_default_timezone_set("Asia/Jakarta");
                $tanggal = date("Y-m-d H:i:s");
                
                $data = [
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

                    //selesaikan semua request yg berhubungan
                    $per = DB::table('request_permintaan')->where("fk_id_tempat","=",$array[0])->get();
                    $pen = DB::table('request_penawaran')->where("fk_id_tempat","=",$array[0])->get();
                    if (!$per->isEmpty()) {
                        foreach ($per as $key => $value) {
                            $dataPem = DB::table('pemilik_alat')->where("id_pemilik","=",$value->fk_id_pemilik)->get()->first();
                            $dataAla = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                            $dataLapa = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();
                            if ($value->status_permintaan == "Disewakan") {
                                $data4 = [
                                    "id" => $value->id_permintaan,
                                    "status" => "Selesai"
                                ];
                                $permin = new requestPermintaan();
                                $permin->updateStatus($data4);
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
                                $e->sendEmail($dataPem->nama_pemilik, $dataNotif9);

                            }
                            else if ($value->status_permintaan == "Menunggu" || $value->status_permintaan == "Diterima") {
                                $data4 = [
                                    "id" => $value->id_permintaan,
                                    "status" => "Dibatalkan"
                                ];
                                $permin = new requestPermintaan();
                                $permin->updateStatus($data4);

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
                                $e->sendEmail($dataPem->nama_pemilik, $dataNotif9);
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
                                $e->sendEmail($dataPem2->nama_pemilik, $dataNotif10);
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
                                $e->sendEmail($dataPem2->nama_pemilik, $dataNotif10);
                            }
                        }
                    }

                    //batalkan semua transaksi yang "Menunggu" & kembalikan seluruh dana cust
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
                                $e->sendEmail($dataCust1->nama_user, $dataNotif11);
                            }
                        }
                    }

                    //kasih notif ke tempat, akun e dihapus, produk, request, transaksi semua dihapus
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
                    $temp->softDelete($data);

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
                                                <b>yang Disewa pada Transaksi: ".$value2."</b><br><br>
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

                    //kasih notif ke pemilik, akun e dihapus, produk, request semua dihapus
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
                    $pemi->softDelete($data);

                    $penanganan .= "Hapus Pemilik,";
                }
            }
            else {
                return redirect()->back()->with("error", "akun yang akan dinonaktifkan tidak boleh kosong!");
            }
        }

        $penanganan .= "Pembatalan Request";

        $data2 = [
            "id" => $request->id_request,
            "status" => "Dibatalkan"
        ];

        //pembatalan request otomatis
        if ($request->fk_id_permintaan != null) {
            $per = new requestPermintaan();
            $per->updateStatus($data2);
        }
        else if ($request->fk_id_penawaran != null) {
            $pen = new requestPenawaran();
            $pen->updateStatus($data2);
        }

        //ganti status komplain menjadi "Diterima"
        $data3 = [
            "id" => $request->id_komplain,
            "status" => "Diterima"
        ];
        $komp = new ModelsKomplainRequest();
        $komp->updateStatus($data3);

        //isi penanganan_komplain di db
        $data4 = [
            "id" => $request->id_komplain,
            "penanganan" => $penanganan
        ];
        $penang = new ModelsKomplainRequest();
        $penang->updatePenanganan($data4);

        //notif pemilik/tempat yg mengajukan

        $jenis = "";
        if ($komplain->fk_id_permintaan != null) {
            //jenis request permintaan
            $jenis = "Permintaan";
        }
        else {
            $jenis = "Penawaran";
        }

        $pengaju = "";
        $email = "";
        if ($komplain->fk_id_pemilik != null) {
            // yang mengajukan pemilik
            $pengaju = $pemilik->nama_pemilik;
            $email = $pemilik->email_pemilik;
            if ($jenis == "Permintaan") {
                $url = "https://sportiva.my.id/pemilik/permintaan/detailPermintaanNego/".$komplain->fk_id_permintaan;
            }
            else {
                $url = "https://sportiva.my.id/pemilik/penawaran/detailPenawaranNego/".$komplain->fk_id_penawaran;
            }
        }
        else {
            //yang mengajukan tempat
            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$komplain->fk_id_tempat)->get()->first();
            $pengaju = $tempat->nama_tempat;
            $email = $tempat->email_tempat;

            if ($jenis == "Permintaan") {
                $url = "https://sportiva.my.id/tempat/permintaan/detailPermintaanNego/".$komplain->fk_id_permintaan;
            }
            else {
                $url = "https://sportiva.my.id/tempat/penawaran/detailPenawaranNego/".$komplain->fk_id_penawaran;
            }
        }

        $tanggalAwal = $komplain->waktu_komplain;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');

        $dataNotif = [
            "subject" => "🎉Komplain ".$jenis." Anda Telah Diterima!🎉",
            "judul" => "Komplain ".$jenis." Anda Telah Diterima!",
            "nama_user" => $pengaju,
            "url" => $url,
            "button" => "Lihat Detail ".$jenis,
            "isi" => "Yeay! Komplain ".$jenis." yang Anda ajukan telah diterima Admin:<br><br>
                    <b>Jenis Komplain: ".$komplain->jenis_komplain."</b><br>
                    <b>Diajukan pada: ".$tanggalBaru."</b><br><br>
                    Komplain ini telah disetujui Admin dengan penanganan berupa ".$penanganan."!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($email, $dataNotif);

        return redirect()->back()->with("success", "Berhasil menangani komplain!");
    }

    public function tolakKomplain(Request $request) {
        $data = [
            "id" => $request->id,
            "status" => "Ditolak"
        ];
        $komp = new ModelsKomplainRequest();
        $komp->updateStatus($data);

        $data2 = [
            "id" => $request->id,
            "alasan" => $request->alasan
        ];
        $komp->updateAlasan($data2);

        //notif pemilik/tempat
        $komplain = DB::table('komplain_request')->where("id_komplain_req","=",$request->id)->get()->first();

        $jenis = "";
        if ($komplain->fk_id_permintaan != null) {
            //jenis request permintaan
            $jenis = "Permintaan";
        }
        else {
            $jenis = "Penawaran";
        }

        $pengaju = "";
        $email = "";
        if ($komplain->fk_id_pemilik != null) {
            // yang mengajukan pemilik
            $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$komplain->fk_id_pemilik)->get()->first();
            $pengaju = $pemilik->nama_pemilik;
            $email = $pemilik->email_pemilik;

            if ($jenis == "Permintaan") {
                $url = "https://sportiva.my.id/pemilik/permintaan/detailPermintaanNego/".$komplain->fk_id_permintaan;
            }
            else {
                $url = "https://sportiva.my.id/pemilik/penawaran/detailPenawaranNego/".$komplain->fk_id_penawaran;
            }
        }
        else {
            //yang mengajukan tempat
            $tempat = DB::table('pihak_tempat')->where("id_tempat","=",$komplain->fk_id_tempat)->get()->first();
            $pengaju = $tempat->nama_tempat;
            $email = $tempat->email_tempat;

            if ($jenis == "Permintaan") {
                $url = "https://sportiva.my.id/tempat/permintaan/detailPermintaanNego/".$komplain->fk_id_permintaan;
            }
            else {
                $url = "https://sportiva.my.id/tempat/penawaran/detailPenawaranNego/".$komplain->fk_id_penawaran;
            }
        }

        $tanggalAwal = $komplain->waktu_komplain;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');

        $dataNotif = [
            "subject" => "😔Komplain ".$jenis." Anda Telah Ditolak!😔",
            "judul" => "Komplain ".$jenis." Anda Telah Ditolak!",
            "nama_user" => $pengaju,
            "url" => $url,
            "button" => "Lihat Detail ".$jenis,
            "isi" => "Maaf! Komplain ".$jenis." yang Anda ajukan belum bisa kami terima.<br><br>
                    <b>Jenis Komplain: ".$komplain->jenis_komplain."</b><br>
                    <b>Diajukan pada: ".$tanggalBaru."</b><br><br>
                    Komplain kami tolak karena ".$request->alasan."<br>
                    Kami menghargai umpan balik Anda! Kami akan berusaha lebih baik di masa depan. Terima kasih atas pengertiannya!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($email, $dataNotif);

        return redirect()->back()->with("success", "Berhasil menolak komplain!");
    }
}
