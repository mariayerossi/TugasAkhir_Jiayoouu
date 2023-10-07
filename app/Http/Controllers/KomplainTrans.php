<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\customer;
use App\Models\filesKomplainTrans;
use App\Models\htrans;
use App\Models\komplainTrans as ModelsKomplainTrans;
use App\Models\lapanganOlahraga;
use App\Models\pemilikAlat;
use App\Models\pihakTempat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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

                    $penanganan = "Pengembalian dana dari pemilik alat sebesar $request->jumlah";
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

                    $penanganan = "Pengembalian dana dari pihak tempat sebesar $request->jumlah";
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

                    $penanganan = "Hapus Alat";
                }
                else if ($array[1] == "lapangan") {
                    $lapangan = new lapanganOlahraga();
                    $lapangan->softDelete($data3);

                    $penanganan = "Hapus Lapangan";
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
                    $temp = new pihakTempat();
                    $temp->softDelete($data4);

                    $penanganan = "Hapus Tempat";
                }
                else if ($array[1] == "pemilik") {
                    $pemi = new pemilikAlat();
                    $pemi->softDelete($data4);

                    $penanganan = "Hapus Pemilik";
                }
            }
            else {
                return redirect()->back()->with("error", "akun yang akan dinonaktifkan tidak boleh kosong!");
            }
        }

        //status transaksi menjadi ditolak
        $data5 = [
            "id" => $request->id_htrans,
            "status" => "Ditolak"
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

        return redirect()->back()->with("success", "Berhasil menangani komplain!");
    }

    public function tolakKomplain(Request $request) {
        $data = [
            "id" => $request->id,
            "status" => "Ditolak"
        ];
        $komp = new ModelsKomplainTrans();
        $komp->updateStatus($data);

        return redirect()->back()->with("success", "Berhasil menolak komplain!");
    }
}
