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
            'payouts' => array(
                "beneficiary_name"=> Session::get("dataRole")->nama_rek_pemilik,
                "beneficiary_account"=> Session::get("dataRole")->norek_pemilik,
                "beneficiary_bank"=> Session::get("dataRole")->nama_bank_pemilik,
                "beneficiary_email"=> "beneficiary@example.com",
                "amount"=> (int)$request->jumlah,
                "notes"=> "Payout"
            ),
        );
        // $response = Http::withHeaders([
        //     'Content-Type' => 'application/json',
        //     'Authorization' => 'Basic ' . base64_encode(env('MIDTRANS_SERVER_KEY') . ':')
        // ])->post('https://api.sandbox.midtrans.com/payouts', [
        //     'bank_account' => "1111222233333",
        //     'amount' => (int)$request->jumlah,
        //     'beneficiary_name' => "Mandiri Simulator A",
        //     'bank' => "Bank Mandiri",
        //     'email' => 'john.doe@example.com'
        // ]);
        // dd($response);
        // \Midtrans\Pay
        $response = Transaction::payout($params);
        return response()->json(['result' => $response->json()]);
    }
}
