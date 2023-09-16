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
            "foto[]" => 'required|max:5120',
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
            "foto[].max" => "ukuran foto alat olahraga tidak boleh melebihi 5MB!",
            "foto[].required" => "foto alat olahraga tidak boleh kosong!",
            "numeric" => ":attribute alat olahraga tidak valid!",
            "ganti.numeric" => "uang :attribute rugi tidak valid!",
            "ganti.required" => "uang :attribute tidak boleh kosong!",
            "integer" => ":attribute alat olahraga tidak valid!",
            "max" => "deskripsi alat olahraga maksimal 500 kata!"
        ]);
        
        $komisi = intval(str_replace(".", "", $request->komisi));
        $ganti = intval(str_replace(".", "", $request->ganti));

        $ukuran = $request->panjang . "x" . $request->lebar . "x" . $request->tinggi;

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
            "pemilik"=>$request->pemilik,
            "role" => $request->role
        ];
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
            "pemilik" => $request->pemilik,
            "role" => $request->role
        ];
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
            $query = DB::table('alat_olahraga')->where('deleted_at',"=",null)->where("role_pemilik_alat","=","Pemilik")->where("status_alat","=","Aktif");
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
}
