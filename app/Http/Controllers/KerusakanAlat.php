<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\customer;
use App\Models\dtrans;
use App\Models\htrans;
use App\Models\kerusakanAlat as ModelsKerusakanAlat;
use App\Models\requestPenawaran;
use App\Models\requestPermintaan;
use App\Models\sewaSendiri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\notifikasiEmail;
use App\Models\pihakTempat;

class KerusakanAlat extends Controller
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
    
    public function ajukanKerusakan(Request $request) {
        // Mengambil semua data dari request
        // dd($request->file("file")->getClientOriginalExtension());
        $cek = false;
        // Ambil unsur dan foto
        $unsur = $request->unsur;
        $foto = $request->file("file");
        // return response()->json(['success' => false, 'message' => $foto]);
        $id_dtrans = $request->id_dtrans;
        // dd($foto);
        if ($unsur == null || $unsur == "undefined") {
            return response()->json(['success' => false, 'message' => "Unsur Kesengajaan tidak valid!"]);
        }
        else {
            if ($foto != null) {
                $destinasi = "/upload";
                $foto2 = uniqid().".".$foto->getClientOriginalExtension();
                $foto->move(public_path($destinasi),$foto2);

                $data = [
                    "id_dtrans" => $id_dtrans,
                    "unsur" => $unsur,
                    "foto" => $foto2
                ];
                $ker = new ModelsKerusakanAlat();
                $ker->insertKerusakanAlat($data);

                $dataTrans = DB::table('dtrans')
                        ->select("dtrans.fk_id_alat", "htrans.fk_id_lapangan")
                        ->leftJoin("htrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                        ->where("dtrans.id_dtrans","=",$id_dtrans)
                        ->get()
                        ->first();

                date_default_timezone_set("Asia/Jakarta");                

                //hapus dtrans yang berhubungan dengan alat ini
                $dataDtrans2 = DB::table('dtrans')
                            ->select("dtrans.id_dtrans","user.saldo_user","user.nama_user","user.email_user","user.id_user","dtrans.subtotal_alat","alat_olahraga.nama_alat","htrans.kode_trans")
                            ->leftJoin("htrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                            ->join("user","htrans.fk_id_user","=","user.id_user")
                            ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                            ->where("dtrans.fk_id_alat","=",$dataTrans->fk_id_alat)
                            ->where(function ($query) {
                                $query->where("htrans.status_trans", "=", "Menunggu")
                                    ->orWhere("htrans.status_trans", "=", "Diterima");
                            })
                            ->where("dtrans.deleted_at", "=", null)
                            ->get();

                if (!$dataDtrans2->isEmpty()) {
                    foreach ($dataDtrans2 as $key => $value) {
                        //kembalikan dana ke saldo cust
                        $saldoUser = (int)$this->decodePrice($value->saldo_user, "mysecretkey");
                        $saldoUser += $value->subtotal_alat;
                        $enkripUser = $this->encodePrice((string)$saldoUser, "mysecretkey");

                        $dataSaldoUser = [
                            "id" => $value->id_user,
                            "saldo" => $enkripUser
                        ];
                        $user = new customer();
                        $user->updateSaldo($dataSaldoUser);

                        $data3 = [
                            "id" => $value->id_dtrans,
                            "tanggal" => date("Y-m-d H:i:s")
                        ];
                        $dtrans = new dtrans();
                        $dtrans->softDelete($data3);

                        //kasih notif ke cust
                        $dataNotifUser = [
                            "subject" => "😢Pembatalan Alat Olahraga yang Dipesan😢",
                            "judul" => "Pembatalan Alat Olahraga yang Dipesan",
                            "nama_user" => $value->nama_user,
                            "url" => "https://sportiva.my.id/customer/daftarRiwayat",
                            "button" => "Lihat Detail Transaksi",
                            "isi" => "Maaf! Alat olahraga yang anda pesan:<br><br>
                                    <b>Nama Alat Olahraga: ".$value->nama_alat."</b><br>
                                    <b>dari Transaksi: ".$value->kode_trans."</b><br><br>
                                    Telah mengalami kerusakan, oleh karena itu alat olahraga tersebut akan otomatis dibatalkan dari transaksi dan dana telah kami kembalikan ke saldo anda!"
                        ];
                        $e = new notifikasiEmail();
                        $e->sendEmail($value->email_user, $dataNotifUser);
                    }
                }

                //hapus request alat (Disewakan) di tempat
                $permintaan = DB::table('request_permintaan')
                            ->where("req_id_alat","=",$dataTrans->fk_id_alat)
                            ->where("status_permintaan","=","Disewakan")
                            ->get();
                if (!$permintaan->isEmpty()) {
                    foreach ($permintaan as $key => $value) {
                        $id = $value->id_permintaan;
                    
                        $data3 = [
                            "id" => $id,
                            "status" => "Selesai"
                        ];
                        $per = new requestPermintaan();
                        $per->updateStatus($data3);

                        //memasukkan total komisi ke saldo tempat
                        $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$value->fk_id_tempat)->get()->first();
                        $saldoTempat = (int)$this->decodePrice($dataTempat->saldo_tempat, "mysecretkey");

                        $transaksi = DB::table('dtrans')
                                    ->select("dtrans.total_komisi_tempat", "extend_dtrans.total_komisi_tempat as total_ext")
                                    ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
                                    ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                                    ->where("dtrans.fk_id_alat","=",$dataTrans->fk_id_alat)
                                    ->where("htrans.fk_id_tempat","=",$value->fk_id_tempat)
                                    ->where("htrans.status_trans","=","Selesai")
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
                    }
                }
                $penawaran = DB::table('request_penawaran')
                        ->where("req_id_alat","=",$dataTrans->fk_id_alat)
                        ->where("status_penawaran","=","Disewakan")
                        ->get();
                if (!$penawaran->isEmpty()) {
                    foreach ($penawaran as $key => $value) {
                        $id = $value->id_penawaran;
                    
                        $data3 = [
                            "id" => $id,
                            "status" => "Selesai"
                        ];
                        $pen = new requestPenawaran();
                        $pen->updateStatus($data3);

                        //memasukkan total komisi ke saldo tempat
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
                                    // dd($transaksi);
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
                }
                
                $sewa = DB::table('sewa_sendiri')
                    ->where("req_id_alat","=",$dataTrans->fk_id_alat)
                    ->get();

                if (!$sewa->isEmpty()) {
                    $id = $sewa->first()->id_sewa;

                    date_default_timezone_set("Asia/Jakarta");
                    
                    $data3 = [
                        "id" => $id,
                        "delete" => date("Y-m-d H:i:s")
                    ];
                    $pen = new sewaSendiri();
                    $pen->deleteSewa($data3);
                }

                //hapus request (menunggu, diterima)
                $permintaan1 = DB::table('request_permintaan')
                            ->where("req_id_alat","=",$dataTrans->fk_id_alat)
                            ->where(function ($query) {
                                $query->where("status_permintaan", "=", "Menunggu")
                                    ->orWhere("status_permintaan", "=", "Diterima");
                            })
                            ->get();
                // dd($permintaan1);
                if (!$permintaan1->isEmpty()) {
                    foreach ($permintaan1 as $key => $value) {
                        $id = $value->id_permintaan;
                    
                        $data3 = [
                            "id" => $id,
                            "status" => "Dibatalkan"
                        ];
                        $per = new requestPermintaan();
                        $per->updateStatus($data3);
                    }
                }
                $penawaran1 = DB::table('request_penawaran')
                        ->where("req_id_alat","=",$dataTrans->fk_id_alat)
                        ->where(function ($query) {
                            $query->where("status_penawaran", "=", "Menunggu")
                                ->orWhere("status_penawaran", "=", "Diterima");
                        })
                        ->get();
                if (!$penawaran1->isEmpty()) {
                    foreach ($penawaran1 as $key => $value) {
                        $id = $value->id_penawaran;
                    
                        $data3 = [
                            "id" => $id,
                            "status" => "Dibatalkan"
                        ];
                        $pen = new requestPenawaran();
                        $pen->updateStatus($data3);
                    }
                }

                $cek = true;

                //notif ke pemilik klo alat miliknya rusak dan perlu diambil
                $dtrans = DB::table('dtrans')->where("id_dtrans","=",$id_dtrans)->get()->first();
                if ($dtrans->fk_id_pemilik != null) {
                    $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$dtrans->fk_id_pemilik)->get()->first();

                    $sengaja = "";
                    if ($unsur == "Tidak") {
                        $sengaja = "Setelah melakukan pengecekan, kami memastikan bahwa kerusakan ini terjadi karena unsur ketidaksengajaan. Oleh karena itu, tidak ada pihak yang akan dikenakan biaya ganti rugi atas kerusakan ini. Kami menghargai pengertian Anda dan mohon maaf atas ketidaknyamanan yang mungkin timbul.";
                    }
                    else {
                        $sengaja = "Setelah melakukan pengecekan, kami menemukan bukti yang menunjukkan bahwa kerusakan ini terjadi karena adanya kesengajaan. Oleh karena itu, sesuai dengan peraturan dan ketentuan yang telah disepakati, akan ada biaya ganti rugi yang dikenakan kepada pihak tempat olahraga yang bertanggung jawab.";
                    }

                    $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$dtrans->fk_id_alat)->get()->first();

                    $dataNotif = [
                        "subject" => "😢Pemberitahuan Kerusakan Alat Olahraga😢",
                        "judul" => "Pemberitahuan Kerusakan Alat Olahraga",
                        "nama_user" => $pemilik->nama_pemilik,
                        "url" => "https://sportiva.my.id/pemilik/lihatDetail/".$dataAlat->id_alat,
                        "button" => "Lihat Detail Alat",
                        "isi" => "Maaf! Alat olahraga yang anda sewakan mengalami kerusakan:<br><br>
                                <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
                                <b>Ganti Rugi Alat Olahraga: Rp ".number_format($dataAlat->ganti_rugi_alat, 0, ',', '.')."</b><br><br>
                                ".$sengaja."<br><br>
                                Alat olahraga sudah bisa diambil di tempat olahraga:<br>
                                <b>".Session::get("dataRole")->nama_tempat."</b><br>
                                Terus sewakan alat olahragamu di Sportiva!"
                    ];
                    $e = new notifikasiEmail();
                    $e->sendEmail($pemilik->email_pemilik, $dataNotif);
                }

                $data2 = [
                    "id" => $dataTrans->fk_id_alat,
                    "tanggal" => date("Y-m-d H:i:s")
                ];
                $alat = new alatOlahraga();
                $alat->softDelete($data2);
            }
            else {
                return response()->json(['success' => false, 'message' => "Foto tidak boleh kosong!"]);
            }
        }

        if ($cek) {
            return response()->json(['success' => true, 'message' => "Berhasil mengajukan kerusakan alat olahraga!"]);
        }
        else {
            return response()->json(['success' => false, 'message' => "Gagal mengajukan kerusakan alat olahraga!"]);
        }
    }

    // public function ajukanKerusakan(Request $request) {
    //     // Mengambil semua data dari request
    //     $cek = false;
    //     foreach ($request->input('id_dtrans') as $index => $id_dtrans) {
    //         // Ambil unsur dan foto
    //         $unsur = $request->input("unsur$index");
    //         $foto = $request->file("foto$index");
            
    //         if ($unsur != null) {
    //             if ($foto != null) {
    //                 $destinasi = "/upload";
    //                 $foto2 = uniqid().".".$foto->getClientOriginalExtension();
    //                 $foto->move(public_path($destinasi),$foto2);

    //                 $data = [
    //                     "id_dtrans" => $id_dtrans,
    //                     "unsur" => $unsur,
    //                     "foto" => $foto2
    //                 ];
    //                 $ker = new ModelsKerusakanAlat();
    //                 $ker->insertKerusakanAlat($data);

    //                 $dataTrans = DB::table('dtrans')
    //                         ->select("dtrans.fk_id_alat", "htrans.fk_id_lapangan")
    //                         ->leftJoin("htrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
    //                         ->where("dtrans.id_dtrans","=",$id_dtrans)
    //                         ->get()
    //                         ->first();

    //                 //hapus alat
    //                 date_default_timezone_set("Asia/Jakarta");
    //                 $data2 = [
    //                     "id" => $dataTrans->fk_id_alat,
    //                     "tanggal" => date("Y-m-d H:i:s")
    //                 ];
    //                 $alat = new alatOlahraga();
    //                 $alat->softDelete($data2);

    //                 //hapus dtrans yang berhubungan dengan alat ini
    //                 $dataDtrans2 = DB::table('dtrans')
    //                             ->select("dtrans.id_dtrans","user.saldo_user","user.nama_user","user.email_user","user.id_user","dtrans.subtotal_alat","alat_olahraga.nama_alat","htrans.kode_trans")
    //                             ->leftJoin("htrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
    //                             ->join("user","htrans.fk_id_user","=","user.id_user")
    //                             ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
    //                             ->where("dtrans.fk_id_alat","=",$dataTrans->fk_id_alat)
    //                             ->where("htrans.status_trans","=","Diterima")
    //                             ->get();

    //                 if (!$dataDtrans2->isEmpty()) {
    //                     foreach ($dataDtrans2 as $key => $value) {
    //                         //kembalikan dana ke saldo cust
    //                         $saldoUser = (int)$this->decodePrice($value->saldo_user, "mysecretkey");
    //                         $saldoUser += $value->subtotal_alat;
    //                         $enkripUser = $this->encodePrice((string)$saldoUser, "mysecretkey");

    //                         $dataSaldoUser = [
    //                             "id" => $value->id_user,
    //                             "saldo" => $enkripUser
    //                         ];
    //                         $user = new customer();
    //                         $user->updateSaldo($dataSaldoUser);

    //                         $data3 = [
    //                             "id" => $value->id_dtrans,
    //                             "tanggal" => date("Y-m-d H:i:s")
    //                         ];
    //                         $dtrans = new dtrans();
    //                         $dtrans->softDelete($data3);

    //                         //kasih notif ke cust
    //                         $dataNotifUser = [
    //                             "subject" => "😢Pembatalan Alat Olahraga yang Dipesan😢",
    //                             "judul" => "Pembatalan Alat Olahraga yang Dipesan",
    //                             "nama_user" => $value->nama_user,
    //                             "url" => "https://sportiva.my.id/customer/daftarRiwayat",
    //                             "button" => "Lihat Detail Transaksi",
    //                             "isi" => "Maaf! Alat olahraga yang anda pesan:<br><br>
    //                                     <b>Nama Alat Olahraga: ".$value->nama_alat."</b><br>
    //                                     <b>dari Transaksi: ".$value->kode_trans."</b><br><br>
    //                                     Telah mengalami kerusakan, oleh karena itu alat olahraga tersebut akan otomatis dibatalkan dari transaksi dan dana telah kami kembalikan ke saldo anda!"
    //                         ];
    //                         $e = new notifikasiEmail();
    //                         $e->sendEmail($value->email_user, $dataNotifUser);
    //                     }
    //                 }

    //                 //hapus request alat di tempat
    //                 $permintaan = DB::table('request_permintaan')
    //                             ->where("req_id_alat","=",$dataTrans->fk_id_alat)
    //                             ->where("req_lapangan","=",$dataTrans->fk_id_lapangan)
    //                             ->where("status_permintaan","=","Disewakan")
    //                             ->get();
    //                 if (!$permintaan->isEmpty()) {
    //                     $id = $permintaan->first()->id_permintaan;
                        
    //                     $data3 = [
    //                         "id" => $id,
    //                         "status" => "Selesai"
    //                     ];
    //                     $per = new requestPermintaan();
    //                     $per->updateStatus($data3);

    //                     //memasukkan total komisi ke saldo tempat
    //                     $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$permintaan->first()->fk_id_tempat)->get()->first();
    //                     $saldoTempat = (int)$this->decodePrice($dataTempat->saldo_tempat, "mysecretkey");

    //                     $transaksi = DB::table('dtrans')
    //                                 ->select("dtrans.total_komisi_tempat", "extend_dtrans.total_komisi_tempat as total_ext")
    //                                 ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
    //                                 ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
    //                                 ->where("dtrans.fk_id_alat","=",$dataTrans->fk_id_alat)
    //                                 ->where("htrans.fk_id_tempat","=",$permintaan->first()->fk_id_tempat)
    //                                 ->where("htrans.status_trans","=","Selesai")
    //                                 ->get();
    //                     $total = 0;
    //                     if (!$transaksi->isEmpty()) {
    //                         foreach ($transaksi as $key => $value2) {
    //                             if ($value2->total_ext != null) {
    //                                 $total += $value2->total_komisi_tempat + $value2->total_ext;
    //                             }
    //                             else {
    //                                 $total += $value2->total_komisi_tempat;
    //                             }
    //                         }
    //                     }

    //                     $saldoTempat += $total;
    //                     $enkrip = $this->encodePrice((string)$saldoTempat, "mysecretkey");

    //                     $temp = new pihakTempat();
    //                     $dataSaldo3 = [
    //                         "id" => $permintaan->first()->fk_id_tempat,
    //                         "saldo" => $enkrip
    //                     ];
    //                     $temp->updateSaldo($dataSaldo3);
    //                 }
    //                 else {
    //                     $penawaran = DB::table('request_penawaran')
    //                             ->where("req_id_alat","=",$dataTrans->fk_id_alat)
    //                             ->where("req_lapangan","=",$dataTrans->fk_id_lapangan)
    //                             ->where("status_penawaran","=","Disewakan")
    //                             ->get();
    //                     if (!$penawaran->isEmpty()) {
    //                         $id = $penawaran->first()->id_penawaran;
                            
    //                         $data3 = [
    //                             "id" => $id,
    //                             "status" => "Selesai"
    //                         ];
    //                         $pen = new requestPenawaran();
    //                         $pen->updateStatus($data3);

    //                         //memasukkan total komisi ke saldo tempat
    //                         $dataTempat = DB::table('pihak_tempat')->where("id_tempat","=",$penawaran->first()->fk_id_tempat)->get()->first();
    //                         $saldoTempat = (int)$this->decodePrice($dataTempat->saldo_tempat, "mysecretkey");

    //                         $transaksi = DB::table('dtrans')
    //                                     ->select("dtrans.total_komisi_tempat", "extend_dtrans.total_komisi_tempat as total_ext")
    //                                     ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
    //                                     ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
    //                                     ->where("dtrans.fk_id_alat","=",$penawaran->first()->req_id_alat)
    //                                     ->where("htrans.fk_id_tempat","=",$penawaran->first()->fk_id_tempat)
    //                                     ->where("htrans.status_trans","=","Selesai")
    //                                     ->get();
    //                                     // dd($transaksi);
    //                         $total = 0;
    //                         if (!$transaksi->isEmpty()) {
    //                             foreach ($transaksi as $key => $value2) {
    //                                 if ($value2->total_ext != null) {
    //                                     $total += $value2->total_komisi_tempat + $value2->total_ext;
    //                                 }
    //                                 else {
    //                                     $total += $value2->total_komisi_tempat;
    //                                 }
    //                             }
    //                         }

    //                         $saldoTempat += $total;
    //                         $enkrip = $this->encodePrice((string)$saldoTempat, "mysecretkey");

    //                         $dataSaldo3 = [
    //                             "id" => $penawaran->first()->fk_id_tempat,
    //                             "saldo" => $enkrip
    //                         ];
    //                         $temp = new pihakTempat();
    //                         $temp->updateSaldo($dataSaldo3);
    //                     }
    //                     else {
    //                         $sewa = DB::table('sewa_sendiri')
    //                         ->where("req_id_alat","=",$dataTrans->fk_id_alat)
    //                         ->where("req_lapangan","=",$dataTrans->fk_id_lapangan)
    //                         ->get();

    //                         $id = $sewa->first()->id_sewa;

    //                         date_default_timezone_set("Asia/Jakarta");
                            
    //                         $data3 = [
    //                             "id" => $id,
    //                             "delete" => date("Y-m-d H:i:s")
    //                         ];
    //                         $pen = new sewaSendiri();
    //                         $pen->deleteSewa($data3);
    //                     }
    //                 }

    //                 $cek = true;

    //                 //notif ke pemilik klo alat miliknya rusak dan perlu diambil
    //                 $dtrans = DB::table('dtrans')->where("id_dtrans","=",$id_dtrans)->get()->first();
    //                 if ($dtrans->fk_id_pemilik != null) {
    //                     $pemilik = DB::table('pemilik_alat')->where("id_pemilik","=",$dtrans->fk_id_pemilik)->get()->first();

    //                     $sengaja = "";
    //                     if ($unsur == "Tidak") {
    //                         $sengaja = "Setelah melakukan pengecekan, kami memastikan bahwa kerusakan ini terjadi karena unsur ketidaksengajaan. Oleh karena itu, tidak ada pihak yang akan dikenakan biaya ganti rugi atas kerusakan ini. Kami menghargai pengertian Anda dan mohon maaf atas ketidaknyamanan yang mungkin timbul.";
    //                     }
    //                     else {
    //                         $sengaja = "Setelah melakukan pengecekan, kami menemukan bukti yang menunjukkan bahwa kerusakan ini terjadi karena adanya kesengajaan. Oleh karena itu, sesuai dengan peraturan dan ketentuan yang telah disepakati, akan ada biaya ganti rugi yang dikenakan kepada pihak tempat olahraga yang bertanggung jawab.";
    //                     }

    //                     $dataAlat = DB::table('alat_olahraga')->where("id_alat","=",$dtrans->fk_id_alat)->get()->first();

    //                     $dataNotif = [
    //                         "subject" => "😢Pemberitahuan Kerusakan Alat Olahraga😢",
    //                         "judul" => "Pemberitahuan Kerusakan Alat Olahraga",
    //                         "nama_user" => $pemilik->nama_pemilik,
    //                         "url" => "https://sportiva.my.id/pemilik/lihatDetail/".$dataAlat->id_alat,
    //                         "button" => "Lihat Detail Alat",
    //                         "isi" => "Maaf! Alat olahraga yang anda sewakan mengalami kerusakan:<br><br>
    //                                 <b>Nama Alat Olahraga: ".$dataAlat->nama_alat."</b><br>
    //                                 <b>Ganti Rugi Alat Olahraga: Rp ".number_format($dataAlat->ganti_rugi_alat, 0, ',', '.')."</b><br><br>
    //                                 ".$sengaja."<br><br>
    //                                 Alat olahraga sudah bisa diambil di tempat olahraga:<br>
    //                                 <b>".Session::get("dataRole")->nama_tempat."</b><br>
    //                                 Terus sewakan alat olahragamu di Sportiva!"
    //                     ];
    //                     $e = new notifikasiEmail();
    //                     $e->sendEmail($pemilik->email_pemilik, $dataNotif);
    //                 }
    //             }
    //             else {
    //                 return redirect()->back()->withInput()->with("error", "Foto tidak boleh kosong!");
    //             }
    //         }
    //     }

    //     if ($cek) {
    //         return redirect()->back()->with("success", "Berhasil mengajukan kerusakan alat olahraga!");
    //     }
    //     else {
    //         return redirect()->back()->withInput()->with("error", "Gagal mengajukan kerusakan alat olahraga!");
    //     }
    // }

    public function daftarKerusakan() {
        if (Session::get("role") == "tempat") {
            $data = DB::table('kerusakan_alat')
                    ->select("files_alat.nama_file_alat","alat_olahraga.nama_alat","alat_olahraga.ganti_rugi_alat","kerusakan_alat.kesengajaan","pemilik_alat.nama_pemilik","htrans.kode_trans")
                    ->join("dtrans","kerusakan_alat.fk_id_dtrans","=","dtrans.id_dtrans")
                    ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
                    ->join("pemilik_alat","dtrans.fk_id_pemilik","=","pemilik_alat.id_pemilik")
                    ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                    ->joinSub(function($query) {
                        $query->select("fk_id_alat", "nama_file_alat")
                            ->from('files_alat')
                            ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                    }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                    ->where("htrans.fk_id_tempat","=",Session::get("dataRole")->id_tempat)
                    ->orderBy("kerusakan_alat.id_kerusakan","DESC")
                    ->get();
            // dd($data);
            $param["rusak"] = $data;
            return view("tempat.daftarKerusakan")->with($param);
        }
        else if (Session::get("role") == "admin") {
            $data = DB::table('kerusakan_alat')
                    ->select("files_alat.nama_file_alat","alat_olahraga.nama_alat","alat_olahraga.ganti_rugi_alat","kerusakan_alat.kesengajaan","pemilik_alat.nama_pemilik","htrans.kode_trans", "htrans.id_htrans")
                    ->join("dtrans","kerusakan_alat.fk_id_dtrans","=","dtrans.id_dtrans")
                    ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
                    ->join("pemilik_alat","dtrans.fk_id_pemilik","=","pemilik_alat.id_pemilik")
                    ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                    ->joinSub(function($query) {
                        $query->select("fk_id_alat", "nama_file_alat")
                            ->from('files_alat')
                            ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                    }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                    ->orderBy("kerusakan_alat.id_kerusakan","DESC")
                    ->get();
            // dd($data);
            $param["rusak"] = $data;
            return view("admin.produk.daftarKerusakan")->with($param);
        }
    }

    public function detailTrans(Request $request) {
        $htrans = new htrans();
        $param["htrans"] = $htrans->get_all_data_by_tempat(Session::get("dataRole")->id_tempat);
        $dtrans = new dtrans();
        $param["dtrans"] = $dtrans->get_all_data_by_id_htrans($request->id_htrans);
        return view("tempat.transaksi.detailKerusakan2")->with($param);
    }

    public function detailKerusakan() {
        return view("tempat.transaksi.detailKerusakan2");
    }
}
