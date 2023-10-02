<?php

namespace App\Http\Controllers;

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

    public function deleteAlat($lapangan, $urutan) {
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
        dd($request->mulai);
    }
}
