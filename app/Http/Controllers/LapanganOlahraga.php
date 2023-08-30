<?php

namespace App\Http\Controllers;

use App\Models\filesLapanganOlahraga;
use App\Models\lapanganOlahraga as ModelsLapanganOlahraga;
use Illuminate\Http\Request;

class LapanganOlahraga extends Controller
{
    public function tambahLapangan(Request $request){
        $request->validate([
            "lapangan" => 'required|min:5|max:255',
            "kategori" => 'required',
            "tipe" => 'required',
            "lokasi" => 'required',
            "foto" => 'required|max:5120',
            "deskripsi" => 'required|max:500',
            "panjang" => 'required|numeric|min:0',
            "lebar" => 'required|numeric|min:0',
            "harga" => 'required|numeric|min:0'
        ],[
            "required" => ":attribute lapangan olahraga tidak boleh kosong!",
            "lapangan.required" => "nama :attribute olahraga tidak boleh kosong!",
            "lapangan.max" => "nama lapangan olahraga tidak valid!",
            "lapangan.min" => "nama lapangan olahraga tidak valid!",
            "min" => ":attribute lapangan olahraga tidak valid!",
            "foto.max" => "ukuran foto lapangan olahraga tidak boleh melebihi 5MB!",
            "numeric" => ":attribute lapangan olahraga tidak valid!",
            "deskripsi.max" => "deskripsi lapangan olahraga maksimal 500 kata!"
        ]);
        $harga = intval(str_replace(".", "", $request->harga));

        $luas = $request->panjang . "x" . $request->lebar;

        $data = [
            "nama"=>ucwords($request->lapangan),
            "kategori"=>$request->kategori,
            "tipe" =>$request->tipe,
            "lokasi"=>$request->lokasi,
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

        return redirect()->back()->with("success", "Berhasil Menambah Alat Olahraga!");
    }
}
