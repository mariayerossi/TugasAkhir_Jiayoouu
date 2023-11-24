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

    public function edit(Request $request) {
        $data = DB::table('kategori')->where("id_kategori","=",$request->id)->get()->first()->nama_kategori;

        $param["edit"] = $data;
        $param["id"] = $request->id;
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        return view("admin.masterKategori")->with($param);
    }

    public function editKategori(Request $request) {
        $data = [
            "id" => $request->id,
            "nama" => ucwords($request->kategori)
        ];
        $kat = new kategori();
        $kat->updateKategori($data);

        return redirect("/admin/masterKategori");
    }
}
