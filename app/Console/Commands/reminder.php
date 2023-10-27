<?php

namespace App\Console\Commands;

use App\Models\customer;
use App\Models\htrans;
use App\Models\requestPermintaan;
use DateInterval;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\notifikasiEmail;
use App\Models\requestPenawaran;

class reminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //permintaan
        $per = new requestPermintaan();
        $data = $per->get_all_data();

        if (!$data->isEmpty()) {
            foreach ($data as $key => $value) {
                date_default_timezone_set('Asia/Jakarta');
                $sekarang = date('Y-m-d H:i:s');
                
                //KODE KONFIRMASI DIANTAR
                if ($value->status_permintaan == "Diterima" && $value->updated_at != null) {

                    $updated_at = new DateTime($value->updated_at);
                    $updated_at->add(new DateInterval('P2D'));
                    $exp = $updated_at->format('Y-m-d H:i:s');

                    $updated_at2 = new DateTime($value->updated_at);
                    $updated_at2->add(new DateInterval('P1D'));
                    $rem = $updated_at2->format('Y-m-d H:i:s');

                    if ($rem == $sekarang) {
                        //ngingetin pemilik besok tgl exp
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                        $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$value->fk_id_pemilik)->get()->first();
                        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();

                        $dataNotif = [
                            "subject" => "Ingat! Besok Batas Akhir Pengantaran Alat Olahraga",
                            "judul" => "Besok Batas Akhir Pengantaran Alat Olahraga!",
                            "nama_user" => $dataPemilik->nama_pemilik,
                            "isi" => "Jangan Lupa ya! Besok adalah Batas Akhir Pengantaran Alat Olahraga:<br><br>
                                    <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                    <b>Diantar ke Lapangan: ".$dataLapangan->nama_lapangan."</b><br><br>
                                    Terima kasih telah mempercayai layanan kami. Tetap Jaga Pola Sehat Anda bersama Sportiva! 😊"
                        ];
                        $e = new notifikasiEmail();
                        $e->sendEmail($dataPemilik->email_pemilik, $dataNotif);
                    }
                    else if ($exp == $sekarang) {
                        //batalkan request
                        $data2 = [
                            "id" => $value->id_permintaan,
                            "status" => "Dibatalkan"
                        ];
                        $per->updateStatus($data2);

                        //kasih notif ke pemilik, req dibatalkan
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                        $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$value->fk_id_pemilik)->get()->first();
                        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();

                        $dataNotif = [
                            "subject" => "😔Yah! Request Permintaan Dibatalkan!😔",
                            "judul" => "Request Permintaan Dibatalkan!",
                            "nama_user" => $dataPemilik->nama_pemilik,
                            "isi" => "Sayang sekali! Request Permintaan dari:<br><br>
                                    <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                    <b>Diantar ke Lapangan: ".$dataLapangan->nama_lapangan."</b><br><br>
                                    Telah otomatis dibatalkan karena batas akhir pengiriman sudah lewat! 😢"
                        ];
                        $e = new notifikasiEmail();
                        $e->sendEmail($dataPemilik->email_pemilik, $dataNotif);

                        //kasih notif ke tempat, req dibatalkan
                        $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$value->fk_id_tempat)->get()->first();

                        $dataNotif2 = [
                            "subject" => "😔Yah! Request Permintaan Dibatalkan!😔",
                            "judul" => "Request Permintaan Dibatalkan!",
                            "nama_user" => $dataTempat->nama_tempat,
                            "isi" => "Sayang sekali! Request Permintaan dari:<br><br>
                                    <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                    <b>Diantar ke Lapangan: ".$dataLapangan->nama_lapangan."</b><br><br>
                                    Telah otomatis dibatalkan karena batas akhir pengiriman sudah lewat! 😢"
                        ];
                        $e->sendEmail($dataTempat->email_tempat, $dataNotif2);
                    }
                }
                else if ($value->status_permintaan == "Disewakan" && $value->req_tanggal_selesai." 12:00:00" == $sekarang) {
                    //MASA SEWA ALAT SUDAH SELESAI
                    $data3 = [
                        "id" => $value->id_permintaan,
                        "status" => "Selesai"
                    ];
                    $per->updateStatus($data3);

                    //kasih notif ke pemilik
                    $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                    $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$value->fk_id_pemilik)->get()->first();
                    $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();

                    $dataNotif = [
                        "subject" => "⏳Masa Sewa Alat Olahraga Sudah Selesai!⏳",
                        "judul" => "Masa Sewa Alat Olahraga Sudah Selesai!",
                        "nama_user" => $dataPemilik->nama_pemilik,
                        "isi" => "Masa sewa alat dari:<br><br>
                                <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                Sudah selesai. Silahkan ambil alat olahragamu dan sewakan ditempat lain! Terima kasih telah mempercayai Sportiva! 😊"
                    ];
                    $e = new notifikasiEmail();
                    $e->sendEmail($dataPemilik->email_pemilik, $dataNotif);

                    //kasih notif ke tempat
                    $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$value->fk_id_tempat)->get()->first();

                    $dataNotif2 = [
                        "subject" => "⏳Masa Sewa Alat Olahraga Sudah Selesai!⏳",
                        "judul" => "Masa Sewa Alat Olahraga Sudah Selesai!",
                        "nama_user" => $dataTempat->nama_tempat,
                        "isi" => "Masa sewa alat dari:<br><br>
                                <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                Sudah selesai. Tunggu pemilik alat olahraga mengambil alatnya ya! Terima kasih telah mempercayai Sportiva! 😊"
                    ];
                    $e->sendEmail($dataTempat->email_tempat, $dataNotif2);
                }
            }
        }

        //--------------------------------------------------------------------------------------------------

        //penawaran
        $pen = new requestPenawaran();
        $dataPen = $pen->get_all_data();

        if (!$dataPen->isEmpty()) {
            foreach ($dataPen as $key => $value) {
                date_default_timezone_set('Asia/Jakarta');
                $sekarang = date('Y-m-d H:i:s');
                //KODE KONFIRMASI DIANTAR
                if ($value->status_penawaran == "Diterima" && $value->updated_at != null) {

                    $updated_at = new DateTime($value->updated_at);
                    $updated_at->add(new DateInterval('P2D'));
                    $exp = $updated_at->format('Y-m-d H:i:s');

                    $updated_at2 = new DateTime($value->updated_at);
                    $updated_at2->add(new DateInterval('P1D'));
                    $rem = $updated_at2->format('Y-m-d H:i:s');

                    if ($rem == $sekarang) {
                        //ngingetin pemilik besok tgl exp
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                        $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$value->fk_id_pemilik)->get()->first();
                        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();

                        $dataNotif = [
                            "subject" => "Ingat! Besok Batas Akhir Pengantaran Alat Olahraga",
                            "judul" => "Besok Batas Akhir Pengantaran Alat Olahraga!",
                            "nama_user" => $dataPemilik->nama_pemilik,
                            "isi" => "Jangan Lupa ya! Besok adalah Batas Akhir Pengantaran Alat Olahraga:<br><br>
                                    <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                    <b>Diantar ke Lapangan: ".$dataLapangan->nama_lapangan."</b><br><br>
                                    Terima kasih telah mempercayai layanan kami. Tetap Jaga Pola Sehat Anda bersama Sportiva! 😊"
                        ];
                        $e = new notifikasiEmail();
                        $e->sendEmail($dataPemilik->email_pemilik, $dataNotif);
                    }
                    else if ($exp == $sekarang) {
                        //batalkan request
                        $data2 = [
                            "id" => $value->id_penawaran,
                            "status" => "Dibatalkan"
                        ];
                        $per->updateStatus($data2);

                        //kasih notif ke pemilik, req dibatalkan
                        $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                        $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$value->fk_id_pemilik)->get()->first();
                        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();

                        $dataNotif = [
                            "subject" => "😔Yah! Request Penawaran Dibatalkan!😔",
                            "judul" => "Request Penawaran Dibatalkan!",
                            "nama_user" => $dataPemilik->nama_pemilik,
                            "isi" => "Sayang sekali! Request Penawaran dari:<br><br>
                                    <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                    <b>Diantar ke Lapangan: ".$dataLapangan->nama_lapangan."</b><br><br>
                                    Telah otomatis dibatalkan karena batas akhir pengiriman sudah lewat! 😢"
                        ];
                        $e = new notifikasiEmail();
                        $e->sendEmail($dataPemilik->email_pemilik, $dataNotif);

                        //kasih notif ke tempat, req dibatalkan
                        $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$value->fk_id_tempat)->get()->first();

                        $dataNotif2 = [
                            "subject" => "😔Yah! Request Penawaran Dibatalkan!😔",
                            "judul" => "Request Penawaran Dibatalkan!",
                            "nama_user" => $dataTempat->nama_tempat,
                            "isi" => "Sayang sekali! Request Penawaran dari:<br><br>
                                    <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                    <b>Diantar ke Lapangan: ".$dataLapangan->nama_lapangan."</b><br><br>
                                    Telah otomatis dibatalkan karena batas akhir pengiriman sudah lewat! 😢"
                        ];
                        $e->sendEmail($dataTempat->email_tempat, $dataNotif2);
                    }
                }
                else if ($value->status_penawaran == "Disewakan" && $value->req_tanggal_selesai." 12:00:00" == $sekarang) {
                    //MASA SEWA ALAT SUDAH SELESAI
                    $data3 = [
                        "id" => $value->id_penawaran,
                        "status" => "Selesai"
                    ];
                    $pen->updateStatus($data3);

                    //kasih notif ke pemilik
                    $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->req_id_alat)->get()->first();
                    $dataPemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$value->fk_id_pemilik)->get()->first();
                    $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->req_lapangan)->get()->first();

                    $dataNotif = [
                        "subject" => "⏳Masa Sewa Alat Olahraga Sudah Selesai!⏳",
                        "judul" => "Masa Sewa Alat Olahraga Sudah Selesai!",
                        "nama_user" => $dataPemilik->nama_pemilik,
                        "isi" => "Masa sewa alat dari:<br><br>
                                <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                Sudah selesai. Silahkan ambil alat olahragamu dan sewakan ditempat lain! Terima kasih telah mempercayai Sportiva! 😊"
                    ];
                    $e = new notifikasiEmail();
                    $e->sendEmail($dataPemilik->email_pemilik, $dataNotif);

                    //kasih notif ke tempat
                    $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$value->fk_id_tempat)->get()->first();

                    $dataNotif2 = [
                        "subject" => "⏳Masa Sewa Alat Olahraga Sudah Selesai!⏳",
                        "judul" => "Masa Sewa Alat Olahraga Sudah Selesai!",
                        "nama_user" => $dataTempat->nama_tempat,
                        "isi" => "Masa sewa alat dari:<br><br>
                                <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br><br>
                                Sudah selesai. Tunggu pemilik alat olahraga mengambil alatnya ya! Terima kasih telah mempercayai Sportiva! 😊"
                    ];
                    $e->sendEmail($dataTempat->email_tempat, $dataNotif2);
                }
            }
        }

        //-----------------------------------------------------------------------------------------------

        //transaksi
        $trans = new htrans();
        $dataTrans = $trans->get_all_data();
        
        if (!$dataTrans->isEmpty()) {
            foreach ($dataTrans as $key => $value) {
                date_default_timezone_set('Asia/Jakarta');
                $sekarang = date('Y-m-d H:i:s');

                $tanggal = $value->tanggal_sewa." ".$value->jam_sewa;
                $sewa = new DateTime($tanggal);
                $sewa->add(new DateInterval('P1D'));
                $sew = $sewa->format('Y-m-d H:i:s');

                $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$value->fk_id_lapangan)->get()->first();

                if ($sew == $sekarang) {
                    if ($value->status_trans == "Diterima") {
                        //kasih reminder ke cust
                        $dataCust = DB::table('user')->where("id_user","=",$value->fk_id_user)->get()->first();

                        $tanggalAwal2 = $value->tanggal_sewa;
                        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                        $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');

                        $dataNotif = [
                            "subject" => "🔔Ingat! Besok Hari Sewa Lapangan Olahraga🔔",
                            "judul" => "Jangan Lupa datang besok ya!",
                            "nama_user" => $dataCust->nama_user,
                            "isi" => "Detail Sewa Lapangan:<br><br>
                                    <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br>
                                    <b>Tanggal Sewa: ".$tanggalBaru2."</b><br>
                                    <b>Jam Sewa: ".$value->jam_sewa." WIB - ".\Carbon\Carbon::parse($value->jam_sewa)->addHours($value->durasi_sewa)->format('H:i:s')." WIB</b><br><br>
                                    Ingat datang tepat waktu ya karena kami tidak memberikan toleransi keterlambatan! 😊"
                        ];
                        $e = new notifikasiEmail();
                        $e->sendEmail($dataCust->email_user, $dataNotif);
                    }
                    else if ($value->status_trans == "Menunggu") {
                        //kasih reminder ke pihak tempat klo hrs segera diterima
                        $dataTemp = DB::table('pihak_tempat')->where("id_tempat","=",$value->fk_id_tempat)->get()->first();

                        $tanggalAwal2 = $value->tanggal_sewa;
                        $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
                        $tanggalBaru2 = $tanggalObjek2->format('d-m-Y');

                        $dataNotif2 = [
                            "subject" => "🔔Jangan Lupa Terima Transaksi Lapangan!🔔",
                            "judul" => "Jangan Lupa Terima Transaksi Lapangan!",
                            "nama_user" => $dataTemp->nama_tempat,
                            "isi" => "Detail Sewa Lapangan:<br><br>
                                    <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br>
                                    <b>Tanggal Sewa: ".$tanggalBaru2."</b><br>
                                    <b>Jam Sewa: ".$value->jam_sewa." WIB - ".\Carbon\Carbon::parse($value->jam_sewa)->addHours($value->durasi_sewa)->format('H:i:s')." WIB</b><br><br>
                                    Segera Terima Transaksi ini ya! Apabila tidak diterima hingga 2 jam sebelum waktu penyewaan, transaksi ini akan dibatalkan. 😊"
                        ];
                        $e = new notifikasiEmail();
                        $e->sendEmail($dataTemp->email_tempat, $dataNotif2);
                    }
                }

                $tanggal2 = $value->tanggal_sewa." ".$value->jam_sewa;
                $sewa2 = new DateTime($tanggal2);
                $sewa2->sub(new DateInterval('PT2H')); // mengurangkan 2 jam
                $sew2 = $sewa2->format('Y-m-d H:i:s');

                if ($sew2 == $sekarang && $value->status_trans == "Menunggu") {
                    //status trans berubah menjadi "dibatalkan" dan total transaksi kembali ke saldo cust
                    $cust = new customer();
                    $saldo = (int)$this->decodePrice($cust->get_all_data_by_id($value->fk_id_user)->first()->saldo_user, "mysecretkey");

                    $saldo += $value->total_trans;

                    //enkripsi kembali saldo
                    $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

                    //update db user
                    $dataSaldo = [
                        "id" => $value->fk_id_user,
                        "saldo" => $enkrip
                    ];
                    $cust = new customer();
                    $cust->updateSaldo($dataSaldo);

                    $data = [
                        "id" => $value->id_htrans,
                        "status" => "Dibatalkan"
                    ];
                    $trans = new htrans();
                    $trans->updateStatus($data);

                    //kasih notif pembatalan transaksi ke customer
                    $tanggalAwal3 = $value->tanggal_sewa;
                    $tanggalObjek3 = DateTime::createFromFormat('Y-m-d', $tanggalAwal3);
                    $tanggalBaru3 = $tanggalObjek3->format('d-m-Y');
                    $dataNotif3 = [
                        "subject" => "😔Booking Lapangan ".$dataLapangan->nama_lapangan." Telah Dibatalkan!😔",
                        "judul" => "Yah! Booking Lapangan ".$dataLapangan->nama_lapangan." Telah Dibatalkan",
                        "nama_user" => $cust->get_all_data_by_id($value->fk_id_user)->first()->nama_user,
                        "isi" => "Detail Sewa Lapangan:<br><br>
                                <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br>
                                <b>Tanggal Sewa: ".$tanggalBaru3."</b><br>
                                <b>Jam Sewa: ".$value->jam_sewa." WIB - ".\Carbon\Carbon::parse($value->jam_sewa)->addHours($value->durasi_sewa)->format('H:i:s')." WIB</b><br><br>
                                Telah dibatalkan, dana anda telah kami kembalikan ke saldo wallet! Terus jaga kesehatanmu bersama Sportiva! 😊"
                    ];
                    $e2 = new notifikasiEmail();
                    $e2->sendEmail($cust->get_all_data_by_id($value->fk_id_user)->first()->email_user, $dataNotif3);
                }
            }
        }
    }
}
