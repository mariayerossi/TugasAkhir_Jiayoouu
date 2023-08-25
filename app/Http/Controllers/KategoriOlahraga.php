<?php

namespace App\Http\Controllers;

use App\Models\kategori;
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

        $data = [
            "nama"=>ucwords($request->kategori)
        ];
        $kat = new kategori();
        $kat->insertKategori($data);

        return redirect()->back()->with("success", "Berhasil Menambah Kategori!");
    }

    public function hapusKategori(Request $request) {
        $data = [
            "id" => $request->id
        ];
        $kat = new kategori();
        $kat->deleteKategori($data);

        return redirect()->back()->with("success", "Berhasil Menghapus Kategori!");
    }
}
