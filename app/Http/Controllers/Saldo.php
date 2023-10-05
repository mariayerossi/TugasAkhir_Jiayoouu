<?php

namespace App\Http\Controllers;

use App\Models\customer;
use App\Models\kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
        $param["isi"] =$params;
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

        return response()->json([
            'success' => true,
            'message' => 'Saldo berhasil diperbarui!'
        ]);
    }

    public function tarikSaldo(Request $request) {
        
    }
}
