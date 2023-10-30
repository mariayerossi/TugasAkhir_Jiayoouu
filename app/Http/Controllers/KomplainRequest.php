<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\filesKomplainReq;
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
                    $alat = new alatOlahraga();
                    $alat->softDelete($data);

                    $penanganan = "Hapus Alat,";
                }
                else if ($array[1] == "lapangan") {
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
                    $temp = new pihakTempat();
                    $temp->softDelete($data);

                    $penanganan .= "Hapus Tempat,";
                }
                else if ($array[1] == "pemilik") {
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
            "status" => "Ditolak"
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

        //notif pemilik/tempat
        $komplain = DB::table('komplain_request')->where("id_komplain_req","=",$request->id_komplain)->get()->first();

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
