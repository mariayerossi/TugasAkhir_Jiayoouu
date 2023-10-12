<?php

namespace App\Http\Controllers;

use App\Models\customer;
use App\Models\dtrans;
use App\Models\extendDtrans;
use App\Models\extendHtrans;
use App\Models\htrans;
use App\Models\kategori;
use App\Models\pihakTempat;
use Carbon\Carbon;
use App\Models\notifikasiEmail;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Transaksi extends Controller
{
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

    public function tambahTransaksi(Request $request) {
        // dd(Session::get("sewaAlat"));
        $request->validate([
            "tanggal" => "required",
            "mulai" => "required",
            "selesai" => "required"
        ],[
            "tanggal.required" => "tanggal sewa tidak boleh kosong!",
            "mulai.required" => "jam mulai sewa tidak boleh kosong!",
            "selesai.required" => "jam selesai sewa tidak boleh kosong!"
        ]);

        $date_mulai = new DateTime($request->mulai);
        $date_selesai = new DateTime($request->selesai);
        
        if ($date_selesai <= $date_mulai) {
            return redirect()->back()->with("error", "Tanggal kembali tidak sesuai!");
        }

        //kasi pengecekan apakah ada tgl dan jam sama yg sdh dibooking
        $cek = DB::table('htrans')
                ->select("jam_sewa", "durasi_sewa")
                ->where("status_trans","=","Diterima")
                ->orWhere("status_trans","=","Berlangsung")
                ->where("tanggal_sewa", "=", $request->tanggal)
                ->where("fk_id_lapangan","=",$request->id_lapangan)
                ->get();

        if (!$cek->isEmpty()) {
            $conflict = false;
            foreach ($cek as $value) {
                $booking_jam_selesai = date('H:i', strtotime("+$value->durasi_sewa hour", strtotime($value->jam_sewa)));
                
                if (($request->mulai >= $value->jam_sewa && $request->mulai < $booking_jam_selesai) || 
                    ($request->selesai > $value->jam_sewa && $request->selesai <= $booking_jam_selesai) ||
                    ($request->mulai <= $value->jam_sewa && $request->selesai >= $booking_jam_selesai)) {
                    
                    $conflict = true;
                    break;
                }
            }

            if ($conflict) {
                // Ada konflik dengan booking yang ada
                return back()->with('error', 'Maaf, slot ini sudah dibooking!');
            }
        }

        $lapangan = DB::table('lapangan_olahraga')
                            ->select("harga_sewa_lapangan")
                            ->where('lapangan_olahraga.id_lapangan',"=",$request->id_lapangan)
                            ->get()
                            ->first();
        // dd($lapangan);
        $subtotal_alat_perjam = 0;
        $komisi_alat_pemilik = 0;
        $komisi_alat_tempat = 0;
        $subtotal_alat_lain = 0;
        if (Session::has("sewaAlat") && Session::get("sewaAlat") != null) {
            foreach (Session::get("sewaAlat") as $key => $value) {
                $milik = false;
                //permintaan
                $alat = DB::table('request_permintaan')
                        ->select("request_permintaan.req_harga_sewa as harga_alat", "alat_olahraga.komisi_alat as komisi")
                        ->join("alat_olahraga", "request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                        ->where("request_permintaan.req_id_alat","=",$value["alat"])
                        ->get()
                        ->first();
                if ($alat == null) {
                    //penawaran
                    $alat = DB::table('request_penawaran')
                            ->select("request_penawaran.req_harga_sewa as harga_alat", "alat_olahraga.komisi_alat as komisi")
                            ->join("alat_olahraga", "request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                            ->where("request_penawaran.req_id_alat","=",$value["alat"])
                            ->get()
                            ->first();
                    if ($alat == null) {
                        //sewa sendiri
                        $alat = DB::table('alat_olahraga')
                            ->select("alat_olahraga.komisi_alat as harga_alat", "alat_olahraga.komisi_alat as komisi")
                            ->join("sewa_sendiri", "alat_olahraga.id_alat","=","sewa_sendiri.req_id_alat")
                            ->where("alat_olahraga.id_alat","=",$value["alat"])
                            ->get()
                            ->first();
                        $milik = true;
                    }
                }

                $subtotal_alat_perjam += $alat->harga_alat; //per jam
                if ($milik == false) {
                    $komisi_alat_pemilik += $alat->komisi;//per jam
                    $subtotal_alat_lain += $alat->harga_alat;
                }
                else {
                    $komisi_alat_tempat += $alat->komisi; //per jam
                }
            }
        }

        //membuat durasi sewa nya
        // Mengubah string waktu ke timestamp
        $start_time = strtotime($request->mulai);
        $end_time = strtotime($request->selesai);

        // Menghitung durasi dalam detik
        $duration_seconds = $end_time - $start_time;

        // Menghitung durasi dalam menit
        $duration_minutes = (int)($duration_seconds / 60);

        // Menghitung durasi dalam jam dengan pembulatan ke jam terdekat
        $durasi_sewa = (int)round($duration_minutes / 60);
        //--------------------------------------------------------------------------

        $subtotal_alat = $subtotal_alat_perjam * $durasi_sewa;
        // dd($subtotal_alat);
        
        date_default_timezone_set("Asia/Jakarta");
        $tanggal_trans = date("Y-m-d H:i:s");

        $subtotal_lapangan = $lapangan->harga_sewa_lapangan * $durasi_sewa;

        $total = $subtotal_lapangan + $subtotal_alat;
        
        $total_komisi_alat_pemilik = $komisi_alat_pemilik * $durasi_sewa;
        $total_komisi_alat_tempat = $komisi_alat_tempat * $durasi_sewa;

        //subtotal dari alat olahraga lain(bkn milik tempat yng disewakan di tempat)
        $total_subtotal_alat_lain = $subtotal_alat_lain * $durasi_sewa;

        $persen_tempat = 0.09;
        $pendapatan_tempat = ($subtotal_lapangan + ($total_subtotal_alat_lain - $total_komisi_alat_pemilik) + $total_komisi_alat_tempat) * $persen_tempat;

        // dd($pendapatan_tempat);

        //cek saldo e cukup gaa
        $saldo = (int)$this->decodePrice(Session::get("dataRole")->saldo_user, "mysecretkey");
        if ($saldo < $total) {
            return back()->with('error', 'Saldo anda tidak cukup! Silahkan top up saldo anda.');
        }
        //saldo dipotong sebesar total
        $saldo -= $total;

        //enkripsi kembali saldo
        $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

        //update db user
        $dataSaldo = [
            "id" => Session::get("dataRole")->id_user,
            "saldo" => $enkrip
        ];
        $cust = new customer();
        $cust->updateSaldo($dataSaldo);

        //update session role
        $user = new customer();
        $isiUser = $user->get_all_data_by_id(Session::get("dataRole")->id_user);
        Session::forget("dataRole");
        Session::put("dataRole", $isiUser->first());

        $data = [
            "id_lapangan" => $request->id_lapangan,
            "subtotal_lapangan" => $subtotal_lapangan,
            "subtotal_alat" => $subtotal_alat,
            "tanggal_trans" => $tanggal_trans,
            "tanggal_sewa" => $request->tanggal,
            "jam_sewa" => $request->mulai,
            "durasi_sewa" => $durasi_sewa,
            "total" => $total,
            "id_user" => Session::get("dataRole")->id_user,
            "id_tempat" => $request->id_tempat,
            "pendapatan" => (int)$pendapatan_tempat
        ];
        $trans = new htrans();
        $id = $trans->insertHtrans($data);

        if (Session::has("sewaAlat") && Session::get("sewaAlat") != null) {
            foreach (Session::get("sewaAlat") as $key => $value) {
                $cek = false;

                $alat1 = DB::table('request_permintaan')
                        ->select("request_permintaan.req_harga_sewa as harga_alat", "alat_olahraga.komisi_alat as komisi", "alat_olahraga.fk_id_pemilik", "alat_olahraga.fk_id_tempat")
                        ->join("alat_olahraga", "request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                        ->where("request_permintaan.req_id_alat","=",$value["alat"])
                        ->get()
                        ->first();
                
                if ($alat1 == null) {
                    $alat1 = DB::table('request_penawaran')
                            ->select("request_penawaran.req_harga_sewa as harga_alat", "alat_olahraga.komisi_alat as komisi", "alat_olahraga.fk_id_pemilik", "alat_olahraga.fk_id_tempat")
                            ->join("alat_olahraga", "request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                            ->where("request_penawaran.req_id_alat","=",$value["alat"])
                            ->get()
                            ->first();
                        
                    if ($alat1 == null) {
                        $alat1 = DB::table('alat_olahraga')
                            ->select("alat_olahraga.komisi_alat as harga_alat", "alat_olahraga.komisi_alat as komisi", "alat_olahraga.fk_id_pemilik", "alat_olahraga.fk_id_tempat")
                            ->join("sewa_sendiri", "alat_olahraga.id_alat","=","sewa_sendiri.req_id_alat")
                            ->where("alat_olahraga.id_alat","=",$value["alat"])
                            ->get()
                            ->first();
                        $cek = true;
                    }
                }
                $subtotal_per_alat = $alat1->harga_alat * $durasi_sewa;

                $komisi_pemilik = $alat1->komisi * $durasi_sewa;

                $komisi_tempat = ($alat1->harga_alat - $alat1->komisi) * $durasi_sewa;

                $persen_alat = 0.11;
                $pendapatan_alat = $komisi_pemilik * $persen_alat;

                if ($cek == false) {
                    $data2 = [
                        "id_htrans" => $id,
                        "id_alat" => $value["alat"],
                        "harga_alat" => $alat1->harga_alat,
                        "subtotal_alat" => $subtotal_per_alat,
                        "komisi_pemilik" => $komisi_pemilik,
                        "komisi_tempat" => $komisi_tempat,
                        "id_pemilik" => $alat1->fk_id_pemilik,
                        "id_tempat" => $alat1->fk_id_tempat,
                        "pendapatan" => (int)$pendapatan_alat
                    ];
                }
                else {
                    $data2 = [
                        "id_htrans" => $id,
                        "id_alat" => $value["alat"],
                        "harga_alat" => $alat1->harga_alat,
                        "subtotal_alat" => $subtotal_per_alat,
                        "komisi_pemilik" => null,
                        "komisi_tempat" => $komisi_pemilik,
                        "id_pemilik" => $alat1->fk_id_pemilik,
                        "id_tempat" => $alat1->fk_id_tempat,
                        "pendapatan" => null
                    ];
                }
                $dtrans = new dtrans();
                $dtrans->insertDtrans($data2);
            }
        }

        //hapus session sewaAlat
        if (Session::has("sewaAlat")) {
            $sewaAlat = Session::get("sewaAlat");
            foreach ($sewaAlat as $key => $item) {
                if ($item['lapangan'] == "1") {
                    // 3. Hapus item tersebut dari array
                    unset($sewaAlat[$key]);
                }
            }
            Session::put("sewaAlat", $sewaAlat);
        }

        //hapus session cart
        if (Session::has("cart")) {
            $cart = Session::get("cart");
            foreach ($cart as $key => $item) {
                if ($item['lapangan'] == "1") {
                    // 3. Hapus item tersebut dari array
                    unset($cart[$key]);
                }
            }
            Session::put("cart", $cart);
        }

        //notif ke tempat
        $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$request->id_tempat)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$request->id_lapangan)->get()->first();
        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$id)->get();

        $dtransStr = "";
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->fk_id_alat)->get()->first();
                $dtransStr .= "<b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>";
            }
        }

        $dataNotif = [
            "subject" => "Transaksi Persewaan Baru Menunggu Konfirmasi Anda!",
            "judul" => "Transaksi Persewaan Baru Menunggu Konfirmasi Anda!",
            "nama_user" => $dataTempat->nama_tempat,
            "isi" => "Anda baru saja menerima satu transaksi persewaan baru:<br><br>
                    <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br>
                    ".$dtransStr."<br>
                    <b>Total Transaksi: Rp ".number_format($total, 0, ',', '.')."</b><br><br>
                    Harap segera konfirmasi transaksi untuk memastikan kelancaran prosesnya!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($dataTempat->email_tempat, $dataNotif);

        return redirect("/customer/detailLapangan/$request->id_lapangan")->with("success","Berhasil booking lapangan olahraga!");
    }
    
    public function daftarTransaksiTempat(){
        $role = Session::get("dataRole")->id_tempat;
        //baru
        $baru = DB::table('htrans')
            ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","user.nama_user","user.telepon_user","htrans.tanggal_trans")
            ->join("user", "htrans.fk_id_user", "=", "user.id_user")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->joinSub(function($query) {
                $query->select("fk_id_lapangan", "nama_file_lapangan")
                    ->from('files_lapangan')
                    ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
            }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
            ->where("htrans.fk_id_tempat", "=", $role)
            ->whereNull("htrans.deleted_at")
            ->where("htrans.status_trans", "=", "Menunggu")
            ->get();
        $param["baru"] = $baru;

        //berlangsung
        $berlangsung = DB::table('htrans')
            ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","user.nama_user","user.telepon_user","htrans.tanggal_trans")
            ->join("user", "htrans.fk_id_user", "=", "user.id_user")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->joinSub(function($query) {
                $query->select("fk_id_lapangan", "nama_file_lapangan")
                    ->from('files_lapangan')
                    ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
            }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
            ->where("htrans.fk_id_tempat", "=", $role)
            ->whereNull("htrans.deleted_at")
            ->where("htrans.status_trans", "=", "Berlangsung")
            ->get();
        $param["berlangsung"] = $berlangsung;

        //berlangsung
        $berlangsung = DB::table('htrans')
            ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","user.nama_user","user.telepon_user","htrans.tanggal_trans")
            ->join("user", "htrans.fk_id_user", "=", "user.id_user")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->joinSub(function($query) {
                $query->select("fk_id_lapangan", "nama_file_lapangan")
                    ->from('files_lapangan')
                    ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
            }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
            ->where("htrans.fk_id_tempat", "=", $role)
            ->whereNull("htrans.deleted_at")
            ->where("htrans.status_trans", "=", "Berlangsung")
            ->get();
        $param["berlangsung"] = $berlangsung;

        //diterima
        $diterima = DB::table('htrans')
            ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","user.nama_user","user.telepon_user","htrans.tanggal_trans")
            ->join("user", "htrans.fk_id_user", "=", "user.id_user")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->joinSub(function($query) {
                $query->select("fk_id_lapangan", "nama_file_lapangan")
                    ->from('files_lapangan')
                    ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
            }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
            ->where("htrans.fk_id_tempat", "=", $role)
            ->whereNull("htrans.deleted_at")
            ->where("htrans.status_trans", "=", "Diterima")
            ->get();
        $param["diterima"] = $diterima;

        //ditolak
        $ditolak = DB::table('htrans')
            ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","user.nama_user","user.telepon_user","htrans.tanggal_trans")
            ->join("user", "htrans.fk_id_user", "=", "user.id_user")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->joinSub(function($query) {
                $query->select("fk_id_lapangan", "nama_file_lapangan")
                    ->from('files_lapangan')
                    ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
            }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
            ->where("htrans.fk_id_tempat", "=", $role)
            ->whereNull("htrans.deleted_at")
            ->where("htrans.status_trans", "=", "Ditolak")
            ->get();
        $param["ditolak"] = $ditolak;

        //selesai
        $selesai = DB::table('htrans')
            ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","user.nama_user","user.telepon_user","htrans.tanggal_trans")
            ->join("user", "htrans.fk_id_user", "=", "user.id_user")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->joinSub(function($query) {
                $query->select("fk_id_lapangan", "nama_file_lapangan")
                    ->from('files_lapangan')
                    ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
            }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
            ->where("htrans.fk_id_tempat", "=", $role)
            ->whereNull("htrans.deleted_at")
            ->where("htrans.status_trans", "=", "Selesai")
            ->get();
        $param["selesai"] = $selesai;

        //dibatalkan
        $dibatalkan = DB::table('htrans')
            ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","user.nama_user","user.telepon_user","htrans.tanggal_trans")
            ->join("user", "htrans.fk_id_user", "=", "user.id_user")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->joinSub(function($query) {
                $query->select("fk_id_lapangan", "nama_file_lapangan")
                    ->from('files_lapangan')
                    ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
            }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
            ->where("htrans.fk_id_tempat", "=", $role)
            ->whereNull("htrans.deleted_at")
            ->where("htrans.status_trans", "=", "Dibatalkan")
            ->get();
        $param["dibatalkan"] = $dibatalkan;

        //dikomplain
        $dikomplain = DB::table('htrans')
            ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","user.nama_user","user.telepon_user","htrans.tanggal_trans")
            ->join("user", "htrans.fk_id_user", "=", "user.id_user")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->joinSub(function($query) {
                $query->select("fk_id_lapangan", "nama_file_lapangan")
                    ->from('files_lapangan')
                    ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
            }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
            ->where("htrans.fk_id_tempat", "=", $role)
            ->whereNull("htrans.deleted_at")
            ->where("htrans.status_trans", "=", "Dikomplain")
            ->get();
        $param["dikomplain"] = $dikomplain;
        return view("tempat.transaksi.daftarTransaksi")->with($param);
    }

    public function daftarTransaksiAdmin(){
        $trans = DB::table('htrans')
                ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","user.nama_user","htrans.kode_trans","htrans.total_trans","htrans.tanggal_trans","extend_htrans.total")
                ->join("user", "htrans.fk_id_user", "=", "user.id_user")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->whereNull("htrans.deleted_at")
                ->get();
        $param["trans"] = $trans;
        return view("admin.transaksi.daftarTransaksi")->with($param);
    }

    public function konfirmasiDipakai(Request $request) {
        $request->validate([
            "kode" => "required"
        ],[
            "required" => "Kode transaksi tidak boleh kosong!"
        ]);
        // dd($request->kode_trans);

        if ($request->kode != $request->kode_htrans) {
            return response()->json(['error' => 'Kode transaksi salah!'], 400);
        }

        //update status htrans
        $data = [
            "id" => $request->id_htrans,
            "status" => "Berlangsung"
        ];
        $trans = new htrans();
        $trans->updateStatus($data);

        return response()->json(['success' => 'Berhasil mengkonfirmasi transaksi!'], 200);
    }

    public function tambahAlat(Request $request) {
        $data = [];
        if(Session::has("sewaAlat")) $data = Session::get("sewaAlat");
        
        array_push($data,[
            "lapangan" => $request->id_lapangan,
            "alat" => $request->id_alat,
            "nama" => $request->nama,
            "file" => $request->file,
            "user" => Session::get("dataRole")->id_user
        ]);
    
        Session::put("sewaAlat", $data);
        
        return response()->json(['status' => 'success', 'message' => 'Data berhasil ditambahkan']);
    }

    public function deleteAlat($urutan) {
        // Ambil data dari session
        $data = Session::get("sewaAlat", []);
    
        unset($data[$urutan]);
    
        // Re-index array agar indexnya berurutan kembali
        $data = array_values($data);
    
        // Update session
        Session::put("sewaAlat", $data);
    
        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }

    public function deleteAlatDetail($id) {
        //hapus session sewaAlat
        $sewaAlat = Session::get("sewaAlat");
        foreach ($sewaAlat as $key => $item) {
            if ($item['alat'] == $id) {
                // 3. Hapus item tersebut dari array
                unset($sewaAlat[$key]);
            }
        }
        Session::put("sewaAlat", $sewaAlat);

        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }

    public function tambahKeranjang(Request $request) {
        $cart = [];
        if(Session::has("cart")) $cart = Session::get("cart");

        //ambil alat-alat yang dipesan
        $dataAlat = [];
        if(Session::has("sewaAlat")) $dataAlat = Session::get("sewaAlat");

        $alat = [];
        if ($dataAlat != null) {
            foreach ($dataAlat as $key => $value) {
                if ($value["user"] == Session::get("dataRole")->id_user && $value["lapangan"] == $request->id_lapangan) {
                    array_push($alat,[
                        "alat" => $value["alat"]
                    ]);
                }
            }
        }
        
        array_push($cart,[
            "lapangan" => $request->id_lapangan,
            "tanggal" => $request->tanggal,
            "mulai" => $request->mulai,
            "selesai" => $request->selesai,
            "user" => Session::get("dataRole")->id_user,
            "alat" => $alat
        ]);

        Session::put("cart", $cart);
        return redirect("/customer/daftarKeranjang");
    }

    public function daftarKeranjang() {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $data = [];

        if (Session::has("cart") && Session::get("cart") != null) {
            foreach (Session::get("cart") as $key => $value) {
                $result = DB::table('lapangan_olahraga')
                    ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.kota_lapangan","lapangan_olahraga.pemilik_lapangan")
                    ->joinSub(function($query) {
                        $query->select("fk_id_lapangan", "nama_file_lapangan")
                            ->from('files_lapangan')
                            ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                    }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                    ->where("lapangan_olahraga.id_lapangan","=",$value["lapangan"])
                    ->get();

                    if ($result->first()->id_lapangan == $value["lapangan"]) {
                        $result->first()->tanggal = $value['tanggal'] ?? null;
                        $result->first()->mulai = $value['mulai'] ?? null;
                        $result->first()->selesai = $value['selesai'] ?? null;
                        $result->first()->user = $value['user'] ?? null;

                        // dd($value['alat']);
                        if ($value['alat'] != []) {
                            foreach ($value['alat'] as $key => $value2) {
                                $alat = DB::table('alat_olahraga')
                                ->select("alat_olahraga.id_alat","alat_olahraga.nama_alat", "files_alat.nama_file_alat")
                                ->joinSub(function($query) {
                                    $query->select("fk_id_alat", "nama_file_alat")
                                        ->from('files_alat')
                                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                                ->where("alat_olahraga.id_alat", "=", $value2['alat'])
                                ->get();
                                // Mengubah elemen asli di dalam array
                                $value['alat'][$key]["nama_alat"] = $alat->first()->nama_alat ?? null;
                                $value['alat'][$key]["file_alat"] = $alat->first()->nama_file_alat ?? null;
                            }
                        }
                        $result->first()->id_alat = $value['alat'] ?? [];
                    }

                    $data[] = $result->first();
            }
        }
        usort($data, function ($a, $b) {
            return $b->id_lapangan - $a->id_lapangan; // urutan DESC
        });

        $param["data"] = $data;
        return view("customer.cart")->with($param);
    }

    public function hapusKeranjang($urutan) {
        // Ambil data dari session
        $data = Session::get("cart", []);
    
        unset($data[$urutan]);
    
        // Re-index array agar indexnya berurutan kembali
        $data = array_values($data);
    
        // Update session
        Session::put("cart", $data);
        return response()->json(['success' => true, 'message' => 'Item berhasil dihapus']);
    }

    public function daftarRiwayat() {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $trans = DB::table('htrans')
                ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","htrans.kode_trans","htrans.total_trans","htrans.tanggal_sewa", "htrans.jam_sewa", "htrans.durasi_sewa", "htrans.status_trans", "htrans.tanggal_trans")
                ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("htrans.fk_id_user", "=", Session::get("dataRole")->id_user)
                ->orderBy("htrans.id_htrans", "desc")
                ->get();

        $param["trans"] = $trans;
        return view("customer.riwayat")->with($param);
    }

    public function detailTransaksi(Request $request) {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        
        if ($request->tanggal == null || $request->mulai == null || $request->selesai == null) {
            return redirect()->back()->with("error", "Tanggal dan Jam sewa tidak boleh kosong!");
        }

        $date_mulai = new DateTime($request->mulai);
        $date_selesai = new DateTime($request->selesai);
        
        if ($date_selesai <= $date_mulai) {
            return redirect()->back()->with("error", "Tanggal kembali tidak sesuai!");
        }

        $start_time = strtotime($request->mulai);
        $end_time = strtotime($request->selesai);
        $duration_seconds = $end_time - $start_time;
        $duration_minutes = (int)($duration_seconds / 60);
        $durasi_sewa = (int)round($duration_minutes / 60);

        $subtotal_alat_perjam = 0;
        $komisi_alat_pemilik = 0;
        $komisi_alat_tempat = 0;
        $subtotal_alat_lain = 0;
        if (Session::has("sewaAlat") && Session::get("sewaAlat") != null) {
            foreach (Session::get("sewaAlat") as $key => $value) {
                if ($value["lapangan"] == $request->id_lapangan) {
                    $milik = false;
                    //permintaan
                    $alat = DB::table('request_permintaan')
                            ->select("request_permintaan.req_harga_sewa as harga_alat", "alat_olahraga.komisi_alat as komisi")
                            ->join("alat_olahraga", "request_permintaan.req_id_alat","=","alat_olahraga.id_alat")
                            ->where("request_permintaan.req_id_alat","=",$value["alat"])
                            ->get()
                            ->first();
                    if ($alat == null) {
                        //penawaran
                        $alat = DB::table('request_penawaran')
                                ->select("request_penawaran.req_harga_sewa as harga_alat", "alat_olahraga.komisi_alat as komisi")
                                ->join("alat_olahraga", "request_penawaran.req_id_alat","=","alat_olahraga.id_alat")
                                ->where("request_penawaran.req_id_alat","=",$value["alat"])
                                ->get()
                                ->first();
                        if ($alat == null) {
                            //sewa sendiri
                            $alat = DB::table('alat_olahraga')
                                ->select("alat_olahraga.komisi_alat as harga_alat", "alat_olahraga.komisi_alat as komisi")
                                ->join("sewa_sendiri", "alat_olahraga.id_alat","=","sewa_sendiri.req_id_alat")
                                ->where("alat_olahraga.id_alat","=",$value["alat"])
                                ->get()
                                ->first();
                            $milik = true;
                        }
                    }

                    $subtotal_alat_perjam += $alat->harga_alat; //per jam
                    if ($milik == false) {
                        $komisi_alat_pemilik += $alat->komisi;//per jam
                        $subtotal_alat_lain += $alat->harga_alat;
                    }
                    else {
                        $komisi_alat_tempat += $alat->komisi; //per jam
                    }
                }
            }
        }

        $subtotal_alat = $subtotal_alat_perjam * $durasi_sewa;

        $param["data"] = [
            "tanggal" => $request->tanggal,
            "mulai" => $request->mulai,
            "selesai" => $request->selesai,
            "durasi" => $durasi_sewa,
            "id_lapangan" => $request->id_lapangan,
            "id_tempat" => $request->id_tempat,
            "subtotal_alat" => $subtotal_alat
        ];

        return view("customer.detailTransaksi")->with($param);
    }

    public function terimaTransaksi(Request $request) {
        $data = [
            "id" => $request->id_htrans,
            "status" => "Diterima"
        ];
        $trans = new htrans();
        $trans->updateStatus($data);

        // notif ke customer
        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$request->id_htrans)->get()->first();
        $cust = DB::table('user')->where("id_user","=",$dataHtrans->fk_id_user)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$dataHtrans->fk_id_lapangan)->get()->first();

        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$dataHtrans->id_htrans)->get();

        $dtransStr = "";
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->fk_id_alat)->get()->first();
                $dtransStr .= "<b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>";
            }
        }
        
        $dataNotif = [
            "subject" => "🎉Transaksi Anda Telah Diterima!🎉",
            "judul" => "Transaksi Anda Telah Diterima Pihak Pengelola Tempat Olahraga!",
            "nama_user" => $cust->nama_user,
            "isi" => "Yeay! Transaksi Anda telah diterima:<br><br>
                    <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br>
                    ".$dtransStr."<br>
                    <b>Total Transaksi: Rp ".number_format($dataHtrans->total_trans, 0, ',', '.')."</b><br><br>
                    Ingat untuk datang tepat waktu dan nikmati sesi olahraga Anda! 😊"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($cust->email_user, $dataNotif);

        return response()->json(['success' => true, 'message' => 'Berhasil Diterima!']);
    }

    public function tolakTransaksi(Request $request) {
        $data = [
            "id" => $request->id_htrans,
            "status" => "Ditolak"
        ];
        $trans = new htrans();
        $trans->updateStatus($data);

        //pengembalian dana ke customer
        $saldo = (int)$this->decodePrice($request->saldo_user, "mysecretkey");
        $saldo += (int)$request->total;

        //enkripsi kembali saldo
        $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

        //update db user
        $dataSaldo = [
            "id" => $request->id_user,
            "saldo" => $enkrip
        ];
        $cust = new customer();
        $cust->updateSaldo($dataSaldo);

        //notif ke cust
        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$request->id_htrans)->get()->first();
        $cust = DB::table('user')->where("id_user","=",$dataHtrans->fk_id_user)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$dataHtrans->fk_id_lapangan)->get()->first();

        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$dataHtrans->id_htrans)->get();

        $dtransStr = "";
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->fk_id_alat)->get()->first();
                $dtransStr .= "<b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>";
            }
        }

        $dataNotif = [
            "subject" => "⚠️Transaksi Anda Ditolak!⚠️",
            "judul" => "Transaksi Anda Ditolak Pihak Pengelola Tempat Olahraga!",
            "nama_user" => $cust->nama_user,
            "isi" => "Maaf! Transaksi Anda Tidak Dapat Diproses:<br><br>
                    <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br>
                    ".$dtransStr."<br>
                    <b>Total Transaksi: Rp ".number_format($dataHtrans->total_trans, 0, ',', '.')."</b><br><br>
                    Jangan khawatir! Dana Anda telah kami kembalikan ke saldo wallet Anda. Cari dan sewa lapangan olahraga lain di Sportiva! 😊"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($cust->email_user, $dataNotif);

        return response()->json(['success' => true, 'message' => 'Berhasil Ditolak!']);
    }

    public function batalBooking(Request $request) {
        $id = $request->id_htrans;

        //cek apakah tanggal sewa dan jam sewa sdh lewat atau belom
        $trans = DB::table('htrans')
                ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","htrans.kode_trans","htrans.total_trans","htrans.tanggal_sewa", "htrans.jam_sewa", "htrans.durasi_sewa", "htrans.status_trans", "htrans.fk_id_tempat")
                ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("htrans.id_htrans", "=", $id)
                ->get()->first();

        if ($trans->status_trans == "Diterima") {
            $now = Carbon::now('Asia/Jakarta');
            $sewaDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $trans->tanggal_sewa . ' ' . $trans->jam_sewa, 'Asia/Jakarta');

            if ($sewaDateTime->lte($now)) {
                //sudah lewat
                return response()->json(['success' => false, 'message' => 'Waktu sewa sudah lewat! Tidak bisa membatalkan booking.']);
            }
        }

        //pengembalian dana
        $saldo = (int)$this->decodePrice(Session::get("dataRole")->saldo_user, "mysecretkey");

        //pemotongan denda 10%
        $denda = 0.10;
        $total_denda = $trans->total_trans * $denda;

        $saldo += $trans->total_trans - $total_denda;

        //enkripsi kembali saldo
        $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

        //update db user
        $dataSaldo = [
            "id" => Session::get("dataRole")->id_user,
            "saldo" => $enkrip
        ];
        $cust = new customer();
        $cust->updateSaldo($dataSaldo);

        //update session role
        $user = new customer();
        $isiUser = $user->get_all_data_by_id(Session::get("dataRole")->id_user);
        Session::forget("dataRole");
        Session::put("dataRole", $isiUser->first());

        //total_denda masuk ke saldo pihak tempat
        $saldoTempatAwal = DB::table('pihak_tempat')->where("id_tempat","=",$trans->fk_id_tempat)->get()->first()->saldo_tempat;
        $saldoTempat = (int)$this->decodePrice($saldoTempatAwal, "mysecretkey");

        $saldoTempat += $total_denda;
        $saldoAkhir = $this->encodePrice((string)$saldoTempat, "mysecretkey");

        $dataSaldoTempat = [
            "id" => $trans->fk_id_tempat,
            "saldo" => $saldoAkhir
        ];
        $temp = new pihakTempat();
        $temp->updateSaldo($dataSaldoTempat);

        $data = [
            "id" => $request->id_htrans,
            "status" => "Dibatalkan"
        ];
        $trans = new htrans();
        $trans->updateStatus($data);

        return response()->json(['success' => true, 'message' => 'Berhasil Dibatalkan!']);
    }

    public function detailTambahWaktu(Request $request) {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $htrans = DB::table('htrans')
                ->select("htrans.id_htrans","htrans.kode_trans","lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan","files_lapangan.nama_file_lapangan","lapangan_olahraga.harga_sewa_lapangan","htrans.tanggal_sewa","htrans.jam_sewa","htrans.durasi_sewa")
                ->join("lapangan_olahraga","htrans.fk_id_lapangan","=","lapangan_olahraga.id_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("htrans.id_htrans","=",$request->id_htrans)
                ->get()
                ->first();

        $dataDtrans = DB::table('dtrans')
                ->where("dtrans.fk_id_htrans","=",$htrans->id_htrans)
                ->get();
        
        $jam_sewa = $htrans->jam_sewa;
        $durasi_sewa = $htrans->durasi_sewa;
        $booking_jam_selesai1 = date('H:i', strtotime("+$durasi_sewa hour", strtotime($jam_sewa)));

        $booking_jam_selesai2 = date('H:i', strtotime("+$request->durasi hour", strtotime($booking_jam_selesai1)));

        $cek = DB::table('htrans')
                ->select("jam_sewa", "durasi_sewa")
                ->where("status_trans","=","Diterima")
                ->orWhere("status_trans","=","Berlangsung")
                ->where("tanggal_sewa", "=", $request->tanggal)
                ->where("fk_id_lapangan","=",$request->id_lapangan)
                ->get();

        //kasi pengecekan apakah tanggal dan jamnya bertubrukan
        if (!$cek->isEmpty()) {
            $conflict = false;
            foreach ($cek as $value) {
                $booking_jam_selesai3 = date('H:i', strtotime("+$value->durasi_sewa hour", strtotime($value->jam_sewa)));
                
                if (($booking_jam_selesai1 >= $value->jam_sewa && $booking_jam_selesai1 < $booking_jam_selesai3) || 
                    ($booking_jam_selesai2 > $value->jam_sewa && $booking_jam_selesai2 <= $booking_jam_selesai3) ||
                    ($booking_jam_selesai1 <= $value->jam_sewa && $booking_jam_selesai2 >= $booking_jam_selesai3)) {
                    
                    $conflict = true;
                    break;
                }
            }

            if ($conflict) {
                // Ada konflik dengan booking yang ada
                return back()->with('error', 'Maaf, slot ini sudah dibooking di jam '.$value->jam_sewa.'!');
            }
        }

        $param["durasi"] = $request->durasi;
        $param["jam_mulai"] = $booking_jam_selesai1;
        $param["jam_selesai"] = $booking_jam_selesai2;
        $param["trans"] = $htrans;
        $param["dtrans"] = $dataDtrans;
        return view("customer.detailTambahWaktu")->with($param);
    }

    public function tambahWaktu(Request $request) {
        $htrans = DB::table('htrans')
                ->where("htrans.id_htrans","=",$request->id_htrans)
                ->get()
                ->first();
        // dd($htrans);
        $dtrans = DB::table('dtrans')
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->where("dtrans.fk_id_htrans","=",$request->id_htrans)
                ->get();
        // dd($dtrans);

        $komisi_tempat = 0;
        if (!$dtrans->isEmpty()) {
            foreach ($dtrans as $value) {
                if ($value->fk_id_pemilik != null) {//milik pemilik
                    $komisi_tempat += $value->harga_sewa_alat - $value->komisi_alat;
                }
                else if ($value->fk_id_tempat != null) {//milik tempat
                    $komisi_tempat += $value->harga_sewa_alat;
                }
            }
        }
        // dd($komisi_tempat);

        $total_komisi_tempat = $komisi_tempat * $request->durasi;
        // dd($total_komisi_tempat);

        $persen_tempat = 0.09;
        $pendapatan_tempat = ($request->subtotal_lapangan + $total_komisi_tempat) * $persen_tempat;
        // dd($pendapatan_tempat);

        $data = [
            "id_htrans" => $request->id_htrans,
            "tanggal" => $htrans->tanggal_sewa,
            "jam" => $request->jam,
            "durasi" => (int)$request->durasi,
            "lapangan" => (int)$request->subtotal_lapangan,
            "alat" => (int)$request->subtotal_alat,
            "total" => (int)$request->total,
            "pendapatan" => (int)$pendapatan_tempat
        ];
        // dd($data);
        $extend = new extendHtrans();
        $id = $extend->insertExtendHtrans($data);

        $persen_pemilik = 0.11;

        if (!$dtrans->isEmpty()) {
            foreach ($dtrans as $value) {
                if ($value->fk_id_pemilik != null) {//milik pemilik
                    $data2 = [
                        "id_extend_htrans" => $id,
                        "id_dtrans" => $value->id_dtrans,
                        "harga" => $value->harga_sewa_alat,
                        "subtotal" => $value->harga_sewa_alat * $request->durasi,
                        "total_pemilik" => $value->komisi_alat * $request->durasi,
                        "total_tempat" => ($value->harga_sewa_alat - $value->komisi_alat) * $request->durasi,
                        "pendapatan" => (int)(($value->komisi_alat * $request->durasi) * $persen_pemilik)
                    ];
                }
                else if ($value->fk_id_tempat != null) {//milik tempat
                    $data2 = [
                        "id_extend_htrans" => $id,
                        "id_dtrans" => $value->id_dtrans,
                        "harga" => $value->harga_sewa_alat,
                        "subtotal" => $value->harga_sewa_alat * $request->durasi,
                        "total_pemilik" => null,
                        "total_tempat" => $value->harga_sewa_alat * $request->durasi,
                        "pendapatan" => null
                    ];
                }
                $extendDtrans = new extendDtrans();
                $extendDtrans->insertExtendDtrans($data2);
            }
        }

        //cek saldo e cukup gaa
        $saldo = (int)$this->decodePrice(Session::get("dataRole")->saldo_user, "mysecretkey");
        if ($saldo < (int)$request->total) {
            return back()->with('error', 'Saldo anda tidak cukup! Silahkan top up saldo anda.');
        }
        //saldo dipotong sebesar total
        $saldo -= (int)$request->total;

        //enkripsi kembali saldo
        $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

        //update db user
        $dataSaldo = [
            "id" => Session::get("dataRole")->id_user,
            "saldo" => $enkrip
        ];
        $cust = new customer();
        $cust->updateSaldo($dataSaldo);

        //update session role
        $user = new customer();
        $isiUser = $user->get_all_data_by_id(Session::get("dataRole")->id_user);
        Session::forget("dataRole");
        Session::put("dataRole", $isiUser->first());

        //notif ke tempat
        $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$htrans->fk_id_tempat)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$htrans->fk_id_lapangan)->get()->first();

        $dtransStr = "";
        if (!$dtrans->isEmpty()) {
            foreach ($dtrans as $key => $value) {
                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->fk_id_alat)->get()->first();
                $dtransStr .= "<b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>";
            }
        }

        $dataNotif = [
            "subject" => "Extend Waktu Baru Menunggu Konfirmasi Anda!",
            "judul" => "Extend Waktu Baru Menunggu Konfirmasi Anda!",
            "nama_user" => $dataTempat->nama_tempat,
            "isi" => "Anda baru saja menerima satu permintaan extend waktu baru:<br><br>
                    <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br>
                    ".$dtransStr."<br>
                    <b>Durasi Extend: ".$request->durasi." jam</b><br>
                    <b>Total Transaksi: Rp ".number_format($request->total, 0, ',', '.')."</b><br><br>
                    Harap segera konfirmasi untuk memastikan kelancaran prosesnya!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($dataTempat->email_tempat, $dataNotif);

        return redirect()->back()->with("success", "Berhasil melakukan extend waktu! menunggu konfirmasi pemilik tempat olahraga");
    }

    public function terimaExtend(Request $request) {
        $data = [
            "id" => $request->id_extend,
            "status" => "Diterima"
        ];
        $extend = new extendHtrans();
        $extend->updateStatus($data);

        // notif ke customer
        $extend = DB::table('extend_htrans')->where("id_extend_htrans","=",$request->id_extend)->get()->first();
        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$extend->fk_id_htrans)->get()->first();
        $cust = DB::table('user')->where("id_user","=",$dataHtrans->fk_id_user)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$dataHtrans->fk_id_lapangan)->get()->first();

        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$dataHtrans->id_htrans)->get();

        $dtransStr = "";
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->fk_id_alat)->get()->first();
                $dtransStr .= "<b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>";
            }
        }
        
        $dataNotif = [
            "subject" => "🎉Extend Waktu Anda Telah Diterima!🎉",
            "judul" => "Extend Waktu Anda Telah Diterima Pihak Pengelola Tempat Olahraga!",
            "nama_user" => $cust->nama_user,
            "isi" => "Yeay! Extend Waktu Anda telah diterima:<br><br>
                    <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br>
                    ".$dtransStr."<br>
                    <b>Durasi Extend: ".$extend->durasi_extend." jam</b><br>
                    <b>Total Transaksi: Rp ".number_format($extend->total, 0, ',', '.')."</b><br><br>
                    Selamat menikmati sesi olahraga Anda! Semoga sehat selalu! 😊"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($cust->email_user, $dataNotif);

        return response()->json(['success' => true, 'message' => 'Berhasil Diterima!']);
    }

    public function tolakExtend(Request $request) {
        $data = [
            "id" => $request->id_extend,
            "status" => "Ditolak"
        ];
        $extend = new extendHtrans();
        $extend->updateStatus($data);

        //pengembalian dana ke customer
        $saldo = (int)$this->decodePrice($request->saldo_user, "mysecretkey");
        $saldo += (int)$request->total;

        //enkripsi kembali saldo
        $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

        //update db user
        $dataSaldo = [
            "id" => $request->id_user,
            "saldo" => $enkrip
        ];
        $cust = new customer();
        $cust->updateSaldo($dataSaldo);

        //notif ke cust
        $extend = DB::table('extend_htrans')->where("id_extend_htrans","=",$request->id_extend)->get()->first();
        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$extend->fk_id_htrans)->get()->first();
        $cust = DB::table('user')->where("id_user","=",$dataHtrans->fk_id_user)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$dataHtrans->fk_id_lapangan)->get()->first();

        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$dataHtrans->id_htrans)->get();

        $dtransStr = "";
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->fk_id_alat)->get()->first();
                $dtransStr .= "<b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>";
            }
        }

        $dataNotif = [
            "subject" => "⚠️Extend Waktu Anda Ditolak!⚠️",
            "judul" => "Extend Waktu Anda Ditolak Pihak Pengelola Tempat Olahraga!",
            "nama_user" => $cust->nama_user,
            "isi" => "Maaf! Extend Waktu Anda Tidak Dapat Diproses:<br><br>
                    <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br>
                    ".$dtransStr."<br>
                    <b>Durasi Extend: ".$extend->durasi_extend." jam</b><br>
                    <b>Total Transaksi: Rp ".number_format($extend->total, 0, ',', '.')."</b><br><br>
                    Jangan khawatir! Dana Anda telah kami kembalikan ke saldo wallet Anda. Cari dan sewa lapangan olahraga lain di Sportiva! 😊"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail("maria.yerossi@gmail.com", $dataNotif);

        return response()->json(['success' => true, 'message' => 'Berhasil Ditolak!']);
    }
}
