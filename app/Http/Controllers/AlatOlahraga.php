<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlatOlahraga extends Controller
{
    public function tambahAlat(Request $request){
        $request->validate([
            "alat" => 'required',
            "kategori" => 'required',
            "foto" => 'required|max:5120',
            "deskripsi" => 'required',
            "berat" => 'required|min:0',
            "panjang" => 'required|min:0',
            "lebar" => 'required|min:0',
            "tinggi" => 'required|min:0',
            "stok" => 'required|min:0',
            "komisi" => 'required|min:0',
            "ganti" => 'required|min:0'
        ],[
            "required" => ":attribute alat olahraga tidak boleh kosong!",
            "alat.required" => "nama :attribute olahraga tidak boleh kosong!",
            "min" => ":attribute alat olahraga tidak valid!",
            "ganti.min" =>"uang :attribute rugi alat olahraga tidak valid!"
        ]);

        
    }
}
