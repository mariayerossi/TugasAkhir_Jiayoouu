<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\kerusakanAlat as ModelsKerusakanAlat;
use App\Models\requestPenawaran;
use App\Models\requestPermintaan;
use App\Models\sewaSendiri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class KerusakanAlat extends Controller
{
    public function ajukanKerusakan(Request $request) {
        // Mengambil semua data dari request
        $cek = false;
        foreach ($request->input('id_dtrans') as $index => $id_dtrans) {
            // Ambil unsur dan foto
            $unsur = $request->input("unsur$index");
            $foto = $request->file("foto$index");
            
            if ($unsur != null) {
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

                    //ubah status alat menjadi non aktif
                    $data2 = [
                        "id" => $dataTrans->fk_id_alat,
                        "status" => "Non Aktif"
                    ];
                    $alat = new alatOlahraga();
                    $alat->updateStatus($data2);

                    //hapus request alat di tempat
                    $permintaan = DB::table('request_permintaan')
                                ->where("req_id_alat","=",$dataTrans->fk_id_alat)
                                ->where("req_lapangan","=",$dataTrans->fk_id_lapangan)
                                ->get();
                    if ($permintaan != null) {
                        $id = $permintaan->first()->id_permintaan;
                        
                        $data3 = [
                            "id" => $id,
                            "status" => "Selesai"
                        ];
                        $per = new requestPermintaan();
                        $per->updateStatus($data3);
                    }
                    else {
                        $penawaran = DB::table('request_penawaran')
                                ->where("req_id_alat","=",$dataTrans->fk_id_alat)
                                ->where("req_lapangan","=",$dataTrans->fk_id_lapangan)
                                ->get();
                        if ($penawaran != null) {
                            $id = $penawaran->first()->id_penawaran;
                            
                            $data3 = [
                                "id" => $id,
                                "status" => "Selesai"
                            ];
                            $pen = new requestPenawaran();
                            $pen->updateStatus($data3);
                        }
                        else {
                            $sewa = DB::table('sewa_sendiri')
                            ->where("req_id_alat","=",$dataTrans->fk_id_alat)
                            ->where("req_lapangan","=",$dataTrans->fk_id_lapangan)
                            ->get();

                            $id = $sewa->first()->id_sewa;

                            date_default_timezone_set("Asia/Jakarta");
                            
                            $data3 = [
                                "id" => $id,
                                "delete" => date("Y-m-d H:i:s")
                            ];
                            $pen = new sewaSendiri();
                            $pen->deleteSewa($data3);
                        }
                    }

                    $cek = true;
                }
                else {
                    return redirect()->back()->with("error", "Foto tidak boleh kosong!");
                }
            }
        }

        if ($cek) {
            return redirect()->back()->with("success", "Berhasil mengajukan kerusakan alat olahraga!");
        }
        else {
            return redirect()->back()->with("error", "Gagal mengajukan kerusakan alat olahraga!");
        }
    }

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
}
