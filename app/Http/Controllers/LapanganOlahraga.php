<?php

namespace App\Http\Controllers;

use App\Models\filesLapanganOlahraga;
use App\Models\kategori;
use App\Models\lapanganOlahraga as ModelsLapanganOlahraga;
use App\Models\slotWaktu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LapanganOlahraga extends Controller
{
    public function tambahLapangan(Request $request){
        $request->validate([
            "lapangan" => 'required|min:5|max:255',
            "kategori" => 'required',
            "tipe" => 'required',
            "lokasi" => 'required',
            "kota" => 'required',
            "foto" => 'required|max:5120',
            "deskripsi" => 'required|max:500',
            "panjang" => 'required|numeric|min:0',
            "lebar" => 'required|numeric|min:0',
            "harga" => 'required|numeric|min:0',
            "hari1" => 'required',
            "buka1" => 'required',
            "tutup1" => 'required'
        ],[
            "required" => ":attribute lapangan olahraga tidak boleh kosong!",
            "lapangan.required" => "nama :attribute olahraga tidak boleh kosong!",
            "lapangan.max" => "nama lapangan olahraga tidak valid!",
            "lapangan.min" => "nama lapangan olahraga tidak valid!",
            "min" => ":attribute lapangan olahraga tidak valid!",
            "foto.max" => "ukuran foto lapangan olahraga tidak boleh melebihi 5MB!",
            "foto.required" => "foto lapangan olahraga tidak boleh kosong!",
            "numeric" => ":attribute lapangan olahraga tidak valid!",
            "deskripsi.max" => "deskripsi lapangan olahraga maksimal 500 kata!",
            "hari1.required" => "hari operasional lapangan tidak boleh kosong!",
            "buka1.required" => "jam buka lapangan tidak boleh kosong!",
            "tutup1.required" => "jam tutup lapangan tidak boleh kosong!"
        ]);
        $harga = intval(str_replace(".", "", $request->harga));

        $luas = $request->panjang . "x" . $request->lebar;

        $data = [
            "nama"=>ucwords($request->lapangan),
            "kategori"=>$request->kategori,
            "tipe" =>$request->tipe,
            "lokasi"=>$request->lokasi,
            "kota"=>$request->kota,
            "deskripsi"=>$request->deskripsi,
            "luas"=>$luas,
            "harga"=>$harga,
            "status"=>$request->status,
            "pemilik"=>$request->pemilik
        ];
        $lapa = new ModelsLapanganOlahraga();
        $id = $lapa->insertLapangan($data);

        //insert foto alatnya
        $destinasi = "/upload";
        foreach ($request->foto as $key => $value) {
            $foto = uniqid().".".$value->getClientOriginalExtension();
            $value->move(public_path($destinasi),$foto);
            $data2 = [
                "nama"=>$foto,
                "fk"=>$id
            ];
            $file = new filesLapanganOlahraga();
            $file->insertFilesLapangan($data2);
        }

        $index = 1;
        // Asumsikan Anda memiliki model "Jadwal"
        while ($request->has("hari$index") && $request->has("buka$index") && $request->has("tutup$index")) {

            $jamBuka = $request->input("buka$index");
            $jamTutup = $request->input("tutup$index");

            // Pengecekan apakah jam buka lebih awal dari jam tutup
            if (strtotime($jamBuka) >= strtotime($jamTutup)) {
                return redirect()->back()->with('error', 'Jam buka harus lebih awal daripada jam tutup!');
            }

            $data3 = [
                "hari" => $request->input("hari$index"),
                "buka" => $jamBuka,
                "tutup" => $jamTutup,
                "lapangan" => $id
            ];
            $slot = new slotWaktu();
            $slot->insertSlot($data3);

            $index++; // Pergi ke set input berikutnya
        }

        return redirect()->back()->with("success", "Berhasil Menambah Lapangan Olahraga!");
    }

    public function editLapangan(Request $request){
        $request->validate([
            "lapangan" => 'required|min:5|max:255',
            "kategori" => 'required',
            "tipe" => 'required',
            "lokasi" => 'required',
            "kota" => 'required',
            "deskripsi" => 'required|max:500',
            "panjang" => 'required|numeric|min:0',
            "lebar" => 'required|numeric|min:0',
            "harga" => 'required|numeric|min:0',
            "hari1" => 'required',
            "buka1" => 'required',
            "tutup1" => 'required'
        ],[
            "required" => ":attribute lapangan olahraga tidak boleh kosong!",
            "lapangan.required" => "nama :attribute olahraga tidak boleh kosong!",
            "lapangan.max" => "nama lapangan olahraga tidak valid!",
            "lapangan.min" => "nama lapangan olahraga tidak valid!",
            "min" => ":attribute lapangan olahraga tidak valid!",
            "numeric" => ":attribute lapangan olahraga tidak valid!",
            "deskripsi.max" => "deskripsi lapangan olahraga maksimal 500 kata!",
            "hari1.required" => "hari operasional lapangan tidak boleh kosong!",
            "buka1.required" => "jam buka lapangan tidak boleh kosong!",
            "tutup1.required" => "jam tutup lapangan tidak boleh kosong!"
        ]);

        $harga = intval(str_replace(".", "", $request->harga));

        $luas = $request->panjang . "x" . $request->lebar;

        $data = [
            "id"=> $request->id,
            "nama"=>ucwords($request->lapangan),
            "kategori"=>$request->kategori,
            "tipe" =>$request->tipe,
            "lokasi"=>$request->lokasi,
            "kota"=>$request->kota,
            "deskripsi"=>$request->deskripsi,
            "luas"=>$luas,
            "harga"=>$harga,
            "status"=>$request->status,
            "pemilik"=>$request->pemilik
        ];
        $lapa = new ModelsLapanganOlahraga();
        $lapa->updateLapangan($data);

        // Proses unggah foto baru
        if($request->has('foto')) {
            $destinasi = "/upload";
            foreach ($request->foto as $key => $value) {
                $foto = uniqid().".".$value->getClientOriginalExtension();
                $value->move(public_path($destinasi),$foto);
                $data2 = [
                    "nama"=>$foto,
                    "fk"=>$request->id
                ];
                $file = new filesLapanganOlahraga();
                $file->insertFilesLapangan($data2);
            }
        }
    
        // Proses hapus foto
        if($request->has('delete_photos')) {
            foreach ($request->delete_photos as $photo_id) {
                $photo = filesLapanganOlahraga::find($photo_id);
                if($photo) {
                    // Hapus file dari storage
                    @unlink(public_path("/upload/" . $photo->nama));
                    // Hapus record dari database
                    $photo->delete();
                }
            }
        }

        $index = 1;
        $prevJamTutup = null;
        $prevHari = null; // variabel untuk menyimpan hari dari field sebelumnya

        while ($request->has("hari$index") && $request->has("buka$index") && $request->has("tutup$index")) {
            $hari = $request->input("hari$index");
            $jamBuka = $request->input("buka$index");
            $jamTutup = $request->input("tutup$index");

            // Pengecekan apakah jam buka lebih awal dari jam tutup
            if ($prevHari === $hari && $prevJamTutup !== null && strtotime($jamBuka) <= strtotime($prevJamTutup)) {
                return redirect()->back()->with('error', 'Jam buka dari field berikutnya harus lebih dari jam tutup dari field sebelumnya!');
            }
            
            // Tambahkan pengecekan apakah jam buka dari field saat ini sama dengan jam tutup dari field sebelumnya
            // Hanya berlaku jika hari masih sama dengan hari sebelumnya
            if ($prevHari === $hari && $prevJamTutup !== null && strtotime($jamBuka) === strtotime($prevJamTutup)) {
                return redirect()->back()->with('error', 'Jam buka dari field berikutnya tidak boleh sama dengan jam tutup dari field sebelumnya jika hari sama!');
            }

            $slot2 = slotWaktu::where('id_slot', $request->id_slot.$index)->first();
            if ($slot2) {
                $data3 = [
                    "id" => $request->id_slot.$index,
                    "hari" => $request->input("hari$index"),
                    "buka" => $jamBuka,
                    "tutup" => $jamTutup
                ];
                $slot = new slotWaktu();
                $slot->updateSlot($data3);
            }
            else {
                $data4 = [
                    "hari" => $request->input("hari$index"),
                    "buka" => $jamBuka,
                    "tutup" => $jamTutup,
                    "lapangan" => $request->id
                ];
                $slot = new slotWaktu();
                $slot->insertSlot($data4);
            }

            $prevJamTutup = $jamTutup; // Simpan nilai jam tutup dari field saat ini
            $prevHari = $hari; // Simpan nilai hari dari field saat ini untuk digunakan pada iterasi berikutnya
            $index++; // Pergi ke set input berikutnya
        }
        return redirect()->back()->with("success", "Berhasil Mengubah Detail Lapangan!");
    }

    public function searchLapangan(Request $request)
    {
        // $search = $request->input("cari");
        if (Session::get("role") == "admin") {
            $query = DB::table('lapangan_olahraga')->where('lapangan_olahraga.deleted_at',"=",null);
        }
        else {
            $query = DB::table('lapangan_olahraga')->where('lapangan_olahraga.deleted_at',"=",null)->where("lapangan_olahraga.status_lapangan","=","Aktif");
        }
    
        if ($request->filled('kategori')) {
            $query->where('lapangan_olahraga.fk_id_kategori', $request->kategori);
        }
        
        if ($request->filled('cari')) {
            // $query->where('nama_lapangan', 'like', '%' . $request->cari . '%');
            $query->where(function ($q) use ($request) {
                $q->where('lapangan_olahraga.nama_lapangan', 'like', '%' . $request->cari . '%')
                  ->orWhereExists(function ($subQuery) use ($request) {
                      $subQuery->select(DB::raw(1))
                               ->from('pihak_tempat')
                               ->whereColumn('lapangan_olahraga.pemilik_lapangan', 'pihak_tempat.id_tempat')
                               ->where('pihak_tempat.nama_tempat', 'like', '%' . $request->cari . '%');
                  });
            });
        }

        // if ($request->filled('cariPemilik')) {
        //     $query = DB::table('lapangan_olahraga')
        //     ->where('lapangan_olahraga.deleted_at',"=",null)
        //     ->join('pihak_tempat', 'lapangan_olahraga.pemilik_lapangan', '=', 'pihak_tempat.id_tempat')
        //     ->where('pihak_tempat.nama_tempat', 'like', '%' . $request->cariPemilik . '%');
        //     // dd($query->get());
        // }
        
        $hasil = $query
                ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.kota_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->get();
        // dd($hasil);
        $kat = new kategori();
        $kategori = $kat->get_all_data();

        // $files = new filesLapanganOlahraga();

        //Cek role siapa yang sedang search alat
        $role = Session::get("role");
        if ($role == "pemilik") {
            // mengirimkan data ke tampilan
            return view('pemilik.cariLapangan', ['lapangan' => $hasil, 'kategori' => $kategori]);
        }
        else if ($role == "admin") {
            return view('admin.produk.cariLapangan', ['lapangan' => $hasil, 'kategori' => $kategori]);
        }
    }

    public function searchLapanganCustomer(Request $request)
    {
        $query = DB::table('lapangan_olahraga')->where('lapangan_olahraga.deleted_at',"=",null)->where("lapangan_olahraga.status_lapangan","=","Aktif");
    
        if ($request->filled('kategori')) {
            $query->where('lapangan_olahraga.fk_id_kategori',"=", $request->kategori);
        }
        // dd($query->get());
        
        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('lapangan_olahraga.nama_lapangan', 'like', '%' . $request->cari . '%')
                  ->orWhereExists(function ($subQuery) use ($request) {
                      $subQuery->select(DB::raw(1))
                               ->from('pihak_tempat')
                               ->whereColumn('lapangan_olahraga.pemilik_lapangan', 'pihak_tempat.id_tempat')
                               ->where('pihak_tempat.nama_tempat', 'like', '%' . $request->cari . '%');
                  });
            });
        }

        // if ($request->filled('cariPemilik')) {
        //     $query = DB::table('lapangan_olahraga')
        //     ->where('lapangan_olahraga.deleted_at',"=",null)
        //     ->join('pihak_tempat', 'lapangan_olahraga.pemilik_lapangan', '=', 'pihak_tempat.id_tempat')
        //     ->where('pihak_tempat.nama_tempat', 'like', '%' . $request->cariPemilik . '%');
        //     // dd($query->get());
        // }
        
        $hasil = $query
                ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.kota_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->get();
        // dd($hasil);
        $kat = new kategori();
        $kategori = $kat->get_all_data();

        // $files = new filesLapanganOlahraga();

        return view('customer.beranda', ['lapangan' => $hasil, 'kategori' => $kategori]);
    }

    public function daftarLapangan() {
        $role = Session::get("dataRole")->id_tempat;

        $data = DB::table('lapangan_olahraga')
                ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.status_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("lapangan_olahraga.pemilik_lapangan", "=", $role)
                ->where("lapangan_olahraga.deleted_at","=",null)
                ->get();
        // dd($data);

        $param["lapangan"] = $data;
        return view("tempat.lapangan.daftarLapangan")->with($param);
    }

    public function cariLapangan() {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $data = DB::table('lapangan_olahraga')
                ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.kota_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("lapangan_olahraga.status_lapangan", "=", "Aktif")
                ->where("lapangan_olahraga.deleted_at","=",null)
                ->get();
                // dd($data);
        $param["lapangan"] = $data;
        
        if (Session::get("role") == "pemilik") {
            return view("pemilik.cariLapangan")->with($param);
        }
        else if (Session::get("role") == "customer") {
            return view("customer.beranda")->with($param);
        }
    }

    public function cariLapanganAdmin() {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $data = DB::table('lapangan_olahraga')
                ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.kota_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("lapangan_olahraga.deleted_at","=",null)
                ->get();
        $param["lapangan"] = $data;
        
        return view("admin.produk.cariLapangan")->with($param);
    }
}
