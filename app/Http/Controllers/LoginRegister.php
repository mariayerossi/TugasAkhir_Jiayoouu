<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginRegister extends Controller
{
    //Register User
    public function registerUser(Request $request){
        $request->validate([
            "nama" => 'required|min:5|alpha',
            "email" => 'required|email',
            "telepon" => 'required|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3,4}[-\s\.]?[0-9]{4,6}$/',
            "password" => 'required|min:8',
            "konfirmasi" => 'required|min:8'
        ], [
            "nama.required" => ":attribute lengkap tidak boleh kosong!",
            "required" => ":attribute tidak boleh kosong!",
            "nama.min" => ":attribute lengkap tidak valid!",
            "alpha" => ":attribute lengkap tidak valid!",
            "email.required" => "alamat :attribute tidak boleh kosong!",
            "email" => "alamat :attribute tidak valid!",
            "telepon.required" => "nomer :attribute tidak boleh kosong!",
            "regex" => "nomer :attribute tidak valid!",
            "password.min" => ":attribute harus memiliki setidaknya 8 karakter!",
            "konfirmasi.required" => ":attribute password tidak boleh kosong!",
            "konfirmasi.min" => ":attribute password harus memiliki setidaknya 8 karakter!"
        ]);

        if ($request->password == $request->konfirmasi) {
            $result = DB::insert("INSERT INTO user VALUES(?, ?, ?, ?, ?)", [
                0,
                $request->nama,
                $request->email,
                $request->telepon,
                $request->password
            ]);
    
            if ($result) {
                return redirect()->back()->with("success", "Berhasil Register!");
            }
            else {
                return redirect()->back()->with("error", "Gagal Register!");
            }
        }
        else {
            return redirect()->back()->with("error", "Konfirmasi password salah!");
        }
    }

    // Register Pemilik

    //Register Tempat
}
