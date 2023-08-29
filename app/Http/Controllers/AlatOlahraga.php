<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga as ModelsAlatOlahraga;
use App\Models\filesAlatOlahraga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlatOlahraga extends Controller
{
    public function tambahAlat(Request $request){
        dd($request->foto[]);
        $request->validate([
            "alat" => 'required|max:255',
            "kategori" => 'required',
            "foto" => 'required|max:5120',
            "deskripsi" => 'required|max:500',
            "berat" => 'required|numeric|min:0',
            "panjang" => 'required|numeric|min:0',
            "lebar" => 'required|numeric|min:0',
            "tinggi" => 'required|numeric|min:0',
            "stok" => 'required|integer|min:0',
            "komisi" => 'required|numeric|min:0',
            "ganti" => 'required|numeric|min:0'
        ],[
            "required" => ":attribute alat olahraga tidak boleh kosong!",
            "alat.required" => "nama :attribute olahraga tidak boleh kosong!",
            "alat.max" => "nama alat olahraga tidak valid!",
            "min" => ":attribute alat olahraga tidak valid!",
            "ganti.min" =>"uang :attribute rugi alat olahraga tidak valid!",
            "foto.max" => "ukuran foto alat olahraga tidak boleh melebihi 5MB!",
            "numeric" => ":attribute alat olahraga tidak valid!",
            "ganti.numeric" => "uang :attribute rugi tidak valid!",
            "ganti.required" => "uang :attribute tidak boleh kosong!",
            "integer" => ":attribute alat olahraga tidak valid!",
            "max" => "deskripsi alat olahraga maksimal 500 kata!"
        ]);

        $komisi = intval(str_replace(".", "", $request->komisi));
        $ganti = intval(str_replace(".", "", $request->komisi));

        $ukuran = $request->panjang . "x" . $request->lebar . "x" . $request->tinggi;

        $data = [
            "nama"=>ucwords($request->alat),
            "kategori"=>$request->kategori,
            "deskripsi"=>$request->deskripsi,
            "berat"=>$request->berat,
            "ukuran"=>$ukuran,
            "stok"=>$request->stok,
            "komisi"=>$komisi,
            "ganti"=>$ganti,
            "status"=>$request->status,
            "pemilik"=>$request->pemilik
        ];
        $alat = new ModelsAlatOlahraga();
        $id = $alat->insertAlat($data);
        
        //dapetin fk_id_alat
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
        // ... kode validasi dan lain-lain ...
    
        // Proses unggah foto baru
        if($request->has('foto')) {
            $destinasi = "/upload";
            foreach ($request->foto as $key => $value) {
                $foto = uniqid().".".$value->getClientOriginalExtension();
                $value->move(public_path($destinasi),$foto);
                $data2 = [
                    "nama"=>$foto,
                    "fk"=>$request->id // Diasumsikan ini adalah ID dari alat olahraga
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
    
        // ... kode update informasi lainnya ...
    
        return redirect()->back()->with("success", "Berhasil Mengedit Alat Olahraga!");
    }
}
