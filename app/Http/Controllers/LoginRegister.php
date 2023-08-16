<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LoginRegister extends Controller
{
    // Enkripsi saldo
    private function encodePrice($price, $key) {
        $encodedPrice = '';
        $priceLength = strlen($price);
        $keyLength = strlen($key);
    
        for ($i = 0; $i < $priceLength; $i++) {
            $encodedPrice .= $price[$i] ^ $key[$i % $keyLength];
        }
    
        return base64_encode($encodedPrice);
    }

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
            //enkripsi saldo user
            $saldo = "0"; 
            $secretKey = "mysecretkey"; // Kunci rahasia untuk melakukan enkripsi dan dekripsi
            $enkripsiSaldo = $this->encodePrice($saldo, $secretKey);

            //Enkripsi password user
            $password = $request->password;  // Ganti dengan password pengguna
            $hash_password = password_hash($password, PASSWORD_BCRYPT);

            $result = DB::insert("INSERT INTO user VALUES(?, ?, ?, ?, ?, ?)", [
                0,
                $request->nama,
                $request->email,
                $request->telepon,
                $hash_password,
                $enkripsiSaldo
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
    public function registerPemilik(Request $request){
        $request->validate([
            "nama" => 'required|min:5|alpha',
            "email" => 'required|email',
            "telepon" => 'required|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3,4}[-\s\.]?[0-9]{4,6}$/',
            "ktp" => 'required|max:5120',
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
            "ktp.required" => "foto KTP tidak boleh kosong!",
            "ktp.max" => "ukuran gambar KTP tidak boleh lebih dari 5 MB!",
            "password.min" => ":attribute harus memiliki setidaknya 8 karakter!",
            "konfirmasi.required" => ":attribute password tidak boleh kosong!",
            "konfirmasi.min" => ":attribute password harus memiliki setidaknya 8 karakter!"
        ]);

        if ($request->password == $request->konfirmasi) {
            //enkripsi saldo pemilik
            $saldo = "0"; 
            $secretKey = "mysecretkey"; // Kunci rahasia untuk melakukan enkripsi dan dekripsi
            $enkripsiSaldo = $this->encodePrice($saldo, $secretKey);

            //Enkripsi password pemilik
            $password = $request->password;  // Ganti dengan password pengguna
            $hash_password = password_hash($password, PASSWORD_BCRYPT);

            //Upload file
            $destinasi = "/upload";
            $file = $request->file("ktp");
            $ktp = uniqid().".".$file->getClientOriginalExtension();

            $result = DB::insert("INSERT INTO pemilik_alat VALUES(?, ?, ?, ?, ?, ?, ?)", [
                0,
                $request->nama,
                $request->email,
                $request->telepon,
                $ktp,
                $hash_password,
                $enkripsiSaldo
            ]);
            $file->move(public_path($destinasi),$ktp);
    
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

    //Register Tempat
    public function registerTempat(Request $request){
        $request->validate([
            "nama" => 'required|min:5',
            "pemilik" => 'required|min:5|alpha',
            "email" => 'required|email',
            "telepon" => 'required|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3,4}[-\s\.]?[0-9]{4,6}$/',
            "ktp" => 'required|max:5120',
            "npwp" => 'required|max:5120',
            "password" => 'required|min:8',
            "konfirmasi" => 'required|min:8'
        ], [
            "required" => ":attribute tidak boleh kosong!",
            "nama.required" => ":attribute tempat olahraga tidak boleh kosong!",
            "nama.min" => ":attribute tempat olahraga tidak valid!",
            "pemilik.required" => "nama lengkap :attribute tempat olahraga tidak boleh kosong!",
            "pemilik.min" => "nama lengkap :attribute tempat olahraga tidak valid",
            "alpha" => "nama lengkap :attribute tempat olahraga tidak valid!",
            "email.required" => "alamat :attribute tidak boleh kosong!",
            "email" => "alamat :attribute tidak valid!",
            "telepon.required" => "nomer :attribute tidak boleh kosong!",
            "regex" => "nomer :attribute tidak valid!",
            "ktp.required" => "foto KTP tidak boleh kosong!",
            "ktp.max" => "ukuran gambar KTP tidak boleh lebih dari 5 MB!",
            "npwp.required" => "foto NPWP tidak boleh kosong!",
            "npwp.max" => "ukuran gambar NPWP tidak boleh lebih dari 5 MB!",
            "password.min" => ":attribute harus memiliki setidaknya 8 karakter!",
            "konfirmasi.required" => ":attribute password tidak boleh kosong!",
            "konfirmasi.min" => ":attribute password harus memiliki setidaknya 8 karakter!"
        ]);

        if ($request->password == $request->konfirmasi) {
            //enkripsi saldo tempat
            $saldo = "0"; 
            $secretKey = "mysecretkey"; // Kunci rahasia untuk melakukan enkripsi dan dekripsi
            $enkripsiSaldo = $this->encodePrice($saldo, $secretKey);

            //Enkripsi password tempat
            $password = $request->password;  // Ganti dengan password pengguna
            $hash_password = password_hash($password, PASSWORD_BCRYPT);

            //Upload file
            $destinasi = "/upload";
            $file1 = $request->file("ktp");
            $file2 = $request->file("npwp");
            $ktp = uniqid().".".$file1->getClientOriginalExtension();
            $npwp = uniqid().".".$file2->getClientOriginalExtension();

            $db = [];
            if(Session::has("regTempat")) $db = Session::get("regTempat");//ambil data lama

            array_push($db, [//masukin data baru
                "nama" => $request->nama,
                "pemilik" => $request->pemilik,
                "email" => $request->email,
                "telepon" => $request->telepon,
                "ktp" => $ktp,
                "npwp" => $npwp,
                "password" => $hash_password,
                "saldo" => $enkripsiSaldo
            ]);
            Session::put("regTempat",$db);

            return redirect()->back()->with("success", "Registrasi menunggu konfirmasi admin!");
        }
        else {
            return redirect()->back()->with("error", "Konfirmasi password salah!");
        }
    }
}
