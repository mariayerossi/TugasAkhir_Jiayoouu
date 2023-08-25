<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriOlahraga extends Controller
{
    public function tambahKategori(Request $request){
        $request->validate([
            "kategori" => 'required',
        ], [
            "required" => "nama :attribute tidak boleh kosong!"
        ]);

        $result = DB::insert("INSERT INTO kategori VALUES(?, ?)", [
            0,
            $request->kategori
        ]);

        if ($result) {
            return redirect()->back()->with("success", "Berhasil Menambah Kategori!");
        }
        else {
            return redirect()->back()->with("error", "Gagal Menambah Kategori!");
        }
    }

    public function hapusKategori(Request $request) {
        
    }
}
