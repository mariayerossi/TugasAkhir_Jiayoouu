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
use App\Models\pemilikAlat;
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
            return redirect()->back()->with("error", "Jam sewa tidak sesuai!");
        }

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");
        $date_now = new DateTime($skrg); // ini adalah tanggal dan waktu saat ini
        $date_mulai_full = new DateTime($request->tanggal . ' ' . $request->mulai);
        $date_selesai_full = new DateTime($request->tanggal . ' ' . $request->selesai);

        // Kurangi 3 jam dari waktu mulai booking untuk mendapatkan batas waktu pemesanan
        $deadline_booking = clone $date_mulai_full;
        $deadline_booking->modify('-3 hours');

        if ($date_mulai_full <= $date_now || $date_selesai_full <= $date_now || $date_now >= $deadline_booking) {
            return redirect()->back()->with("error", "Tanggal atau waktu booking tidak valid! Booking harus dilakukan minimal 3 jam sebelum waktu sewa.");
        }

        if ($date_selesai <= $date_mulai) {
            return redirect()->back()->with("error", "Jam sewa tidak sesuai!");
        }

        //kasi pengecekan apakah ada tgl dan jam sama yg sdh dibooking
        $cek = DB::table('htrans')
                ->select("htrans.jam_sewa", "htrans.durasi_sewa", "htrans.tanggal_sewa", "extend_htrans.jam_sewa as jam_ext","extend_htrans.durasi_extend")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->whereDate("htrans.tanggal_sewa", $request->tanggal)
                ->where("fk_id_lapangan","=",$request->id_lapangan)
                ->where(function($query) {
                    $query->where("htrans.status_trans", "=", "Diterima")
                          ->orWhere("htrans.status_trans", "=", "Berlangsung");
                })
                ->get();
                // dd($cek);

        if (!$cek->isEmpty()) {
            $conflict = false;
            foreach ($cek as $value) {
                $booking_jam_selesai = date('H:i', strtotime("+$value->durasi_sewa hour", strtotime($value->jam_sewa)));
                $booking_jam_selesai_ext = date('H:i', strtotime("+$value->durasi_extend hour", strtotime($value->jam_ext)));
                
                if (($request->mulai >= $value->jam_sewa && $request->mulai < $booking_jam_selesai) || 
                    ($request->selesai > $value->jam_sewa && $request->selesai <= $booking_jam_selesai) ||
                    ($request->mulai <= $value->jam_sewa && $request->selesai >= $booking_jam_selesai) ||
                    ($request->mulai >= $value->jam_ext && $request->mulai < $booking_jam_selesai_ext) || 
                    ($request->selesai > $value->jam_ext && $request->selesai <= $booking_jam_selesai_ext) ||
                    ($request->mulai <= $value->jam_ext && $request->selesai >= $booking_jam_selesai_ext)) {
                    
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
        if (Session::has("sewaAlat") && Session::get("sewaAlat") != null) {
            $sewaAlat = Session::get("sewaAlat");
            foreach ($sewaAlat as $key => $item) {
                if ($item['lapangan'] == $request->id_lapangan) {
                    // 3. Hapus item tersebut dari array
                    unset($sewaAlat[$key]);
                }
            }
            Session::put("sewaAlat", $sewaAlat);
        }

        //hapus session cart
        if (Session::has("cart") && Session::get("cart") != null) {
            $cart = Session::get("cart");
            foreach ($cart as $key => $item) {
                if ($item['lapangan'] == $request->id_lapangan && $item['tanggal'] == $request->tanggal && $item['mulai'] == $request->mulai) {
                    // 3. Hapus item tersebut dari array
                    unset($cart[$key]);
                }
            }
            Session::put("cart", $cart);
        }

        //notif ke tempat
        $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$request->id_tempat)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$request->id_lapangan)->get()->first();
        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$id)->where("deleted_at","=",null)->get();

        $dtransStr = "";
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->fk_id_alat)->get()->first();
                $dtransStr .= "<b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>";
            }
        }

        $dataNotif = [
            "subject" => "🔔Transaksi Persewaan Baru Menunggu Konfirmasi Anda!🔔",
            "judul" => "Transaksi Persewaan Baru Menunggu Konfirmasi Anda!",
            "nama_user" => $dataTempat->nama_tempat,
            "url" => "https://sportiva.my.id/tempat/transaksi/detailTransaksi/".$id,
            "button" => "Lihat Detail Transaksi",
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
        
        return response()->json(['status' => 'success', 'message' => 'Alat berhasil ditambahkan! Cek bagian Atur Tanggal dan Jam Booking untuk melihat daftar alat yang dibooking']);
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
                ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.id_lapangan", "lapangan_olahraga.nama_lapangan","htrans.kode_trans","htrans.total_trans","htrans.tanggal_sewa", "htrans.jam_sewa", "htrans.durasi_sewa", "htrans.status_trans", "htrans.tanggal_trans")
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
            return redirect()->back()->withInput()->with("error", "Tanggal kembali tidak sesuai!");
        }

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");
        $date_now = new DateTime($skrg); // ini adalah tanggal dan waktu saat ini
        $date_mulai_full = new DateTime($request->tanggal . ' ' . $request->mulai);
        $date_selesai_full = new DateTime($request->tanggal . ' ' . $request->selesai);

        // Kurangi 3 jam dari waktu mulai booking untuk mendapatkan batas waktu pemesanan
        $deadline_booking = clone $date_mulai_full;
        $deadline_booking->modify('-3 hours');

        if ($date_mulai_full <= $date_now || $date_selesai_full <= $date_now || $date_now >= $deadline_booking) {
            return redirect()->back()->with("error", "Tanggal atau waktu booking tidak valid! Booking harus dilakukan minimal 3 jam sebelum waktu sewa.");
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
        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$request->id_htrans)->get()->first();
        $cek = DB::table('htrans')
                ->select("htrans.jam_sewa", "htrans.durasi_sewa", "htrans.tanggal_sewa", "extend_htrans.jam_sewa as jam_ext","extend_htrans.durasi_extend")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->whereDate("htrans.tanggal_sewa", $dataHtrans->tanggal_sewa)
                ->where("fk_id_lapangan","=",$dataHtrans->fk_id_lapangan)
                ->where(function($query) {
                    $query->where("htrans.status_trans", "=", "Diterima")
                          ->orWhere("htrans.status_trans", "=", "Berlangsung");
                })
                ->get();
                // dd($cek);
        $selesai = date('H:i', strtotime("+$dataHtrans->durasi_sewa hour", strtotime($dataHtrans->jam_sewa)));

        if (!$cek->isEmpty()) {
            $conflict = false;
            foreach ($cek as $value) {
                $booking_jam_selesai = date('H:i', strtotime("+$value->durasi_sewa hour", strtotime($value->jam_sewa)));
                $booking_jam_selesai_ext = date('H:i', strtotime("+$value->durasi_extend hour", strtotime($value->jam_ext)));
                
                if (($dataHtrans->jam_sewa >= $value->jam_sewa && $dataHtrans->jam_sewa < $booking_jam_selesai) || 
                    ($selesai > $value->jam_sewa && $selesai <= $booking_jam_selesai) ||
                    ($dataHtrans->jam_sewa <= $value->jam_sewa && $selesai >= $booking_jam_selesai) ||
                    ($dataHtrans->jam_sewa >= $value->jam_ext && $dataHtrans->jam_sewa < $booking_jam_selesai_ext) || 
                    ($selesai > $value->jam_ext && $selesai <= $booking_jam_selesai_ext) ||
                    ($dataHtrans->jam_sewa <= $value->jam_ext && $selesai >= $booking_jam_selesai_ext)) {
                    
                    $conflict = true;
                    break;
                }
            }

            if ($conflict) {
                // Ada konflik dengan booking yang ada
                return response()->json(['success' => false, 'message' => 'Maaf, slot ini sudah dibooking!']);
            }
        }

        $data = [
            "id" => $request->id_htrans,
            "status" => "Diterima"
        ];
        $trans = new htrans();
        $trans->updateStatus($data);

        // notif ke customer
        $cust = DB::table('user')->where("id_user","=",$dataHtrans->fk_id_user)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$dataHtrans->fk_id_lapangan)->get()->first();

        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$dataHtrans->id_htrans)->where("deleted_at","=",null)->get();

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
            "url" => "https://sportiva.my.id/customer/daftarRiwayat",
            "button" => "Lihat Transaksi",
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

        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$dataHtrans->id_htrans)->where("deleted_at","=",null)->get();

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
            "url" => "https://sportiva.my.id/customer/daftarRiwayat",
            "button" => "Lihat Transaksi",
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

        //pemotongan denda 5%
        $denda = 0.05;
        $total_denda = $trans->total_trans * $denda;
        // dd((int)$total_denda);

        $saldo += $trans->total_trans - (int)$total_denda;

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


        //kasih notif ke pihak tempat
        $namaTempat = DB::table('pihak_tempat')->where("id_tempat","=",$trans->fk_id_tempat)->get()->first()->nama_tempat;
        $emailTempat = DB::table('pihak_tempat')->where("id_tempat","=",$trans->fk_id_tempat)->get()->first()->email_tempat;
        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$id)->where("deleted_at","=",null)->get();

        $dtransStr = "";
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->fk_id_alat)->get()->first();
                $dtransStr .= "<b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>";
            }
        }
        $tanggalAwal = $trans->tanggal_sewa;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y');

        $dataNotif = [
            "subject" => "⚠️Transaksi Dibatalkan Customer!⚠️",
            "judul" => "Transaksi Dibatalkan Customer!",
            "nama_user" => $namaTempat,
            "url" => "https://sportiva.my.id/tempat/transaksi/detailTransaksi/".$request->id_htrans,
            "button" => "Lihat Detail Transaksi",
            "isi" => "Maaf! Transaksi Anda Tidak Dapat Dilanjutkan oleh Customer:<br><br>
                    <b>Nama Lapangan Olahraga: ".$trans->nama_lapangan."</b><br>
                    ".$dtransStr."<br>
                    <b>Tanggal Transaksi: ".$tanggalBaru." ".$trans->jam_sewa."</b><br><br>
                    Jangan khawatir! Dana Kompensasi telah kami tambahkan ke saldo wallet Anda! 😊"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($emailTempat, $dataNotif);

        $data = [
            "id" => $request->id_htrans,
            "status" => "Dibatalkan"
        ];
        $trans = new htrans();
        $trans->updateStatus($data);

        return response()->json(['success' => true, 'message' => 'Berhasil Dibatalkan!']);
    }

    public function batalTrans(Request $request) {
        $id = $request->id_htrans;

        //cek apakah tanggal sewa dan jam sewa sdh lewat atau belom
        $trans = DB::table('htrans')
                ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","htrans.kode_trans","htrans.total_trans","htrans.tanggal_sewa", "htrans.jam_sewa", "htrans.durasi_sewa", "htrans.status_trans", "htrans.fk_id_tempat", "user.saldo_user","user.nama_user","pihak_tempat.saldo_tempat","htrans.fk_id_user","pihak_tempat.email_tempat")
                ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
                ->join("user","htrans.fk_id_user","=","user.id_user")
                ->join("pihak_tempat","htrans.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("htrans.id_htrans", "=", $id)
                ->get()->first();

        //pengembalian dana
        $saldoTemp = (int)$this->decodePrice($trans->saldo_tempat, "mysecretkey");

        $saldo = (int)$this->decodePrice($trans->saldo_user, "mysecretkey");

        //pemotongan denda 10% dari tempat
        $denda = 0.10;
        $total_denda = $trans->total_trans * $denda;

        if ($saldoTemp < $total_denda) {
            return response()->json(['success' => false, 'message' => 'Gagal Membatalkan Transaksi! Kompensasi tidak dapat diberikan!']);
        }

        $saldoTemp -= $total_denda;

        $saldo += $trans->total_trans + $total_denda;

        //enkripsi kembali saldo
        $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");
        $enkripTemp = $this->encodePrice((string)$saldoTemp, "mysecretkey");

        //update db user
        $dataSaldo = [
            "id" => $trans->fk_id_user,
            "saldo" => $enkrip
        ];
        $cust = new customer();
        $cust->updateSaldo($dataSaldo);

        //update db tempat
        $dataSaldo2 = [
            "id" => $trans->fk_id_tempat,
            "saldo" => $enkripTemp
        ];
        $temp = new pihakTempat();
        $temp->updateSaldo($dataSaldo2);

        //update session tempat
        $isiTemp = $temp->get_all_data_by_id($trans->fk_id_tempat);
        Session::forget("dataRole");
        Session::put("dataRole", $isiTemp->first());

        //kasih notif ke cust
        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$id)->where("deleted_at","=",null)->get();

        $dtransStr = "";
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->fk_id_alat)->get()->first();
                $dtransStr .= "<b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>";
            }
        }

        $tanggalAwal = $trans->tanggal_sewa;
        $tanggalObjek = DateTime::createFromFormat('Y-m-d', $tanggalAwal);
        $tanggalBaru = $tanggalObjek->format('d-m-Y');

        $dataNotif = [
            "subject" => "⚠️Transaksi Dibatalkan Pihak Pengelola Tempat Olahraga!⚠️",
            "judul" => "Transaksi Dibatalkan Pihak Pengelola Tempat Olahraga!",
            "nama_user" => $trans->nama_user,
            "url" => "https://sportiva.my.id/customer/daftarRiwayat",
            "button" => "Lihat Transaksi",
            "isi" => "Maaf! Transaksi Anda Tidak Dapat Dilanjutkan oleh Pihak Pengelola Tempat Olahraga:<br><br>
                    <b>Nama Lapangan Olahraga: ".$trans->nama_lapangan."</b><br>
                    ".$dtransStr."<br>
                    <b>Tanggal Transaksi: ".$tanggalBaru." ".$trans->jam_sewa."</b><br><br>
                    Jangan khawatir! Dana Kompensasi telah kami tambahkan ke saldo wallet Anda! 😊"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($trans->email_tempat, $dataNotif);

        $data = [
            "id" => $request->id_htrans,
            "status" => "Dibatalkan"
        ];
        $trans = new htrans();
        $trans->updateStatus($data);

        return response()->json(['success' => true, 'message' => 'Berhasil Dibatalkan!']);
    }

    public function detailTambahWaktu(Request $request) {
        // dd($request->id_htrans);
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
                ->where("dtrans.fk_id_htrans","=",$request->id_htrans)
                ->where("dtrans.deleted_at","=",null)
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
        // $htrans = DB::table('htrans')
        //         ->select("htrans.id_htrans","htrans.kode_trans","lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan","files_lapangan.nama_file_lapangan","lapangan_olahraga.harga_sewa_lapangan","htrans.tanggal_sewa","htrans.jam_sewa","htrans.durasi_sewa")
        //         ->join("lapangan_olahraga","htrans.fk_id_lapangan","=","lapangan_olahraga.id_lapangan")
        //         ->joinSub(function($query) {
        //             $query->select("fk_id_lapangan", "nama_file_lapangan")
        //                 ->from('files_lapangan')
        //                 ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
        //         }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
        //         ->where("htrans.id_htrans","=",$request->id_htrans)
        //         ->get()
        //         ->first();
        
        // $jam_sewa = $htrans->jam_sewa;
        // $durasi_sewa = $htrans->durasi_sewa;
        // $booking_jam_selesai1 = date('H:i', strtotime("+$durasi_sewa hour", strtotime($jam_sewa)));

        // $booking_jam_selesai2 = date('H:i', strtotime("+$request->durasi hour", strtotime($booking_jam_selesai1)));
        // // dd($booking_jam_selesai1);

        // $cek = DB::table('htrans')
        //     ->select("jam_sewa", "durasi_sewa", "fk_id_lapangan")
        //     ->where("tanggal_sewa","=", $request->tanggal)
        //     ->where("fk_id_lapangan","=",$request->id_lapangan)
        //     ->where(function($query) {
        //         $query->where("status_trans","=","Diterima")
        //               ->orWhere("status_trans","=","Berlangsung");
        //     })
        //     ->get();

        // // dd($cek);

        // //kasi pengecekan apakah tanggal dan jamnya bertubrukan
        // if (!$cek->isEmpty()) {
        //     $conflict = false;
        //     foreach ($cek as $value) {
        //         $booking_jam_selesai3 = date('H:i', strtotime("+$value->durasi_sewa hour", strtotime($value->jam_sewa)));
                
        //         if (($booking_jam_selesai1 >= $value->jam_sewa && $booking_jam_selesai1 < $booking_jam_selesai3) || 
        //             ($booking_jam_selesai2 > $value->jam_sewa && $booking_jam_selesai2 <= $booking_jam_selesai3) ||
        //             ($booking_jam_selesai1 <= $value->jam_sewa && $booking_jam_selesai2 >= $booking_jam_selesai3)) {
                    
        //             $conflict = true;
        //             break;
        //         }
        //     }

        //     if ($conflict) {
        //         // Ada konflik dengan booking yang ada
        //         return back()->with('error', 'Maaf, slot ini sudah dibooking di jam '.$value->jam_sewa.'!');
        //     }
        // }
        // dd($cek);
        $htrans = DB::table('htrans')
                ->where("htrans.id_htrans","=",$request->id_htrans)
                ->get()
                ->first();
        // dd($htrans);
        $dtrans = DB::table('dtrans')
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->where("dtrans.fk_id_htrans","=",$request->id_htrans)
                ->where("dtrans.deleted_at","=",null)
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
            "subject" => "🔔Extend Waktu Baru Menunggu Konfirmasi Anda!🔔",
            "judul" => "Extend Waktu Baru Menunggu Konfirmasi Anda!",
            "nama_user" => $dataTempat->nama_tempat,
            "url" => "https://sportiva.my.id/tempat/transaksi/detailTransaksi/".$request->id_htrans,
            "button" => "Lihat Detail Transaksi",
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
        $extend = DB::table('extend_htrans')->where("id_extend_htrans","=",$request->id_extend)->get()->first();
        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$extend->fk_id_htrans)->get()->first();

        //kasih pengecekan slot uda dipesan blm
        $htrans = DB::table('htrans')
                ->select("htrans.id_htrans","htrans.kode_trans","lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan","files_lapangan.nama_file_lapangan","lapangan_olahraga.harga_sewa_lapangan","htrans.tanggal_sewa","htrans.jam_sewa","htrans.durasi_sewa")
                ->join("lapangan_olahraga","htrans.fk_id_lapangan","=","lapangan_olahraga.id_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("htrans.id_htrans","=",$dataHtrans->id_htrans)
                ->get()
                ->first();
        
        $jam_sewa = $htrans->jam_sewa;
        $durasi_sewa = $htrans->durasi_sewa;
        $booking_jam_selesai1 = date('H:i', strtotime("+$durasi_sewa hour", strtotime($jam_sewa)));

        $booking_jam_selesai2 = date('H:i', strtotime("+$extend->durasi_extend hour", strtotime($booking_jam_selesai1)));

        $cek = DB::table('htrans')
                ->select("jam_sewa", "durasi_sewa")
                ->where("status_trans","=","Diterima")
                ->orWhere("status_trans","=","Berlangsung")
                ->where("tanggal_sewa", "=", $htrans->tanggal_sewa)
                ->where("fk_id_lapangan","=",$htrans->id_lapangan)
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
                // return back()->with('error', 'Maaf, slot ini sudah dibooking di jam '.$value->jam_sewa.'!');
                return response()->json(['success' => false, 'message' => 'Maaf, slot ini sudah dibooking di jam '.$value->jam_sewa.'!']);
            }
        }

        $data = [
            "id" => $request->id_extend,
            "status" => "Diterima"
        ];
        $extend = new extendHtrans();
        $extend->updateStatus($data);

        // notif ke customer
        $cust = DB::table('user')->where("id_user","=",$dataHtrans->fk_id_user)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$dataHtrans->fk_id_lapangan)->get()->first();

        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$dataHtrans->id_htrans)->where("deleted_at","=",null)->get();

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
            "url" => "https://sportiva.my.id/customer/daftarRiwayat",
            "button" => "Lihat Transaksi",
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

        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$dataHtrans->id_htrans)->where("deleted_at","=",null)->get();

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
            "url" => "https://sportiva.my.id/customer/daftarRiwayat",
            "button" => "Lihat Transaksi",
            "isi" => "Maaf! Extend Waktu Anda Tidak Dapat Diproses:<br><br>
                    <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br>
                    ".$dtransStr."<br>
                    <b>Durasi Extend: ".$extend->durasi_extend." jam</b><br>
                    <b>Total Transaksi: Rp ".number_format($extend->total, 0, ',', '.')."</b><br><br>
                    Jangan khawatir! Dana Anda telah kami kembalikan ke saldo wallet Anda. Cari dan sewa lapangan olahraga lain di Sportiva! 😊"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($cust->email_user, $dataNotif);

        return response()->json(['success' => true, 'message' => 'Berhasil Ditolak!']);
    }

    public function hapusAlat(Request $request) {
        //kasih pengecekan
        $dataDtrans = DB::table('dtrans')->where("id_dtrans","=",$request->id_dtrans)->where("deleted_at","=",null)->get()->first();
        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$dataDtrans->fk_id_htrans)->get()->first();
        $dataCust = DB::table('user')->where("id_user","=",$dataHtrans->fk_id_user)->get()->first();

        if ($dataHtrans->status_trans == "Diterima") {
            //ubah subtotal alat dan total di htrans
            $sub = (int)$dataHtrans->subtotal_alat;
            $sub -= $dataDtrans->subtotal_alat;
            $data2 = [
                "id" => $dataDtrans->fk_id_htrans,
                "subtotal" => $sub
            ];
            $trans = new htrans();
            $trans->updateSubtotalAlat($data2);
            
            $total = $dataHtrans->total_trans;
            $total -= $dataDtrans->subtotal_alat;
            $data3 = [
                "id" => $dataDtrans->fk_id_htrans,
                "total" => $total
            ];
            $trans->updateTotal($data3);

            //kembalikan uang alat ke cust
            $saldo = (int)$this->decodePrice($dataCust->saldo_user, "mysecretkey");
            $saldo += $dataDtrans->subtotal_alat;
            $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

            //update ke db
            $dataSaldo = [
                "id" => $dataCust->id_user,
                "saldo" => $enkrip
            ];
            $cust = new customer();
            $cust->updateSaldo($dataSaldo);

            //notif email ke cust
            $dataDtransAll = DB::table('dtrans')->where("fk_id_htrans","=",$dataHtrans->id_htrans)->where("deleted_at","=",null)->get();
            $dtransStr = "";
            if (!$dataDtransAll->isEmpty()) {
                foreach ($dataDtransAll as $key => $value) {
                    $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->fk_id_alat)->get()->first();
                    $dtransStr .= "<b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>";
                }
            }

            $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$dataHtrans->fk_id_lapangan)->get()->first();
            $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$dataDtrans->fk_id_alat)->get()->first();

            $dataNotif = [
                "subject" => "⚠️Transaksi Anda Telah Diubah!⚠️",
                "judul" => "Transaksi Anda Telah Diubah!",
                "nama_user" => $dataCust->nama_user,
                "url" => "https://sportiva.my.id/customer/daftarRiwayat",
                "button" => "Lihat Transaksi",
                "isi" => "Detail Transaksi<br><br>
                        <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br>
                        ".$dtransStr."<br><br>
                        <b>Alat Olahraga ".$dataAlat->nama_alat."</b><br>
                        Telah diubah! Jangan khawatir, dana sudah kami kembalikan ke saldo wallet anda!<br>
                        Terima kasih telah mempercayai layanan kami. Tetap Jaga Pola Sehat Anda bersama Sportiva! 😊"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($dataCust->email_user, $dataNotif);

            //delete
            date_default_timezone_set("Asia/Jakarta");
            $skrg = date("Y-m-d H:i:s");
            $data = [
                "id" => $request->id_dtrans,
                "tanggal" => $skrg
            ];
            $dt = new dtrans();
            $dt->softDelete($data);

            return response()->json(['success' => true, 'message' => 'Transaksi berhasil diedit dan dana berhasil dikembalikan ke customer!']);
        }
    }

    public function cetakNota(Request $request) {
        // status htrans berubah menjadi "selesai"
        $data = [
            "id" => $request->id_htrans,
            "status" => "Selesai"
        ];
        $trans = new htrans();
        $trans->updateStatus($data);

        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$request->id_htrans)->get()->first();
        $extend = DB::table('extend_htrans')->where("fk_id_htrans","=",$dataHtrans->id_htrans)->get()->first();
        $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$request->id_htrans)->where("deleted_at","=",null)->get();

        //total komisi dari alat miliknya sendiri
        $total_komisi_tempat = 0;
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                if ($value->fk_id_tempat == Session::get("dataRole")->id_tempat) {
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
        $saldo = (int)$this->decodePrice(Session::get("dataRole")->saldo_tempat, "mysecretkey");
        // dd($extend->subtotal_lapangan);
        $saldo += (int)$dataHtrans->subtotal_lapangan + $extend_subtotal + $total_komisi_tempat;

        // dd($dataHtrans->subtotal_lapangan + $extend_subtotal + $total_komisi_tempat);
        //enkripsi kembali saldo
        $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

        //update db tempat
        $dataSaldo = [
            "id" => Session::get("dataRole")->id_tempat,
            "saldo" => $enkrip
        ];
        $temp = new pihakTempat();
        $temp->updateSaldo($dataSaldo);

        //update session role
        $isiTemp = $temp->get_all_data_by_id(Session::get("dataRole")->id_tempat);
        Session::forget("dataRole");
        Session::put("dataRole", $isiTemp->first());

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
                        $extend_total_komisi = $extend_dtrans2->total_komisi_pemilik;
                    }
                    $saldo2 += (int)$value->total_komisi_pemilik + $extend_total_komisi;
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

        //notif ke customer
        $cust = DB::table('user')->where("id_user","=",$dataHtrans->fk_id_user)->get()->first();
        $dataLapangan = DB::table('lapangan_olahraga')->where("id_lapangan","=",$dataHtrans->fk_id_lapangan)->get()->first();

        $dtransStr = "";
        if (!$dataDtrans->isEmpty()) {
            foreach ($dataDtrans as $key => $value) {
                $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$value->fk_id_alat)->get()->first();
                $dtransStr .= "<b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>";
            }
        }
        
        $dataNotif = [
            "subject" => "🎉Transaksi Anda Telah Selesai!🎉",
            "judul" => "Transaksi Anda Telah Selesai!",
            "nama_user" => $cust->nama_user,
            "url" => "https://sportiva.my.id/customer/daftarRiwayat",
            "button" => "Lihat Transaksi",
            "isi" => "Yeay! Transaksi Anda telah selesai:<br><br>
                    <b>Nama Lapangan Olahraga: ".$dataLapangan->nama_lapangan."</b><br>
                    ".$dtransStr."<br>
                    <b>Total Transaksi: Rp ".number_format($dataHtrans->total_trans + $extend_total, 0, ',', '.')."</b><br><br>
                    Terima kasih telah mempercayai layanan kami. Tetap Jaga Pola Sehat Anda bersama Sportiva! 😊"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($cust->email_user, $dataNotif);

        //cetak nota
        $trans = DB::table('htrans')
                ->select("pihak_tempat.nama_tempat", "pihak_tempat.alamat_tempat", "htrans.kode_trans", "htrans.tanggal_sewa", "htrans.jam_sewa","htrans.durasi_sewa","lapangan_olahraga.nama_lapangan","lapangan_olahraga.harga_sewa_lapangan","extend_htrans.durasi_extend","htrans.subtotal_lapangan","extend_htrans.subtotal_lapangan as extend_subtotal", "htrans.subtotal_alat", "extend_htrans.subtotal_alat as extend_alat", "htrans.total_trans","extend_htrans.total")
                ->leftJoin("extend_htrans", function ($join) {
                    $join->on("htrans.id_htrans", "=", "extend_htrans.fk_id_htrans")
                         ->where("extend_htrans.status_extend", "=", "Diterima");
                })
                ->join("lapangan_olahraga","htrans.fk_id_lapangan","=","lapangan_olahraga.id_lapangan")
                ->join("pihak_tempat","htrans.fk_id_tempat","=","pihak_tempat.id_tempat")
                ->where("htrans.id_htrans","=",$request->id_htrans)
                ->get()
                ->first();
            // dd($trans);

        $dtrans = DB::table('dtrans')
                ->select("alat_olahraga.nama_alat","dtrans.harga_sewa_alat", "dtrans.subtotal_alat","extend_dtrans.subtotal_alat as extend_subtotal")
                ->rightJoin("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->where("htrans.id_htrans","=",$request->id_htrans)
                ->where("dtrans.deleted_at","=",null)
                ->get();
        // dd($trans);

        $param["htrans"] = $trans;
        $param["dtrans"] = $dtrans;
        return view("tempat.transaksi.cetakNota")->with($param);
    }
}
