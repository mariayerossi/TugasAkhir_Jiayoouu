<?php

namespace App\Http\Controllers;

use App\Models\customer;
use App\Models\dtrans;
use App\Models\extendDtrans;
use App\Models\extendHtrans;
use App\Models\filesLapanganOlahraga;
use App\Models\htrans;
use App\Models\jamKhusus;
use App\Models\kategori;
use App\Models\komplainTrans;
use App\Models\lapanganOlahraga;
use App\Models\notifikasi;
use App\Models\pihakTempat;
use Carbon\Carbon;
use App\Models\notifikasiEmail;
use App\Models\pemilikAlat;
use DateInterval;
use DateTime;
use DateTimeZone;
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
        // $request->validate([
        //     "tanggal" => "required",
        //     "mulai" => "required",
        //     "selesai" => "required"
        // ],[
        //     "tanggal.required" => "tanggal sewa tidak boleh kosong!",
        //     "mulai.required" => "jam mulai sewa tidak boleh kosong!",
        //     "selesai.required" => "jam selesai sewa tidak boleh kosong!"
        // ]);

        $date_mulai = new DateTime($request->mulai);
        $date_selesai = new DateTime($request->selesai);
        
        if ($date_selesai <= $date_mulai) {
            return response()->json(['success' => false, 'message' => 'Jam sewa tidak sesuai!']);
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
            // return redirect()->back()->with("error", "Tanggal atau waktu booking tidak valid! Booking harus dilakukan minimal 3 jam sebelum waktu sewa.");
            return response()->json(['success' => false, 'message' => 'Tanggal atau waktu booking tidak valid! Booking harus dilakukan minimal 3 jam sebelum waktu sewa.']);
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
                return response()->json(['success' => false, 'message' => 'Maaf, slot ini sudah dibooking!']);
            }
        }

        //cek apakah jam booking pas lapangan buka (operasional)
        $mulai = $request->tanggal . ' ' . $request->mulai;
        $selesai = $request->tanggal . ' ' . $request->selesai;

        // Mengonversi string menjadi objek DateTime
        $mulaiDateTime = new DateTime($mulai);
        $selesaiDateTime = new DateTime($selesai);

        // Daftar hari dalam bahasa Indonesia
        $daftarHari = array(
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        );

        // Mendapatkan hari dari tanggal awal
        $hariIndex = $mulaiDateTime->format('w');
        $hari = $daftarHari[$hariIndex];

        $mulaiDateTime1 = new DateTime($request->mulai);
        $selesaiDateTime1 = new DateTime($request->selesai);

        $cek = 0;
        $slot = DB::table('slot_waktu')->where("fk_id_lapangan","=",$request->id_lapangan)->get();
        if (!$slot->isEmpty()) {
            foreach ($slot as $key => $value) {
                if ($value->hari == $hari) {
                    $jamOperasionalMulai = new DateTime($value->jam_buka);
                    $jamOperasionalSelesai = new DateTime($value->jam_tutup);

                    if ($mulaiDateTime1 >= $jamOperasionalMulai && $selesaiDateTime1 <= $jamOperasionalSelesai) {
                        $cek = 1;
                    }
                }
            }
        }

        if ($cek == 0) {
            return response()->json(['success' => false, 'message' => 'Maaf, Tidak dapat menyewa ketika lapangan tutup!']);
        }

        //kasi pengecekan apakah ada jadwal tutup lapangan
        $cek2 = 0;
        $jam = new jamKhusus();
        $cekJam = $jam->get_all_data_by_lapangan($request->id_lapangan);
        // dd($cekJam);
        if (!$cekJam->isEmpty()) {
            foreach ($cekJam as $key => $value) {
                if ($value->tanggal == $request->tanggal) {
                    $jamMulai = new DateTime($value->tanggal." ".$value->jam_mulai);
                    $jamSelesai = new DateTime($value->tanggal." ".$value->jam_selesai);

                    $mulaiDateTime2 = new DateTime($request->tanggal." ".$request->mulai);
                    $selesaiDateTime2 = new DateTime($request->tanggal." ".$request->selesai);

                    if (($mulaiDateTime2 >= $jamMulai && $mulaiDateTime2 < $jamSelesai) || 
                        ($selesaiDateTime2 > $jamMulai && $selesaiDateTime2 <= $jamSelesai) ||
                        ($mulaiDateTime2 <= $jamMulai && $selesaiDateTime2 >= $jamSelesai)) {
                        $cek2 = 1;
                        break; // Berhenti loop jika ada tabrakan
                    }
                }
            }
        }
        // dd($cek2);

        if ($cek2 == 1) {
            return back()->with('error', 'Maaf, Tidak dapat menyewa ketika lapangan tutup!');
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
        $custt = new customer();
        $saldo_cust = $custt->get_all_data_by_id(Session::get("dataRole")->id_user)->first()->saldo_user;

        $saldo = (int)$this->decodePrice($saldo_cust, "mysecretkey");
        if ($saldo < $total) {
            return response()->json(['success' => false, 'message' => 'Saldo anda tidak cukup! Silahkan top up saldo anda.']);
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

        //notif web ke pihak tempat
        $dataNotifWeb = [
            "keterangan" => "Transaksi Baru ".$dataLapangan->nama_lapangan,
            "waktu" => $skrg,
            "link" => "/tempat/transaksi/detailTransaksi/".$id,
            "user" => null,
            "pemilik" => null,
            "tempat" => $request->id_tempat,
            "admin" => null
        ];
        $notifWeb = new notifikasi();
        $notifWeb->insertNotifikasi($dataNotifWeb);

        $tanggal2 = $request->tanggal." ".$request->mulai;
        $sewa2 = new DateTime($tanggal2);
        $sewa2->sub(new DateInterval('PT2H')); // mengurangkan 2 jam
        $sew2 = $sewa2->format('Y-m-d H:i:s');

        $tanggalAwal3 = $sew2;
        $tanggalObjek3 = DateTime::createFromFormat('Y-m-d H:i:s', $tanggalAwal3);
        $carbonDate3 = \Carbon\Carbon::parse($tanggalObjek3)->locale('id');
        $tanggalBaru3 = $carbonDate3->isoFormat('D MMMM YYYY HH:mm');

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
                    Harap segera terima transaksi ini sampai ".$tanggalBaru3."! Jika melebihi waktu status akan otomatis dibatalkan!"
        ];
        $e = new notifikasiEmail();
        $e->sendEmail($dataTempat->email_tempat, $dataNotif);

        return response()->json(['success' => true, 'message' => 'Berhasil booking lapangan olahraga!']);
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
            ->orderBy("htrans.id_htrans","DESC")
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
            ->orderBy("htrans.id_htrans","DESC")
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
            ->orderBy("htrans.id_htrans","DESC")
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
            ->orderBy("htrans.id_htrans","DESC")
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
            ->orderBy("htrans.id_htrans","DESC")
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
            ->orderBy("htrans.id_htrans","DESC")
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
            ->orderBy("htrans.id_htrans","DESC")
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
            ->orderBy("htrans.id_htrans","DESC")
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

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");
        $waktuSkrg = new DateTime($skrg);

        $sewa = $request->tanggal_sewa." ".$request->jam_sewa;
        $waktuMulai = new DateTime($sewa);
        $waktuMulai->sub(new DateInterval('PT10M'));
        // dd($skrg);

        if ($waktuSkrg < $waktuMulai) {
            return response()->json(['success' => false, 'message' => 'Waktu sewa belum tiba!']);
        }

        if ($request->kode != $request->kode_htrans) {
            return response()->json(['success' => false, 'message' => 'Kode Transaksi salah!']);
        }

        //update status htrans
        $data = [
            "id" => $request->id_htrans,
            "status" => "Berlangsung"
        ];
        $trans = new htrans();
        $trans->updateStatus($data);

        return response()->json(['success' => true, 'message' => 'Berhasil mengkonfirmasi transaksi!']);
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

        $kot = new lapanganOlahraga();
        $param["kota"] = $kot->get_kota();

        $data = [];

        if (Session::has("cart") && Session::get("cart") != null) {
            foreach (Session::get("cart") as $key => $value) {
                if ($value['user'] == Session::get("dataRole")->id_user) {
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

        $kot = new lapanganOlahraga();
        $param["kota"] = $kot->get_kota();

        $trans = DB::table('htrans')
                ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.id_lapangan", "lapangan_olahraga.nama_lapangan","htrans.kode_trans","htrans.total_trans","htrans.tanggal_sewa", "htrans.jam_sewa", "htrans.durasi_sewa", "htrans.status_trans", "htrans.tanggal_trans")
                ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("htrans.fk_id_user", "=", Session::get("dataRole")->id_user)
                // ->where("htrans.status_trans", "!=", "Selesai")
                ->orderBy("htrans.id_htrans", "desc")
                ->get();

        $param["trans"] = $trans;
        return view("customer.riwayat")->with($param);
    }

    public function detailTransaksi(Request $request) {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $kot = new lapanganOlahraga();
        $param["kota"] = $kot->get_kota();
        
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

        //cek apakah jam booking pas lapangan buka (operasional)
        $mulai = $request->tanggal . ' ' . $request->mulai;
        $selesai = $request->tanggal . ' ' . $request->selesai;

        // Mengonversi string menjadi objek DateTime
        $mulaiDateTime = new DateTime($mulai);
        $selesaiDateTime = new DateTime($selesai);

        // Daftar hari dalam bahasa Indonesia
        $daftarHari = array(
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        );

        // Mendapatkan hari dari tanggal awal
        $hariIndex = $mulaiDateTime->format('w');
        $hari = $daftarHari[$hariIndex];
        // dd($hari);

        $mulaiDateTime1 = new DateTime($request->mulai);
        $selesaiDateTime1 = new DateTime($request->selesai);

        $cek1 = 0;
        $slot = DB::table('slot_waktu')->where("fk_id_lapangan","=",$request->id_lapangan)->get();
        if (!$slot->isEmpty()) {
            foreach ($slot as $key => $value) {
                if ($value->hari == $hari) {
                    $jamOperasionalMulai = new DateTime($value->jam_buka);
                    $jamOperasionalSelesai = new DateTime($value->jam_tutup);

                    if ($mulaiDateTime1 >= $jamOperasionalMulai && $selesaiDateTime1 <= $jamOperasionalSelesai) {
                       $cek1 = 1;
                    }
                }
            }
        }

        if ($cek1 == 0) {
            return back()->with('error', 'Maaf, Tidak dapat menyewa ketika lapangan tutup!');
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

        //kasi pengecekan apakah ada jadwal tutup lapangan
        $cek2 = 0;
        $jam = new jamKhusus();
        $cekJam = $jam->get_all_data_by_lapangan($request->id_lapangan);
        // dd($cekJam);
        if (!$cekJam->isEmpty()) {
            foreach ($cekJam as $key => $value) {
                if ($value->tanggal == $request->tanggal) {
                    $jamMulai = new DateTime($value->tanggal." ".$value->jam_mulai);
                    $jamSelesai = new DateTime($value->tanggal." ".$value->jam_selesai);

                    $mulaiDateTime2 = new DateTime($request->tanggal." ".$request->mulai);
                    $selesaiDateTime2 = new DateTime($request->tanggal." ".$request->selesai);

                    if (($mulaiDateTime2 >= $jamMulai && $mulaiDateTime2 < $jamSelesai) || 
                        ($selesaiDateTime2 > $jamMulai && $selesaiDateTime2 <= $jamSelesai) ||
                        ($mulaiDateTime2 <= $jamMulai && $selesaiDateTime2 >= $jamSelesai)) {
                        $cek2 = 1;
                        break; // Berhenti loop jika ada tabrakan
                    }
                }
            }
        }
        // dd($cek2);

        if ($cek2 == 1) {
            return back()->with('error', 'Maaf, Tidak dapat menyewa ketika lapangan tutup!');
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

        $custt = new customer();
        $saldo_cust = $custt->get_all_data_by_id(Session::get("dataRole")->id_user)->first()->saldo_user;
        $saldo = (int)$this->decodePrice($saldo_cust, "mysecretkey");

        $param["data"] = [
            "tanggal" => $request->tanggal,
            "mulai" => $request->mulai,
            "selesai" => $request->selesai,
            "durasi" => $durasi_sewa,
            "id_lapangan" => $request->id_lapangan,
            "id_tempat" => $request->id_tempat,
            "subtotal_alat" => $subtotal_alat,
            "saldo" => $saldo
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

        //cek apakah jam booking pas lapangan buka (operasional)
        $mulai = $dataHtrans->tanggal_sewa . ' ' . $dataHtrans->jam_sewa;
        $selesai1 = $dataHtrans->tanggal_sewa . ' ' . $selesai;

        // Mengonversi string menjadi objek DateTime
        $mulaiDateTime = new DateTime($mulai);
        $selesaiDateTime = new DateTime($selesai1);

        // Daftar hari dalam bahasa Indonesia
        $daftarHari = array(
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        );

        // Mendapatkan hari dari tanggal awal
        $hariIndex = $mulaiDateTime->format('w');
        $hari = $daftarHari[$hariIndex];

        $mulaiDateTime1 = new DateTime($dataHtrans->jam_sewa);
        $selesaiDateTime1 = new DateTime($selesai);

        // $slot = DB::table('slot_waktu')->where("fk_id_lapangan","=",$dataHtrans->fk_id_lapangan)->get();
        // if (!$slot->isEmpty()) {
        //     foreach ($slot as $key => $value) {
        //         if ($value->hari == $hari) {
        //             $jamOperasionalMulai = new DateTime($value->jam_buka);
        //             $jamOperasionalSelesai = new DateTime($value->jam_tutup);

        //             if ($mulaiDateTime1 >= $jamOperasionalMulai && $selesaiDateTime1 <= $jamOperasionalSelesai) {
        //                 // Jam sewa sesuai dengan jam operasional
        //                 // Lakukan tindakan yang sesuai
        //             } else {
        //                 // Jam sewa tidak sesuai dengan jam operasional, berikan pesan error
        //                 return response()->json(['success' => false, 'message' => 'Maaf, Tidak dapat menyewa ketika lapangan tutup!']);
        //             }
        //         }
        //         else {
        //             return response()->json(['success' => false, 'message' => 'Maaf, Tidak dapat menyewa ketika lapangan tutup!']);
        //         }
        //     }
        // }

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

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");

        //notif web ke customer
        $dataNotifWeb = [
            "keterangan" => "Transaksi Booking ".$dataLapangan->nama_lapangan." Telah Diterima",
            "waktu" => $skrg,
            "link" => "/customer/daftarRiwayat",
            "user" => $dataHtrans->fk_id_user,
            "pemilik" => null,
            "tempat" => null,
            "admin" => null
        ];
        $notifWeb = new notifikasi();
        $notifWeb->insertNotifikasi($dataNotifWeb);
        
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

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");

        //notif web ke customer
        $dataNotifWeb = [
            "keterangan" => "Transaksi Booking ".$dataLapangan->nama_lapangan." Telah Ditolak",
            "waktu" => $skrg,
            "link" => "/customer/daftarRiwayat",
            "user" => $dataHtrans->fk_id_user,
            "pemilik" => null,
            "tempat" => null,
            "admin" => null
        ];
        $notifWeb = new notifikasi();
        $notifWeb->insertNotifikasi($dataNotifWeb);

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

        return response()->json(['success' => true, 'message' => 'Transaksi Berhasil Ditolak!']);
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
            return response()->json(['success' => false, 'message' => 'Tidak dapat membatalkan transaksi! Transaksi telah diterima!']);
        }

        //ndak jd dipotong 5% soal e lek trans ws diterima, ga isa dibatalno

        //pengembalian dana
        $user = new customer();
        $saldo_user = $user->get_all_data_by_id(Session::get("dataRole")->id_user);
        $saldo = (int)$this->decodePrice($saldo_user->first()->saldo_user, "mysecretkey");

        $saldo += $trans->total_trans;

        //enkripsi kembali saldo
        $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

        //update db user
        $dataSaldo = [
            "id" => Session::get("dataRole")->id_user,
            "saldo" => $enkrip
        ];
        $cust = new customer();
        $cust->updateSaldo($dataSaldo);

        // // //update session role
        
        // Session::forget("dataRole");
        // Session::put("dataRole", $isiUser->first());

        // //total_denda masuk ke saldo pihak tempat
        // $saldoTempatAwal = DB::table('pihak_tempat')->where("id_tempat","=",$trans->fk_id_tempat)->get()->first()->saldo_tempat;
        // $saldoTempat = (int)$this->decodePrice($saldoTempatAwal, "mysecretkey");

        // $saldoTempat += $total_denda;
        // $saldoAkhir = $this->encodePrice((string)$saldoTempat, "mysecretkey");

        // $dataSaldoTempat = [
        //     "id" => $trans->fk_id_tempat,
        //     "saldo" => $saldoAkhir
        // ];
        // $temp = new pihakTempat();
        // $temp->updateSaldo($dataSaldoTempat);


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

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");

        //notif web ke pihak tempat
        $dataNotifWeb = [
            "keterangan" => "Transaksi Booking ".$trans->nama_lapangan." Dibatalkan Customer",
            "waktu" => $skrg,
            "link" => "/tempat/transaksi/detailTransaksi/".$request->id_htrans,
            "user" => null,
            "pemilik" => null,
            "tempat" => $trans->fk_id_tempat,
            "admin" => null
        ];
        $notifWeb = new notifikasi();
        $notifWeb->insertNotifikasi($dataNotifWeb);

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
                    Terima Kasih telah menggunakan Sportiva! 😊"
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

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");

        //notif web ke pihak tempat
        $dataNotifWeb = [
            "keterangan" => "Transaksi Booking ".$trans->nama_lapangan." Dibatalkan Pihak Tempat Olahraga",
            "waktu" => $skrg,
            "link" => "/customer/daftarRiwayat",
            "user" => $trans->fk_id_user,
            "pemilik" => null,
            "tempat" => null,
            "admin" => null
        ];
        $notifWeb = new notifikasi();
        $notifWeb->insertNotifikasi($dataNotifWeb);

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

        $kot = new lapanganOlahraga();
        $param["kota"] = $kot->get_kota();

        $htrans = DB::table('htrans')
                ->select("htrans.id_htrans","htrans.kode_trans","lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan","files_lapangan.nama_file_lapangan","lapangan_olahraga.harga_sewa_lapangan","htrans.tanggal_sewa","htrans.jam_sewa","htrans.durasi_sewa", "lapangan_olahraga.deleted_at")
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

        if ($htrans->deleted_at != null) {
            return back()->with('error', 'Lapangan tidak tersedia!');
        }
        
        $jam_sewa = $htrans->jam_sewa;
        $durasi_sewa = $htrans->durasi_sewa;
        $booking_jam_selesai1 = date('H:i', strtotime("+$durasi_sewa hour", strtotime($jam_sewa)));

        $booking_jam_selesai2 = date('H:i', strtotime("+$request->durasi hour", strtotime($booking_jam_selesai1)));

        $cek = DB::table('htrans')
                ->select("htrans.tanggal_sewa", "htrans.jam_sewa", "htrans.durasi_sewa","extend_htrans.jam_sewa as jam_extend","extend_htrans.durasi_extend as durasi_extend")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->where("htrans.tanggal_sewa", "=", $htrans->tanggal_sewa)
                ->where("htrans.fk_id_lapangan","=",$htrans->id_lapangan)
                ->where(function($query) {
                    $query->where("htrans.status_trans", "=", "Diterima")
                          ->orWhere("htrans.status_trans", "=", "Berlangsung");
                })
                ->get();
        // dd($cek);

        //kasi pengecekan apakah tanggal dan jamnya bertubrukan dgn htrans dan extend_htrans lain
        if (!$cek->isEmpty()) {
            $conflict = false;
            foreach ($cek as $value) {
                //kalau ga ada extend waktu nya
                if ($value->jam_extend == null) {
                    $booking_jam_selesai3 = date('H:i', strtotime("+$value->durasi_sewa hour", strtotime($value->jam_sewa)));
                
                    if (($booking_jam_selesai1 >= $value->jam_sewa && $booking_jam_selesai1 < $booking_jam_selesai3) || 
                        ($booking_jam_selesai2 > $value->jam_sewa && $booking_jam_selesai2 <= $booking_jam_selesai3) ||
                        ($booking_jam_selesai1 <= $value->jam_sewa && $booking_jam_selesai2 >= $booking_jam_selesai3)) {
                        
                        $conflict = true;
                        break;
                    }
                }
                else {
                    $booking_jam_selesai3 = date('H:i', strtotime("+$value->durasi_extend hour", strtotime($value->jam_extend)));

                    if (($booking_jam_selesai1 >= $value->jam_sewa && $booking_jam_selesai1 < $booking_jam_selesai3) || 
                        ($booking_jam_selesai2 > $value->jam_sewa && $booking_jam_selesai2 <= $booking_jam_selesai3) ||
                        ($booking_jam_selesai1 <= $value->jam_sewa && $booking_jam_selesai2 >= $booking_jam_selesai3)) {
                        
                        $conflict = true;
                        break;
                    }
                }
            }
            // dd($conflict);
            if ($conflict) {
                // Ada konflik dengan booking yang ada
                return back()->with('error', 'Maaf, slot ini sudah dibooking di jam '.$value->jam_sewa.'!');
            }
        }

        //cek apakah jam booking pas lapangan buka (operasional)
        $mulai = $htrans->tanggal_sewa . ' ' . $booking_jam_selesai1;
        $selesai = $htrans->tanggal_sewa . ' ' . $booking_jam_selesai2;

        // dd($htrans->tanggal_sewa);

        // Mengonversi string menjadi objek DateTime
        $mulaiDateTime = new DateTime($mulai);
        $selesaiDateTime = new DateTime($selesai);

        // Daftar hari dalam bahasa Indonesia
        $daftarHari = array(
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        );

        // Mendapatkan hari dari tanggal awal
        $hariIndex = $mulaiDateTime->format('w');
        $hari = $daftarHari[$hariIndex];

        $mulaiDateTime1 = new DateTime($booking_jam_selesai1);
        $selesaiDateTime1 = new DateTime($booking_jam_selesai2);

        // dd($mulaiDateTime1);

        $cek = 0;
        $slot = DB::table('slot_waktu')->where("fk_id_lapangan","=",$htrans->id_lapangan)->get();
        if (!$slot->isEmpty()) {
            foreach ($slot as $key => $value) {
                if ($value->hari == $hari) {
                    $jamOperasionalMulai = new DateTime($value->jam_buka);
                    $jamOperasionalSelesai = new DateTime($value->jam_tutup);

                    if ($mulaiDateTime1 >= $jamOperasionalMulai && $selesaiDateTime1 <= $jamOperasionalSelesai) {
                        $cek = 1;
                    }
                }
            }
        }

        if ($cek == 0) {
            return back()->with('error', 'Maaf, Tidak dapat menyewa ketika lapangan tutup!');
        }

        //kasi pengecekan apakah ada jadwal tutup lapangan
        $cek2 = 0;
        $jam = new jamKhusus();
        $cekJam = $jam->get_all_data_by_lapangan($htrans->id_lapangan);
        // dd($cekJam);
        if (!$cekJam->isEmpty()) {
            foreach ($cekJam as $key => $value) {
                if ($value->tanggal == $htrans->tanggal_sewa) {
                    $jamMulai = new DateTime($value->tanggal." ".$value->jam_mulai);
                    $jamSelesai = new DateTime($value->tanggal." ".$value->jam_selesai);

                    if (($mulaiDateTime >= $jamMulai && $mulaiDateTime < $jamSelesai) || 
                        ($selesaiDateTime > $jamMulai && $selesaiDateTime <= $jamSelesai) ||
                        ($mulaiDateTime <= $jamMulai && $selesaiDateTime >= $jamSelesai)) {
                        $cek2 = 1;
                        break; // Berhenti loop jika ada tabrakan
                    }
                }
            }
        }
        // dd($selesaiDateTime);

        if ($cek2 == 1) {
            return back()->with('error', 'Maaf, Tidak dapat menyewa ketika lapangan tutup!');
        }

        $param["durasi"] = $request->durasi;
        $param["jam_mulai"] = $booking_jam_selesai1;
        $param["jam_selesai"] = $booking_jam_selesai2;
        $param["trans"] = $htrans;
        $param["dtrans"] = $dataDtrans;

        // return view("customer.detailTambahWaktu")->with($param);

        if (Session::get("role") == "customer") {
            return view("customer.detailTambahWaktu")->with($param);
        }
        else {
            return view("tempat.transaksi.detailTambahWaktu")->with($param);
        }
    }

    public function tambahWaktu(Request $request) {
        // $htrans = DB::table('htrans')
        //         ->where("htrans.id_htrans","=",$request->id_htrans)
        //         ->get()
        //         ->first();
        $htrans = DB::table('htrans')
                ->select("htrans.id_htrans","htrans.kode_trans","lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan","files_lapangan.nama_file_lapangan","lapangan_olahraga.harga_sewa_lapangan","htrans.tanggal_sewa","htrans.jam_sewa","htrans.durasi_sewa","htrans.fk_id_tempat","htrans.fk_id_lapangan", "lapangan_olahraga.deleted_at")
                ->join("lapangan_olahraga","htrans.fk_id_lapangan","=","lapangan_olahraga.id_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("htrans.id_htrans","=",$request->id_htrans)
                ->get()
                ->first();
        $dtrans = DB::table('dtrans')
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->where("dtrans.fk_id_htrans","=",$request->id_htrans)
                ->where("dtrans.deleted_at","=",null)
                ->get();
        // dd($dtrans);
        // dd("halo");

        if ($htrans->deleted_at != null) {
            return response()->json(['success' => false, 'message' => 'Lapangan Tidak Tersedia']);
        }

        $jam_sewa = $htrans->jam_sewa;
        $durasi_sewa = $htrans->durasi_sewa;
        $booking_jam_selesai1 = date('H:i', strtotime("+$durasi_sewa hour", strtotime($jam_sewa)));//jam selesai htrans

        $booking_jam_selesai2 = date('H:i', strtotime("+$request->durasi hour", strtotime($booking_jam_selesai1)));//jam selesai extend

        $cek = DB::table('htrans')
                ->select("htrans.tanggal_sewa", "htrans.jam_sewa", "htrans.durasi_sewa","extend_htrans.jam_sewa as jam_extend","extend_htrans.durasi_extend as durasi_extend")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->where("htrans.tanggal_sewa", "=", $htrans->tanggal_sewa)
                ->where("htrans.fk_id_lapangan","=",$htrans->id_lapangan)
                ->where(function($query) {
                    $query->where("htrans.status_trans", "=", "Diterima")
                          ->orWhere("htrans.status_trans", "=", "Berlangsung");
                })
                ->get();
        // dd($cek);

        //kasi pengecekan apakah tanggal dan jamnya bertubrukan
        if (!$cek->isEmpty()) {
            $conflict = false;
            foreach ($cek as $value) {
                //kalau ga ada extend waktu nya
                if ($value->jam_extend == null) {
                    $booking_jam_selesai3 = date('H:i', strtotime("+$value->durasi_sewa hour", strtotime($value->jam_sewa)));
                
                    if (($booking_jam_selesai1 >= $value->jam_sewa && $booking_jam_selesai1 < $booking_jam_selesai3) || 
                        ($booking_jam_selesai2 > $value->jam_sewa && $booking_jam_selesai2 <= $booking_jam_selesai3) ||
                        ($booking_jam_selesai1 <= $value->jam_sewa && $booking_jam_selesai2 >= $booking_jam_selesai3)) {
                        
                        $conflict = true;
                        break;
                    }
                }
                else {
                    $booking_jam_selesai3 = date('H:i', strtotime("+$value->durasi_extend hour", strtotime($value->jam_extend)));

                    if (($booking_jam_selesai1 >= $value->jam_sewa && $booking_jam_selesai1 < $booking_jam_selesai3) || 
                        ($booking_jam_selesai2 > $value->jam_sewa && $booking_jam_selesai2 <= $booking_jam_selesai3) ||
                        ($booking_jam_selesai1 <= $value->jam_sewa && $booking_jam_selesai2 >= $booking_jam_selesai3)) {
                        
                        $conflict = true;
                        break;
                    }
                }
            }

            if ($conflict) {
                // Ada konflik dengan booking yang ada
                return response()->json(['success' => false, 'message' => 'Maaf, slot ini sudah dibooking di jam '.$value->jam_sewa.'!']);
            }
        }

        //cek apakah jam booking pas lapangan buka (operasional)
        $mulai = $htrans->tanggal_sewa . ' ' . $booking_jam_selesai1;
        $selesai = $htrans->tanggal_sewa . ' ' . $booking_jam_selesai2;

        // dd($htrans->tanggal_sewa);

        // Mengonversi string menjadi objek DateTime
        $mulaiDateTime = new DateTime($mulai);
        $selesaiDateTime = new DateTime($selesai);

        // Daftar hari dalam bahasa Indonesia
        $daftarHari = array(
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        );

        // Mendapatkan hari dari tanggal awal
        $hariIndex = $mulaiDateTime->format('w');
        $hari = $daftarHari[$hariIndex];

        $mulaiDateTime1 = new DateTime($booking_jam_selesai1);
        $selesaiDateTime1 = new DateTime($booking_jam_selesai2);

        // dd($mulaiDateTime1);

        $cek = 0;
        $slot = DB::table('slot_waktu')->where("fk_id_lapangan","=",$htrans->id_lapangan)->get();
        if (!$slot->isEmpty()) {
            foreach ($slot as $key => $value) {
                if ($value->hari == $hari) {
                    // dd("halo");
                    $jamOperasionalMulai = new DateTime($value->jam_buka);
                    $jamOperasionalSelesai = new DateTime($value->jam_tutup);

                    if ($mulaiDateTime1 >= $jamOperasionalMulai && $selesaiDateTime1 <= $jamOperasionalSelesai) {
                        $cek = 1;
                    }
                }
            }
        }
        // dd($cek);

        if ($cek == 0) {
            // return redirect()->back()->with('error', 'Maaf, Tidak dapat menyewa ketika lapangan tutup!');
            return response()->json(['success' => false, 'message' => 'Maaf, Tidak dapat menyewa ketika lapangan tutup!']);
        }
        
        //kasi pengecekan apakah ada jadwal tutup lapangan
        $cek2 = 0;
        $jam = new jamKhusus();
        $cekJam = $jam->get_all_data_by_lapangan($htrans->id_lapangan);
        // dd($cekJam);
        if (!$cekJam->isEmpty()) {
            foreach ($cekJam as $key => $value) {
                if ($value->tanggal == $htrans->tanggal_sewa) {
                    $jamMulai = new DateTime($value->tanggal." ".$value->jam_mulai);
                    $jamSelesai = new DateTime($value->tanggal." ".$value->jam_selesai);

                    if (($mulaiDateTime >= $jamMulai && $mulaiDateTime < $jamSelesai) || 
                        ($selesaiDateTime > $jamMulai && $selesaiDateTime <= $jamSelesai) ||
                        ($mulaiDateTime <= $jamMulai && $selesaiDateTime >= $jamSelesai)) {
                        $cek2 = 1;
                        break; // Berhenti loop jika ada tabrakan
                    }
                }
            }
        }
        // dd($selesaiDateTime);

        if ($cek2 == 1) {
            return back()->with('error', 'Maaf, Tidak dapat menyewa ketika lapangan tutup!');
        }

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
        // dd($cek);

        $total_komisi_tempat = $komisi_tempat * $request->durasi;
        // dd($total_komisi_tempat);

        $persen_tempat = 0.09;
        $pendapatan_tempat = ($request->subtotal_lapangan + $total_komisi_tempat) * $persen_tempat;
        // dd($pendapatan_tempat);

        if (Session::get("role") == "customer") {
            $data = [
                "id_htrans" => $request->id_htrans,
                "tanggal" => $htrans->tanggal_sewa,
                "jam" => $request->jam,
                "durasi" => (int)$request->durasi,
                "lapangan" => (int)$request->subtotal_lapangan,
                "alat" => (int)$request->subtotal_alat,
                "total" => (int)$request->total,
                "pendapatan" => (int)$pendapatan_tempat,
                "status" => "Menunggu"
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
            $custt = new customer();
            $saldo_cust = $custt->get_all_data_by_id(Session::get("dataRole")->id_user)->first()->saldo_user;

            $saldo = (int)$this->decodePrice($saldo_cust, "mysecretkey");
            if ($saldo < (int)$request->total) {
                return response()->json(['success' => false, 'message' => 'Saldo anda tidak cukup! Silahkan top up saldo anda.']);
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

            date_default_timezone_set("Asia/Jakarta");
            $skrg = date("Y-m-d H:i:s");

            //notif web ke pihak tempat
            $dataNotifWeb = [
                "keterangan" => "Extend Waktu Baru ".$dataLapangan->nama_lapangan,
                "waktu" => $skrg,
                "link" => "/tempat/transaksi/detailTransaksi/".$request->id_htrans,
                "user" => null,
                "pemilik" => null,
                "tempat" => $htrans->fk_id_tempat,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);

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
                        Harap segera terima extend ini sampai ".$booking_jam_selesai2. "! Jika melebihi waktu status akan otomatis dibatalkan!"
            ];
            $e = new notifikasiEmail();
            $e->sendEmail($dataTempat->email_tempat, $dataNotif);
        }
        else {
            $data = [
                "id_htrans" => $request->id_htrans,
                "tanggal" => $htrans->tanggal_sewa,
                "jam" => $request->jam,
                "durasi" => (int)$request->durasi,
                "lapangan" => (int)$request->subtotal_lapangan,
                "alat" => (int)$request->subtotal_alat,
                "total" => (int)$request->total,
                "pendapatan" => (int)$pendapatan_tempat,
                "status" => "Diterima"
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
            $temp = new pihakTempat();
            $saldo_temp = $temp->get_all_data_by_id(Session::get("dataRole")->id_tempat)->first()->saldo_tempat;

            $saldo = (int)$this->decodePrice($saldo_temp, "mysecretkey");
            if ($saldo < (int)$request->total) {
                return response()->json(['success' => false, 'message' => 'Tidak bisa melakukan extend! Saldo anda tidak cukup.']);
            }
            //saldo dipotong sebesar total
            $saldo -= (int)$request->total;

            //enkripsi kembali saldo
            $enkrip = $this->encodePrice((string)$saldo, "mysecretkey");

            //update db user
            $dataSaldo = [
                "id" => Session::get("dataRole")->id_tempat,
                "saldo" => $enkrip
            ];
            $temp->updateSaldo($dataSaldo);
        }
        // return redirect()->back()->with("success", "Berhasil melakukan extend waktu! menunggu konfirmasi pemilik tempat olahraga");
        return response()->json(['success' => true, 'message' => 'Berhasil melakukan extend waktu!']);
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
                ->where(function($query) {
                    $query->where("status_trans", "=", "Diterima")
                          ->orWhere("status_trans", "=", "Berlangsung");
                })
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

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");

        //notif web ke customer
        $dataNotifWeb = [
            "keterangan" => "Extend Waktu ".$dataLapangan->nama_lapangan." Telah Diterima",
            "waktu" => $skrg,
            "link" => "/customer/daftarRiwayat",
            "user" => $dataHtrans->fk_id_user,
            "pemilik" => null,
            "tempat" => null,
            "admin" => null
        ];
        $notifWeb = new notifikasi();
        $notifWeb->insertNotifikasi($dataNotifWeb);
        
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

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");

        //notif web ke customer
        $dataNotifWeb = [
            "keterangan" => "Extend Waktu ".$dataLapangan->nama_lapangan." Ditolak",
            "waktu" => $skrg,
            "link" => "/customer/daftarRiwayat",
            "user" => $dataHtrans->fk_id_user,
            "pemilik" => null,
            "tempat" => null,
            "admin" => null
        ];
        $notifWeb = new notifikasi();
        $notifWeb->insertNotifikasi($dataNotifWeb);

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

            date_default_timezone_set("Asia/Jakarta");
            $skrg = date("Y-m-d H:i:s");

            //notif web ke customer
            $dataNotifWeb = [
                "keterangan" => "Transaksi Booking ".$dataLapangan->nama_lapangan." Diubah Pihak Tempat Olahraga",
                "waktu" => $skrg,
                "link" => "/customer/daftarRiwayat",
                "user" => $dataHtrans->fk_id_user,
                "pemilik" => null,
                "tempat" => null,
                "admin" => null
            ];
            $notifWeb = new notifikasi();
            $notifWeb->insertNotifikasi($dataNotifWeb);

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
        $dataHtrans = DB::table('htrans')->where("id_htrans","=",$request->id_htrans)->get()->first();
        // dd($dataHtrans);
        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");
        $skrg2 = new DateTime($skrg);

        $jam_sewa = $dataHtrans->jam_sewa;
        $durasi_sewa = $dataHtrans->durasi_sewa;
        $booking_jam_selesai1 = date('H:i:s', strtotime("+$durasi_sewa hour", strtotime($jam_sewa)));
        $tgl = $dataHtrans->tanggal_sewa." ".$booking_jam_selesai1;
        $tgl2 = new DateTime($tgl);
        
        // dd($tgl2);

        if ($skrg2 < $tgl2) {
            return redirect()->back()->with("error", "Persewaan lapangan belum selesai!");
        }

        // dd("lanjut");

        $data = [
            "id" => $request->id_htrans,
            "status" => "Selesai"
        ];
        $trans = new htrans();
        $trans->updateStatus($data);

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
        $saldo += (int)$dataHtrans->subtotal_lapangan + $extend_subtotal + $total_komisi_tempat - $dataHtrans->pendapatan_website_lapangan;

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

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");

        //notif web ke customer
        $dataNotifWeb = [
            "keterangan" => "Transaksi Booking ".$dataLapangan->nama_lapangan." Telah Selesai",
            "waktu" => $skrg,
            "link" => "/customer/daftarRiwayat",
            "user" => $dataHtrans->fk_id_user,
            "pemilik" => null,
            "tempat" => null,
            "admin" => null
        ];
        $notifWeb = new notifikasi();
        $notifWeb->insertNotifikasi($dataNotifWeb);
        
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

    public function detailTransaksiTempat($id) {
        $htrans = new htrans();
        $param["htrans"] = $htrans->get_all_data_by_id($id);
        $dtrans = new dtrans();
        $param["dtrans"] = $dtrans->get_all_data_by_id_htrans($id);
        $ext = new extendHtrans();
        $param["extend"] = $ext->get_all_data_by_id_htrans($id);
        $komplain = new komplainTrans();
        $param["komplain"] = $komplain->get_all_data_by_id_htrans($id);

        // $ext_d = new extendDtrans();
        // $id_extend = $ext->get_all_data_by_id_htrans($id)->first()->id_extend_htrans;
        // $param["extendDtrans"] = $ext_d->get_all_data_by_id_extend_htrans($id_extend);

        if (!$htrans->get_all_data_by_id($id)->isEmpty()) {
            $user = new customer();
            $id_user = $htrans->get_all_data_by_id($id)->first()->fk_id_user;
            $param["dataUser"] = $user->get_all_data_by_id($id_user)->first();

            $lapangan = new lapanganOlahraga();
            $id_lapangan = $htrans->get_all_data_by_id($id)->first()->fk_id_lapangan;
            $param["dataLapangan"] = $lapangan->get_all_data_by_id2($id_lapangan)->first();

            $files_lapangan = new filesLapanganOlahraga();
            $param["dataFileLapangan"] = $files_lapangan->get_all_data($id_lapangan)->first();
        }
        
        return view("tempat.transaksi.detailTransaksi")->with($param);
    }

    public function editTransaksiTempat($id) {
        $htrans = new htrans();
        $param["htrans"] = $htrans->get_all_data_by_id($id);
        $dtrans = new dtrans();
        $param["dtrans"] = $dtrans->get_all_data_by_id_htrans($id);

        $user = new customer();
        $id_user = $htrans->get_all_data_by_id($id)->first()->fk_id_user;
        $param["dataUser"] = $user->get_all_data_by_id($id_user)->first();

        $lapangan = new lapanganOlahraga();
        $id_lapangan = $htrans->get_all_data_by_id($id)->first()->fk_id_lapangan;
        $param["dataLapangan"] = $lapangan->get_all_data_by_id2($id_lapangan)->first();

        $file_lapangan = new filesLapanganOlahraga();
        $param["dataFileLapangan"] = $file_lapangan->get_all_data($id_lapangan)->first();

        return view("tempat.transaksi.editTrans")->with($param);
    }

    public function detailTransaksiAdmin($id) {
        $htrans = new htrans();
        $param["htrans"] = $htrans->get_all_data_by_id($id);
        $dtrans = new dtrans();
        $param["dtrans"] = $dtrans->get_all_data_by_id_htrans($id);
        $ext = new extendHtrans();
        $param["extend"] = $ext->get_all_data_by_id_htrans($id);

        // $ext_d = new extendDtrans();
        // $id_extend = $ext->get_all_data_by_id_htrans($id)->first()->id_extend_htrans;
        // $param["extendDtrans"] = $ext_d->get_all_data_by_id_extend_htrans($id_extend);

        $user = new customer();
        $id_user = $htrans->get_all_data_by_id($id)->first()->fk_id_user;
        $param["dataUser"] = $user->get_all_data_by_id($id_user)->first();

        $lapangan = new lapanganOlahraga();
        $id_lapangan = $htrans->get_all_data_by_id($id)->first()->fk_id_lapangan;
        $param["dataLapangan"] = $lapangan->get_all_data_by_id2($id_lapangan)->first();

        $file_lapangan = new filesLapanganOlahraga();
        $param["dataFileLapangan"] = $file_lapangan->get_all_data($id_lapangan)->first();
        
        return view("admin.transaksi.detailTransaksi")->with($param);
    }

    public function lihatJadwal() {
        $id_tempat = Session::get("dataRole")->id_tempat;

        date_default_timezone_set("Asia/Jakarta");
        $tgl = date("Y-m-d");

        $lapangan = DB::table('lapangan_olahraga')
                    ->select('nama_lapangan','id_lapangan')
                    ->join("htrans","lapangan_olahraga.id_lapangan","=","htrans.fk_id_lapangan")
                    ->where("htrans.fk_id_tempat","=",$id_tempat)
                    ->where("htrans.tanggal_sewa","=",$tgl)
                    ->where(function($query) {
                        $query->where("htrans.status_trans", "=", "Diterima")
                              ->orWhere("htrans.status_trans", "=", "Berlangsung")
                              ->orWhere("htrans.status_trans", "=", "Selesai");
                    })
                    ->distinct()
                    ->get();
        // dd($lapangan);
        $param["lapangan"] = $lapangan;

        if ($lapangan->first() != null) {
            $id_pertama = $lapangan->first()->id_lapangan;
            $param["fitur"] = $id_pertama;
        }
        else {
            $id_pertama = 0;
            $param["fitur"] = $id_pertama;
        }

        $trans = DB::table('htrans')
                ->select("htrans.id_htrans","htrans.jam_sewa","htrans.durasi_sewa","user.nama_user","htrans.status_trans")
                ->join("user","htrans.fk_id_user","=","user.id_user")
                ->where("htrans.fk_id_tempat","=",$id_tempat)
                ->where("htrans.tanggal_sewa","=",$tgl)
                ->where("htrans.fk_id_lapangan","=",$id_pertama)
                ->where(function($query) {
                    $query->where("htrans.status_trans", "=", "Diterima")
                          ->orWhere("htrans.status_trans", "=", "Berlangsung")
                          ->orWhere("htrans.status_trans", "=", "Selesai");
                })
                ->orderBy("htrans.jam_sewa","ASC")
                ->get();
        // dd($trans);
        $param["trans"] = $trans;

        return view("tempat.transaksi.jadwalSewa")->with($param);
    }

    public function fiturJadwal(Request $request) {
        $id_tempat = Session::get("dataRole")->id_tempat;

        date_default_timezone_set("Asia/Jakarta");
        $tgl = date("Y-m-d");

        $lapangan = DB::table('lapangan_olahraga')
                    ->select('nama_lapangan','id_lapangan')
                    ->join("htrans","lapangan_olahraga.id_lapangan","=","htrans.fk_id_lapangan")
                    ->where("htrans.fk_id_tempat","=",$id_tempat)
                    ->where("htrans.tanggal_sewa","=",$tgl)
                    ->where(function($query) {
                        $query->where("htrans.status_trans", "=", "Diterima")
                              ->orWhere("htrans.status_trans", "=", "Berlangsung")
                              ->orWhere("htrans.status_trans", "=", "Selesai");
                    })
                    ->distinct()
                    ->get();
        // dd($lapangan);
        $param["lapangan"] = $lapangan;

        if ($lapangan->first() != null) {
            $id_pertama = $request->lapangan;
            $param["fitur"] = $id_pertama;
        }
        else {
            $id_pertama = 0;
            $param["fitur"] = $id_pertama;
        }

        $trans = DB::table('htrans')
                ->select("htrans.id_htrans","htrans.jam_sewa","htrans.durasi_sewa","user.nama_user","htrans.status_trans")
                ->join("user","htrans.fk_id_user","=","user.id_user")
                ->where("htrans.fk_id_tempat","=",$id_tempat)
                ->where("htrans.tanggal_sewa","=",$tgl)
                ->where("htrans.fk_id_lapangan","=",$id_pertama)
                ->where(function($query) {
                    $query->where("htrans.status_trans", "=", "Diterima")
                          ->orWhere("htrans.status_trans", "=", "Berlangsung")
                          ->orWhere("htrans.status_trans", "=", "Selesai");
                })
                ->orderBy("htrans.jam_sewa","ASC")
                ->get();
        // dd($trans);
        $param["trans"] = $trans;

        return view("tempat.transaksi.jadwalSewa")->with($param);
    }
}
