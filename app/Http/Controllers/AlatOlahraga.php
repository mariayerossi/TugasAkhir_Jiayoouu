<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga as ModelsAlatOlahraga;
use App\Models\filesAlatOlahraga;
use App\Models\kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AlatOlahraga extends Controller
{
    public function tambahAlat(Request $request){
        $request->validate([
            "alat" => 'required|min:5|max:255',
            "kategori" => 'required',
            "kota" => 'required',
            "foto" => 'required|max:5120',
            "deskripsi" => 'required|max:500',
            "berat" => 'required|numeric|min:0',
            "panjang" => 'required|numeric|min:0',
            "lebar" => 'required|numeric|min:0',
            "tinggi" => 'required|numeric|min:0',
            "komisi" => 'required|numeric|min:0',
            "ganti" => 'required|numeric|min:0'
        ],[
            "required" => ":attribute alat olahraga tidak boleh kosong!",
            "alat.required" => "nama :attribute olahraga tidak boleh kosong!",
            "alat.max" => "nama alat olahraga tidak valid!",
            "alat.min" => "nama alat olahraga tidak valid!",
            "min" => ":attribute alat olahraga tidak valid!",
            "ganti.min" =>"uang :attribute rugi alat olahraga tidak valid!",
            "foto.max" => "ukuran foto alat olahraga tidak boleh melebihi 5MB!",
            "foto.required" => "foto alat olahraga tidak boleh kosong!",
            "numeric" => ":attribute alat olahraga tidak valid!",
            "ganti.numeric" => "uang :attribute rugi tidak valid!",
            "ganti.required" => "uang :attribute tidak boleh kosong!",
            "integer" => ":attribute alat olahraga tidak valid!",
            "max" => "deskripsi alat olahraga maksimal 500 kata!"
        ]);
        
        $komisi = intval(str_replace(".", "", $request->komisi));
        $ganti = intval(str_replace(".", "", $request->ganti));

        $ukuran = $request->panjang . "x" . $request->lebar . "x" . $request->tinggi;

        if (Session::get("role") == "pemilik") {
            $pemilik = Session::get("dataRole")->id_pemilik;

            $data = [
                "nama"=>ucwords($request->alat),
                "kategori"=>$request->kategori,
                "kota"=>$request->kota,
                "deskripsi"=>$request->deskripsi,
                "berat"=>$request->berat,
                "ukuran"=>$ukuran,
                "komisi"=>$komisi,
                "ganti"=>$ganti,
                "status"=>$request->status,
                "pemilik"=>$pemilik,
                "tempat" => null
            ];
        }
        else {
            $pemilik = Session::get("dataRole")->id_tempat;

            $data = [
                "nama"=>ucwords($request->alat),
                "kategori"=>$request->kategori,
                "kota"=>$request->kota,
                "deskripsi"=>$request->deskripsi,
                "berat"=>$request->berat,
                "ukuran"=>$ukuran,
                "komisi"=>$komisi,
                "ganti"=>$ganti,
                "status"=>$request->status,
                "pemilik"=>null,
                "tempat" => $pemilik
            ];
        }

        $alat = new ModelsAlatOlahraga();
        $id = $alat->insertAlat($data);
        
        //insert foto alatnya
        $destinasi = "/upload";
        foreach ($request->foto as $key => $value) {
            $foto = uniqid().".".$value->getClientOriginalExtension();
            $value->move(public_path($destinasi),$foto);
            $data2 = [
                "nama"=>$foto,
                "fk"=>$id
            ];
            $file = new filesAlatOlahraga();
            $file->insertFilesAlat($data2);
        }

        return redirect()->back()->with("success", "Berhasil Menambah Alat Olahraga!");
    }

    public function editAlat (Request $request) {
        $request->validate([
            "alat" => 'required|max:255',
            "kategori" => 'required',
            "kota"=>'required',
            "deskripsi" => 'required|max:500',
            "berat" => 'required|numeric|min:0',
            "panjang" => 'required|numeric|min:0',
            "lebar" => 'required|numeric|min:0',
            "tinggi" => 'required|numeric|min:0',
            "komisi" => 'required|numeric|min:0',
            "ganti" => 'required|numeric|min:0'
        ],[
            "required" => ":attribute alat olahraga tidak boleh kosong!",
            "alat.required" => "nama :attribute olahraga tidak boleh kosong!",
            "alat.max" => "nama alat olahraga tidak valid!",
            "min" => ":attribute alat olahraga tidak valid!",
            "ganti.min" =>"uang :attribute rugi alat olahraga tidak valid!",
            "numeric" => ":attribute alat olahraga tidak valid!",
            "ganti.numeric" => "uang :attribute rugi tidak valid!",
            "ganti.required" => "uang :attribute tidak boleh kosong!",
            "integer" => ":attribute alat olahraga tidak valid!",
            "max" => "deskripsi alat olahraga maksimal 500 kata!"
        ]);
        
        $komisi = intval(str_replace(".", "", $request->komisi));
        $ganti = intval(str_replace(".", "", $request->ganti));
        
        $ukuran = $request->panjang . "x" . $request->lebar . "x" . $request->tinggi;

        //update detail alat
        if (Session::get("role") == "pemilik") {
            $pemilik = Session::get("dataRole")->id_pemilik;

            $data = [
                "id" => $request->id,
                "nama" => $request->alat,
                "kategori" => $request->kategori,
                "kota"=>$request->kota,
                "deskripsi" => $request->deskripsi,
                "berat" => $request->berat,
                "ukuran" => $ukuran,
                "komisi" => $komisi,
                "ganti" => $ganti,
                "status" => $request->status,
                "pemilik" => $pemilik,
                "tempat" => null
            ];
        }
        else {
            $pemilik = Session::get("dataRole")->id_tempat;

            $data = [
                "id" => $request->id,
                "nama" => $request->alat,
                "kategori" => $request->kategori,
                "kota"=>$request->kota,
                "deskripsi" => $request->deskripsi,
                "berat" => $request->berat,
                "ukuran" => $ukuran,
                "komisi" => $komisi,
                "ganti" => $ganti,
                "status" => $request->status,
                "pemilik"=>null,
                "tempat" => $pemilik
            ];
        }
        $alat = new ModelsAlatOlahraga();
        $alat->updateAlat($data);
    
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
                $file = new filesAlatOlahraga();
                $file->insertFilesAlat($data2);
            }
        }
    
        // Proses hapus foto
        if($request->has('delete_photos')) {
            foreach ($request->delete_photos as $photo_id) {
                $photo = filesAlatOlahraga::find($photo_id);
                if($photo) {
                    // Hapus file dari storage
                    @unlink(public_path("/upload/" . $photo->nama));
                    // Hapus record dari database
                    $photo->delete();
                }
            }
        }
    
        return redirect()->back()->with("success", "Berhasil Mengubah Detail Alat Olahraga!");
    }

    public function searchAlat(Request $request)
    {
        if (Session::get("role") == "admin") {
            $query = DB::table('alat_olahraga')->where('deleted_at',"=",null);
        }
        else {
            $query = DB::table('alat_olahraga')->where('deleted_at',"=",null)->where("fk_id_pemilik","!=",null)->where("status_alat","=","Aktif");
        }
        // $search = $request->input("cari");
    
        if ($request->filled('kategori')) {
            $query->where('kategori_alat', $request->kategori);
        }
        
        if ($request->filled('cari')) {
            $query->where('nama_alat', 'like', '%' . $request->cari . '%');
        }
        
        $hasil = $query->get();
        $kat = new kategori();
        $kategori = $kat->get_all_data();

        $files = new filesAlatOlahraga();

        //Cek role siapa yang sedang search alat
        $role = Session::get("role");
        if ($role == "tempat") {
            // mengirimkan data ke tampilan
            return view('tempat.cariAlat', ['alat' => $hasil, 'kategori' => $kategori, 'files' => $files]);
        }
        else if ($role == "admin") {
            return view('admin.produk.cariAlat', ['alat' => $hasil, 'kategori' => $kategori, 'files' => $files]);
        }
    }

    public function editHargaKomisi(Request $request){
        $request->validate([
            "harga_komisi" => "required"
        ],[
            "required" => "Harga komisi tidak boleh kosong!"
        ]);

        $data = [
            "id" => $request->id_alat,
            "komisi" => $request->harga_komisi
        ];
        $alat = new ModelsAlatOlahraga();
        $alat->updateKomisi($data);
        
        return redirect()->back()->with("success", "Berhasil Mengubah Komisi Alat Olahraga!");
    }

    public function daftarAlatTempat() {
        $role = Session::get("dataRole")->id_tempat;

        $data = DB::table('alat_olahraga')
                ->select("alat_olahraga.id_alat","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "alat_olahraga.komisi_alat","alat_olahraga.status_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("alat_olahraga.fk_id_tempat", "=", $role)
                ->get();
        // dd($data);

        $param["alat"] = $data;
        return view("tempat.alat.daftarAlat")->with($param);
    }

    public function daftarAlatPemilik() {
        $role = Session::get("dataRole")->id_pemilik;

        $data = DB::table('alat_olahraga')
                ->select("alat_olahraga.id_alat","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "alat_olahraga.komisi_alat","alat_olahraga.status_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("alat_olahraga.fk_id_pemilik", "=", $role)
                ->get();
        // dd($data);

        $param["alat"] = $data;
        return view("pemilik.alat.daftarAlat")->with($param);
    }

    public function daftarDisewakan() {
        $role = Session::get("dataRole")->id_pemilik;

        $data = DB::table('dtrans')
                ->select("htrans.tanggal_sewa","files_alat.nama_file_alat","alat_olahraga.nama_alat","dtrans.harga_sewa_alat","htrans.durasi_sewa","dtrans.subtotal_alat")
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->rightJoin("htrans", "dtrans.fk_id_htrans","=","htrans.id_htrans")
                ->where("dtrans.fk_id_pemilik","=",$role)
                ->get();
        // dd($data);

        $param["disewakan"] = $data;
        return view("pemilik.alat.daftarDisewakan")->with($param);
    }

    public function cariAlatAdmin() {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $data = DB::table('alat_olahraga')
                ->select("alat_olahraga.id_alat","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "alat_olahraga.komisi_alat","alat_olahraga.kota_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->get();
        // dd($data);

        $param["alat"] = $data;
        return view("admin.produk.cariAlat")->with($param);
    }

    public function cariAlat() {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $data = DB::table('alat_olahraga')
                ->select("alat_olahraga.id_alat","alat_olahraga.nama_alat", "files_alat.nama_file_alat", "alat_olahraga.komisi_alat","alat_olahraga.kota_alat")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->where("alat_olahraga.fk_id_pemilik","!=",null)
                ->where("alat_olahraga.status_alat", "=", "Aktif")
                ->get();
        // dd($data);

        $param["alat"] = $data;
        return view("tempat.cariAlat")->with($param);
    }
}
