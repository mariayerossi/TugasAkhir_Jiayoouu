<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga as ModelsAlatOlahraga;
use App\Models\filesAlatOlahraga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlatOlahraga extends Controller
{
    public function tambahAlat(Request $request){
        $request->validate([
            "alat" => 'required|max:255',
            "kategori" => 'required',
            "foto" => 'required|max:5120',
            "deskripsi" => 'required',
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
            "integer" => ":attribute alat olahraga tidak valid!"
        ]);

        $ukuran = $request->panjang + "x" + $request->lebar + "x" + $request->tinggi;

        $data = [
            "nama"=>$request->nama,
            "kategori"=>$request->kategori,
            "deskripsi"=>$request->deskripsi,
            "berat"=>$request->berat,
            "ukuran"=>$ukuran,
            "stok"=>$request->stok,
            "komisi"=>$request->komisi,
            "ganti"=>$request->ganti,
            "status"=>$request->status,
            "pemilik"=>$request->pemilik
        ];
        $alat = new ModelsAlatOlahraga();
        $alat->insertAlat($data);
        
        // //insert foto alatnya
        // $destinasi = "/upload";
        // $file = $request->file("ktp");
        // $ktp = uniqid().".".$file->getClientOriginalExtension();
        // foreach ($request->foto as $key => $value) {
        //     $data2 = [
        //         "nama"=>$value,
        //     ];
        //     $file = new filesAlatOlahraga();
        //     $file->insertFilesAlat($data2);
        // }
    }
}
