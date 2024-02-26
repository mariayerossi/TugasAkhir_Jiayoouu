<?php

namespace App\Http\Controllers;

use App\Models\customer;
use App\Models\kategori;
use App\Models\lapanganOlahraga;
use App\Models\notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\notifikasiEmail;
use App\Models\pemilikAlat;
use App\Models\pihakTempat;
use App\Models\tarikDana;
use DateTime;
use Illuminate\Support\Facades\Http;
use Midtrans\Transaction;

class Saldo extends Controller
{
    public function topup(Request $request) {
        $request->validate([
            "jumlah" => "required"
        ],[
            "required" => "Nominal top up tidak boleh kosong!"
        ]);
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config("midtrans.server_key");
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $params = array(
            'transaction_details' => array(
                'order_id' => rand(),
                'gross_amount' => (int)$request->jumlah,
            ),
            'customer_details' => array(
                'name' => Session::get("dataRole")->id_user,
                'phone' => Session::get("dataRole")->telepon_user,
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        $kot = new lapanganOlahraga();
        $param["kota"] = $kot->get_kota();
        $param["isi"] = $params;
        return view('customer.detailTopup', compact('snapToken'))->with($param);
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

    public function callback(Request $request) {
        $serverKey = config("midtrans.server_key");
        $hashed = hash("sha512",$request->order_id.$request->status_code.$request->gross_amount.$serverKey);
        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == "capture") {
                //tambahin saldonya
                $saldo = Session::get("dataRole")->saldo_user;
                $saldo += $request->gross_amount;

                $data = [
                    "id" => Session::get("dataRole")->id_user,
                    "saldo" => $saldo
                ];
                $cust = new customer();
                $cust->updateSaldo($data);

                $isiUser = $cust->get_all_data_by_id(Session::get("dataRole")->id_user);
                Session::forget("dataRole");
                Session::put("dataRole", $isiUser->first());
            }
        }
    }

    public function afterpayment(Request $request) {
        //tambahin saldonya
        $saldo = $this->decodePrice(Session::get("dataRole")->saldo_user, "mysecretkey");
        $saldo += $request->jumlah;

        $enkodeSaldo = $this->encodePrice((string)$saldo, "mysecretkey");

        $data = [
            "id" => Session::get("dataRole")->id_user,
            "saldo" => $enkodeSaldo
        ];
        $cust = new customer();
        $cust->updateSaldo($data);

        $isiUser = $cust->get_all_data_by_id(Session::get("dataRole")->id_user);
        Session::forget("dataRole");
        Session::put("dataRole", $isiUser->first());

        //notif ke cust

        date_default_timezone_set("Asia/Jakarta");
        $tanggal = date("Y-m-d H:i:s");

        $tanggalAwal = $tanggal;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y H:i:s');

        $dataNotifWeb = [
            "keterangan" => "Top Up Sebesar Rp ".number_format($request->jumlah, 0, ',', '.')." Berhasil!",
            "waktu" => $tanggal,
            "link" => "/customer/beranda",
            "user" => Session::get("dataRole")->id_user,
            "pemilik" => null,
            "tempat" => null,
            "admin" => null
        ];
        $notifWeb = new notifikasi();
        $notifWeb->insertNotifikasi($dataNotifWeb);

        $dataNotif = [
            "subject" => "🎉Top Up Sebesar Rp ".number_format($request->jumlah, 0, ',', '.')." Berhasil!🎉",
            "judul" => "Yeay! Top Up Sebesar Rp ".number_format($request->jumlah, 0, ',', '.')." Berhasil!",
            "nama_user" => Session::get("dataRole")->nama_user,
            "url" => "https://sportiva.my.id/customer/beranda",
            "button" => "Cari dan Temukan Lapangan Olahraga yang Menarik",
            "isi" => "Top up saldo dengan detail:<br><br>
                    <b>Nominal Top up: Rp ".number_format($request->jumlah, 0, ',', '.')."</b><br>
                    <b>Tanggal Top up: ".$tanggalBaru."</b><br><br>
                    Saldo Anda telah berhasil diperbarui. Selamat bertransaksi! 😊"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail(Session::get("dataRole")->email_user, $dataNotif);

        return response()->json([
            'success' => true,
            'message' => 'Saldo berhasil diperbarui!'
        ]);
    }

    public function tambahDetailPemilik(Request $request) {
        if ($request->bank == "" || $request->noRek == "" || $request->namaRek == "") {
            return response()->json(['success' => false, 'message' => "Input Detail Tidak Boleh Kosong!"]);
        }

        if ($request->noRek < 1 || strlen($request->noRek) < 10) {
            return response()->json(['success' => false, 'message' => "Nomer Rekening Tidak Valid!"]);
        }

        if (Session::get("dataRole")->norek_pemilik != null) {
            return response()->json(['success' => false, 'message' => "Nomer Rekening Telah Ditambahkan!"]);
        }

        $data = [
            "id" => Session::get("dataRole")->id_pemilik,
            "noRek" => $request->noRek,
            "namaRek" => $request->namaRek,
            "bank" => $request->bank
        ];
        $pemi = new pemilikAlat();
        $pemi->insertRekening($data);

        //update session
        $dataRole = $pemi->get_all_data_by_id(Session::get("dataRole")->id_pemilik);
        Session::forget("dataRole");
        Session::put("dataRole", $dataRole->first());

        return response()->json(['success' => true, 'message' => "Berhasil Menambah Detail!"]);
    }

    public function tambahDetailTempat(Request $request) {
        if ($request->bank == "" || $request->noRek == "" || $request->namaRek == "") {
            return response()->json(['success' => false, 'message' => "Input Detail Tidak Boleh Kosong!"]);
        }

        if ($request->noRek < 1 || strlen($request->noRek) < 10) {
            return response()->json(['success' => false, 'message' => "Nomer Rekening Tidak Valid!"]);
        }

        if (Session::get("dataRole")->norek_tempat != null) {
            return response()->json(['success' => false, 'message' => "Nomer Rekening Telah Ditambahkan!"]);
        }

        $data = [
            "id" => Session::get("dataRole")->id_tempat,
            "noRek" => $request->noRek,
            "namaRek" => $request->namaRek,
            "bank" => $request->bank
        ];
        $temp = new pihakTempat();
        $temp->insertRekening($data);

        //update session
        $dataRole = $temp->get_all_data_by_id(Session::get("dataRole")->id_tempat);
        Session::forget("dataRole");
        Session::put("dataRole", $dataRole->first());

        return response()->json(['success' => true, 'message' => "Berhasil Menambah Detail!"]);
    }

    public function tarikSaldo(Request $request) {
        $request->validate([
            "jumlah" => "required"
        ],[
            "required" => "Nominal tarik dana tidak boleh kosong!"
        ]);
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config("midtrans.server_key");
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = false;

        // $params = array(
        //     'payouts' => array(
        //         "beneficiary_name"=> Session::get("dataRole")->nama_rek_pemilik,
        //         "beneficiary_account"=> Session::get("dataRole")->norek_pemilik,
        //         "beneficiary_bank"=> Session::get("dataRole")->nama_bank_pemilik,
        //         "beneficiary_email"=> "beneficiary@example.com",
        //         "amount"=> (int)$request->jumlah,
        //         "notes"=> "Payout"
        //     ),
        // );
        // $response = Http::withHeaders([
        //     'Content-Type' => 'application/json',
        //     'Authorization' => 'Basic ' . base64_encode(env('MIDTRANS_SERVER_KEY') . ':')
        // ])->post('https://app.sandbox.midtrans.com/iris/api/v1/payouts',
        // [
        //     "beneficiary_name" => "Mandiri Simulator A	",
        //     "beneficiary_account" => "1111222233333",
        //     "beneficiary_bank" => "mandiri",
        //     "beneficiary_email" => "beneficiary@example.com",
        //     "amount" => "100000.00",
        //     "notes" => "Payout April 17" 
        // ]);
        // dd($response);
        // \Midtrans\Pay
        // $response = Transaction::payout($params);

        $authString = "Basic " . base64_encode(env('MIDTRANS_SERVER_KEY') . ':');
        // dd($authString);

        $baseUrl = 'https://app.sandbox.midtrans.com/iris/api/v1/beneficiaries';
        // $baseUrl = 'https://app.sandbox.midtrans.com/iris';
        // $baseUrl = 'https://api.sandbox.midtrans.com/v2/414371147/status';
        
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('GET', $baseUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => $authString,
                    'X-Idempotency-Key' => uniqid()
                ],
                'json' => [
                    "beneficiary_name" => "Mandiri Simulator A",
                    "beneficiary_account" => "1111222233333",
                    "beneficiary_bank" => "mandiri",
                    "beneficiary_email" => "beneficiary@example.com",
                    "amount" => "100000.00",
                    "notes" => "Payout April 17"
                ]
                // 'verify' => false,
            ]);
        
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
        
            return response()->json([
                'statusCode' => $statusCode,
                'body' => $body
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
        return response()->json();
    }

    public function tarikSaldo2(Request $request) {
        if ($request->jumlah == null || $request->jumlah == "") {
            return response()->json(['success' => false, 'message' => "Nominal tarik dana tidak boleh kosong!"]);
        }

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");

        if (Session::get("role") == "pemilik") {
            $saldo = Session::get("dataRole")->first()->saldo_pemilik;
            if ($request->jumlah > $saldo) {
                return response()->json(['success' => false, 'message' => "Saldo tidak cukup!"]);
            }

            $data = [
                "pemilik" => Session::get("dataRole")->first()->id_pemilik,
                "tempat" => null,
                "total" => (int)$request->jumlah,
                "tanggal" => $skrg
            ];
            $tarik = new tarikDana();
            $tarik->insertTarik($data);

            return response()->json(['success' => true, 'message' => "Berhasil mengajukan penarikan! Mohon tunggu konfirmasi admin"]);
        }
        else if (Session::get("role") == "tempat") {
            $saldo = Session::get("dataRole")->first()->saldo_tempat;
            if ($request->jumlah > $saldo) {
                return response()->json(['success' => false, 'message' => "Saldo tidak cukup!"]);
            }

            $data = [
                "pemilik" => null,
                "tempat" => Session::get("dataRole")->first()->id_tempat,
                "total" => (int)$request->jumlah,
                "tanggal" => $skrg
            ];
            $tarik = new tarikDana();
            $tarik->insertTarik($data);

            return response()->json(['success' => true, 'message' => "Berhasil mengajukan penarikan! Mohon tunggu konfirmasi admin"]);
        }
    }

    public function detailTarikAdmin(){
        $dana = new tarikDana();
        $param["tarik"] = $dana->get_all_data();

        // dd($dana->get_all_data());
        return view("admin.tarikDana")->with($param);
    }

    public function terimaTarik($id) {
        $data = [
            "id" => $id,
            "status" => "Diterima"
        ];
        $tarik = new tarikDana();
        $tarik->updateStatus($data);

        $total = $tarik->get_all_data_by_id($id)->first()->total_tarik;
        $tanggal = $tarik->get_all_data_by_id($id)->first()->tanggal_tarik;

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");

        //kasih notif email ke user
        if ($tarik->get_all_data_by_id($id)->first()->fk_id_pemilik != null) {
            $id_user = $tarik->get_all_data_by_id($id)->first()->fk_id_pemilik;
            $pem = new pemilikAlat();
            $saldo_pemilik = $pem->get_all_data_by_id($id_user)->first()->saldo_pemilik;
            $saldo = $this->decodePrice($saldo_pemilik, "mysecretkey");

            if ($total > $saldo) {
                return response()->json(['success' => false, 'message' => "Saldo user tidak cukup!"]);
            }

            $saldo -= $total;
            $enkodeSaldo = $this->encodePrice((string)$saldo, "mysecretkey");

            $data2 = [
                "id" => $id_user,
                "saldo" => $enkodeSaldo
            ];
            $pem->updateSaldo($data2);
            
            $nama_pemilik = $pem->get_all_data_by_id($id_user)->first()->nama_pemilik;
            $email_pemilik = $pem->get_all_data_by_id($id_user)->first()->email_pemilik;
            
            //notif web ke pemilik
            $dataNotifWeb = [
                "keterangan" => "Penarikan dana sebesar Rp ".number_format($total, 0, ',', '.')." Berhasil",
                "waktu" => $skrg,
                "link" => "/pemilik/saldo/tarikSaldo",
                "user" => null,
                "pemilik" => $id_user,
                "tempat" => null,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);

            $tanggalAwal2 = $tanggal;
            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
            $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
            $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY HH:mm');

            $dataNotif = [
                "subject" => "🎉Penarikan dana sebesar Rp ".number_format($total, 0, ',', '.')." Berhasil!🎉",
                "judul" => "Penarikan dana sebesar Rp ".number_format($total, 0, ',', '.')." Berhasil!",
                "nama_user" => $nama_pemilik,
                "url" => "https://sportiva.my.id/pemilik/saldo/tarikSaldo",
                "button" => "Lihat Detail Penarikan",
                "isi" => "Penarikan dana yang Anda ajukan:<br><br>
                        <b>Total Penarikan: Rp ".number_format($total, 0, ',', '.')."</b><br>
                        <b>Tanggal Penarikan: ".$tanggalBaru2."</b><br><br>
                        Telah diterima oleh Admin! Terima kasih telah menggunakan Sportiva!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($email_pemilik, $dataNotif);
        }
        else if ($tarik->get_all_data_by_id($id)->first()->fk_id_tempat != null) {
            $id_user = $tarik->get_all_data_by_id($id)->first()->fk_id_tempat;
            $pem = new pihakTempat();
            $saldo_tempat = $pem->get_all_data_by_id($id_user)->first()->saldo_tempat;
            $saldo = $this->decodePrice($saldo_tempat, "mysecretkey");
            
            if ($total > $saldo) {
                return response()->json(['success' => false, 'message' => "Saldo user tidak cukup!"]);
            }

            $saldo -= $total;
            $enkodeSaldo = $this->encodePrice((string)$saldo, "mysecretkey");

            $data2 = [
                "id" => $id_user,
                "saldo" => $enkodeSaldo
            ];
            $pem->updateSaldo($data2);
            
            $nama_tempat = $pem->get_all_data_by_id($id_user)->first()->nama_tempat;
            $email_tempat = $pem->get_all_data_by_id($id_user)->first()->email_tempat;
            
            //notif web ke tempat
            $dataNotifWeb = [
                "keterangan" => "Penarikan dana sebesar Rp ".number_format($total, 0, ',', '.')." Berhasil",
                "waktu" => $skrg,
                "link" => "/tempat/saldo/tarikSaldo",
                "user" => null,
                "pemilik" => null,
                "tempat" => $id_user,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);

            $tanggalAwal2 = $tanggal;
            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
            $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
            $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY HH:mm');

            $dataNotif = [
                "subject" => "🎉Penarikan dana sebesar Rp ".number_format($total, 0, ',', '.')." Berhasil!🎉",
                "judul" => "Penarikan dana sebesar Rp ".number_format($total, 0, ',', '.')." Berhasil!",
                "nama_user" => $nama_tempat,
                "url" => "https://sportiva.my.id/tempat/saldo/tarikSaldo",
                "button" => "Lihat Detail Penarikan",
                "isi" => "Penarikan dana yang Anda ajukan:<br><br>
                        <b>Total Penarikan: Rp ".number_format($total, 0, ',', '.')."</b><br>
                        <b>Tanggal Penarikan: ".$tanggalBaru2."</b><br><br>
                        Telah diterima oleh Admin! Terima kasih telah menggunakan Sportiva!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($email_tempat, $dataNotif);
        }

        return response()->json(['success' => true, 'message' => "Berhasil menerima penarikan dana"]);
    }

    public function tolakTarik($id) {
        $data = [
            "id" => $id,
            "status" => "Ditolak"
        ];
        $tarik = new tarikDana();
        $tarik->updateStatus($data);

        $total = $tarik->get_all_data_by_id($id)->first()->total_tarik;
        $tanggal = $tarik->get_all_data_by_id($id)->first()->tanggal_tarik;

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");

        //kasih notif email ke user
        if ($tarik->get_all_data_by_id($id)->first()->fk_id_pemilik != null) {
            $id_user = $tarik->get_all_data_by_id($id)->first()->fk_id_pemilik;
            $pem = new pemilikAlat();
            $nama_pemilik = $pem->get_all_data_by_id($id_user)->first()->nama_pemilik;
            $email_pemilik = $pem->get_all_data_by_id($id_user)->first()->email_pemilik;
            
            //notif web ke pemilik
            $dataNotifWeb = [
                "keterangan" => "Penarikan dana sebesar Rp ".number_format($total, 0, ',', '.')." Ditolak",
                "waktu" => $skrg,
                "link" => "/pemilik/saldo/tarikSaldo",
                "user" => null,
                "pemilik" => $id_user,
                "tempat" => null,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);

            $tanggalAwal2 = $tanggal;
            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
            $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
            $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY HH:mm');

            $dataNotif = [
                "subject" => "😔Penarikan dana sebesar Rp ".number_format($total, 0, ',', '.')." Ditolak!😔",
                "judul" => "Penarikan dana sebesar Rp ".number_format($total, 0, ',', '.')." Ditolak!",
                "nama_user" => $nama_pemilik,
                "url" => "https://sportiva.my.id/pemilik/saldo/tarikSaldo",
                "button" => "Lihat Detail Penarikan",
                "isi" => "Mohon maaf! Penarikan dana yang Anda ajukan:<br><br>
                        <b>Total Penarikan: Rp ".number_format($total, 0, ',', '.')."</b><br>
                        <b>Tanggal Penarikan: ".$tanggalBaru2."</b><br><br>
                        Telah ditolak oleh Admin! Terima kasih telah menggunakan Sportiva!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($email_pemilik, $dataNotif);
        }
        else if ($tarik->get_all_data_by_id($id)->first()->fk_id_tempat != null) {
            $id_user = $tarik->get_all_data_by_id($id)->first()->fk_id_tempat;
            $pem = new pihakTempat();
            $nama_tempat = $pem->get_all_data_by_id($id_user)->first()->nama_tempat;
            $email_tempat = $pem->get_all_data_by_id($id_user)->first()->email_tempat;
            
            //notif web ke tempat
            $dataNotifWeb = [
                "keterangan" => "Penarikan dana sebesar Rp ".number_format($total, 0, ',', '.')." Ditolak",
                "waktu" => $skrg,
                "link" => "/tempat/saldo/tarikSaldo",
                "user" => null,
                "pemilik" => null,
                "tempat" => $id_user,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);

            $tanggalAwal2 = $tanggal;
            $tanggalObjek2 = DateTime::createFromFormat('Y-m-d', $tanggalAwal2);
            $carbonDate2 = \Carbon\Carbon::parse($tanggalObjek2)->locale('id');
            $tanggalBaru2 = $carbonDate2->isoFormat('D MMMM YYYY HH:mm');

            $dataNotif = [
                "subject" => "😔Penarikan dana sebesar Rp ".number_format($total, 0, ',', '.')." Ditolak!😔",
                "judul" => "Penarikan dana sebesar Rp ".number_format($total, 0, ',', '.')." Ditolak!",
                "nama_user" => $nama_tempat,
                "url" => "https://sportiva.my.id/tempat/saldo/tarikSaldo",
                "button" => "Lihat Detail Penarikan",
                "isi" => "Mohon maaf! Penarikan dana yang Anda ajukan:<br><br>
                        <b>Total Penarikan: Rp ".number_format($total, 0, ',', '.')."</b><br>
                        <b>Tanggal Penarikan: ".$tanggalBaru2."</b><br><br>
                        Telah ditolak oleh Admin! Terima kasih telah menggunakan Sportiva!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($email_tempat, $dataNotif);
        }

        return response()->json(['success' => true, 'message' => "Berhasil menolak penarikan dana"]);
    }

    public function detailSaldoPemilik() {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $dana = new tarikDana();
        $param["dana"] = $dana->get_all_data_by_id_pemilik(Session::get("dataRole")->first()->id_pemilik);
        return view("pemilik.tarikSaldo")->with($param);
    }

    public function detailSaldoTempat() {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $dana = new tarikDana();
        $param["dana"] = $dana->get_all_data_by_id_tempat(Session::get("dataRole")->first()->id_tempat);
        return view("tempat.tarikSaldo")->with($param);
    }
}
