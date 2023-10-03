<?php

namespace App\Http\Controllers;

use App\Models\kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Transaksi extends Controller
{
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
                ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","user.nama_user","htrans.kode_trans","htrans.total_trans","htrans.tanggal_trans")
                ->join("user", "htrans.fk_id_user", "=", "user.id_user")
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
                    ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.kota_lapangan")
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
                ->select("htrans.id_htrans","files_lapangan.nama_file_lapangan", "lapangan_olahraga.nama_lapangan","htrans.kode_trans","htrans.total_trans","htrans.tanggal_sewa", "htrans.jam_sewa", "htrans.durasi_sewa")
                ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("htrans.fk_id_user", "=", Session::get("dataRole")->id_user)
                ->get();

        $param["trans"] = $trans;
        return view("customer.riwayat")->with($param);
    }
}
