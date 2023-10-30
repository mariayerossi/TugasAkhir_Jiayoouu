<?php

namespace App\Http\Controllers;

use App\Models\customer;
use App\Models\pemilikAlat;
use App\Models\pihakTempat;
use App\Models\registerTempat;
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
            "nama" => 'required|min:5|regex:/^[^0-9]*$/',
            "email" => 'required|email',
            "telepon" => 'required|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3,4}[-\s\.]?[0-9]{4,6}$/',
            "password" => 'required|min:8',
            "konfirmasi" => 'required|min:8'
        ], [
            "nama.regex" => ":attribute tidak boleh mengandung angka!",
            "nama.required" => ":attribute lengkap tidak boleh kosong!",
            "required" => ":attribute tidak boleh kosong!",
            "nama.min" => ":attribute lengkap tidak valid!",
            "email.required" => "alamat :attribute tidak boleh kosong!",
            "email" => "alamat :attribute tidak valid!",
            "telepon.required" => "nomer :attribute tidak boleh kosong!",
            "regex" => "nomer :attribute tidak valid!",
            "password.min" => ":attribute harus memiliki setidaknya 8 karakter!",
            "konfirmasi.required" => ":attribute password tidak boleh kosong!",
            "konfirmasi.min" => ":attribute password harus memiliki setidaknya 8 karakter!"
        ]);

        if ($request->password == $request->konfirmasi) {
            // //cek apakah ada email serupa
            $user = new customer();
            $data1 = $user->cek_email_user($request->email);

            $pemilik = new pemilikAlat();
            $data2 = $pemilik->cek_email_pemilik($request->email);

            $tempat = new pihakTempat();
            $data3 = $tempat->cek_email_tempat($request->email);

            if (!$data1->isEmpty() || !$data2->isEmpty() || !$data3->isEmpty()) {
                return redirect()->back()->withInput()->with("error", "Email sudah pernah digunakan!");
            }
            else {
                //enkripsi saldo user
                $saldo = "0"; 
                $secretKey = "mysecretkey"; // Kunci rahasia untuk melakukan enkripsi dan dekripsi
                $enkripsiSaldo = $this->encodePrice($saldo, $secretKey);

                //Enkripsi password user
                $password = $request->password;  // Ganti dengan password pengguna
                $hash_password = password_hash($password, PASSWORD_BCRYPT);

                $data = [
                    "nama"=>$request->nama,
                    "email"=>$request->email,
                    "telepon"=>$request->telepon,
                    "password" => $hash_password,
                    "saldo" => $enkripsiSaldo
                ];
                $user = new customer();
                $user->insertUser($data);
        
                return redirect()->back()->with("success", "Berhasil Register!");
            }
        }
        else {
            return redirect()->back()->withInput()->with("error", "Konfirmasi password salah!");
        }
    }

    public function verifikasiUser() {
        
    }

    // Register Pemilik
    public function registerPemilik(Request $request){
        $request->validate([
            "nama" => 'required|min:5|regex:/^[^0-9]*$/',
            "email" => 'required|email',
            "telepon" => 'required|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3,4}[-\s\.]?[0-9]{4,6}$/',
            "ktp" => 'required|max:5120',
            "password" => 'required|min:8',
            "konfirmasi" => 'required|min:8'
        ], [
            "nama.regex" => ":attribute tidak boleh mengandung angka!",
            "nama.required" => ":attribute lengkap tidak boleh kosong!",
            "required" => ":attribute tidak boleh kosong!",
            "nama.min" => ":attribute lengkap tidak valid!",
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
            //cek apakah ada email serupa
            $user = new customer();
            $data1 = $user->cek_email_user($request->email);

            $pemilik = new pemilikAlat();
            $data2 = $pemilik->cek_email_pemilik($request->email);

            $tempat = new pihakTempat();
            $data3 = $tempat->cek_email_tempat($request->email);
            
            if (!$data1->isEmpty() || !$data2->isEmpty() || !$data3->isEmpty()) {
                return redirect()->back()->withInput()->with("error", "Email sudah pernah digunakan!");
            }
            else {
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
                $file->move(public_path($destinasi),$ktp);

                $data = [
                    "nama"=>$request->nama,
                    "email"=>$request->email,
                    "telepon"=>$request->telepon,
                    "ktp" =>$ktp,
                    "password" => $hash_password,
                    "saldo" => $enkripsiSaldo
                ];
                $pemilik = new pemilikAlat();
                $pemilik->insertPemilik($data);
        
                return redirect()->back()->with("success", "Berhasil Register!");
            }
        }
        else {
            return redirect()->back()->withInput()->with("error", "Konfirmasi password salah!");
        }
    }

    //Register Tempat
    public function registerTempat(Request $request){
        $request->validate([
            "nama" => 'required|min:5',
            "pemilik" => 'required|min:5|regex:/^[^0-9]*$/',
            "email" => 'required|email',
            "telepon" => 'required|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3,4}[-\s\.]?[0-9]{4,6}$/',
            "alamat" => 'required|min:10',
            "ktp" => 'required|max:5120',
            "npwp" => 'required|max:5120',
            "password" => 'required|min:8',
            "konfirmasi" => 'required|min:8'
        ], [
            "pemilik.regex" => "nama pemilik tidak boleh mengandung angka!",
            "required" => ":attribute tidak boleh kosong!",
            "nama.required" => ":attribute tempat olahraga tidak boleh kosong!",
            "nama.min" => ":attribute tempat olahraga tidak valid!",
            "pemilik.required" => "nama lengkap :attribute tempat olahraga tidak boleh kosong!",
            "pemilik.min" => "nama lengkap :attribute tempat olahraga tidak valid",
            "email.required" => "alamat :attribute tidak boleh kosong!",
            "email" => "alamat :attribute tidak valid!",
            "telepon.required" => "nomer :attribute tidak boleh kosong!",
            "regex" => "nomer :attribute tidak valid!",
            "alamat.required" => ":attribute lengkap tidak boleh kosong!",
            "alamat.min" => ":attribute tidak valid!",
            "ktp.required" => "foto KTP tidak boleh kosong!",
            "ktp.max" => "ukuran gambar KTP tidak boleh lebih dari 5 MB!",
            "npwp.required" => "foto NPWP tidak boleh kosong!",
            "npwp.max" => "ukuran gambar NPWP tidak boleh lebih dari 5 MB!",
            "password.min" => ":attribute harus memiliki setidaknya 8 karakter!",
            "konfirmasi.required" => ":attribute password tidak boleh kosong!",
            "konfirmasi.min" => ":attribute password harus memiliki setidaknya 8 karakter!"
        ]);

        if ($request->password == $request->konfirmasi) {
            //cek apakah ada email serupa
            $user = new customer();
            $data1 = $user->cek_email_user($request->email);

            $pemilik = new pemilikAlat();
            $data2 = $pemilik->cek_email_pemilik($request->email);

            $tempat = new pihakTempat();
            $data3 = $tempat->cek_email_tempat($request->email);

            if (!$data1->isEmpty() || !$data2->isEmpty() || !$data3->isEmpty()) {
                return redirect()->back()->withInput()->with("error", "Email sudah pernah digunakan!");
            }
            else {
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
                $file1->move(public_path($destinasi),$ktp);
                $file2->move(public_path($destinasi),$npwp);

                $data = [
                    "nama" => $request->nama,
                    "pemilik" => $request->pemilik,
                    "email" => $request->email,
                    "telepon" => $request->telepon,
                    "alamat" => $request->alamat,
                    "ktp" => $ktp,
                    "npwp" => $npwp,
                    "password" => $hash_password,
                    "saldo" => $enkripsiSaldo
                ];
                $reg = new registerTempat();
                $reg->insertRegister($data);

                return redirect()->back()->with("success", "Registrasi menunggu konfirmasi admin!");
            }
        }
        else {
            return redirect()->back()->withInput()->with("error", "Konfirmasi password salah!");
        }
    }

    public function login(Request $request){
        $request->validate([
            "email" => 'required|email',
            "password" => 'required|min:8'
        ], [
            "email.required" => "alamat :attribute tidak boleh kosong!",
            "email" => "alamat :attribute tidak valid!",
            "password.required" => ":attribute tidak boleh kosong!",
            "min" => ":attribute tidak valid!"
        ]);

        if ($request->email == "admin@gmail.com" && $request->password == "asdfghjkl") {
            //masuk role admin
            Session::put("role","admin");
            return redirect('/admin/beranda');
        }
        else {
            //cek apakah role user
            $user = new customer();
            $dataUser = $user->cek_email_user($request->email);
            // dd($dataUser);
            if (!$dataUser->isEmpty()) {
                if (password_verify($request->password, $dataUser->first()->password_user)) {
                    Session::put("role","customer");
                    Session::put("dataRole", $dataUser->first());
                    return redirect('/customer/beranda');
                } else {
                    return redirect()->back()->withInput()->with("error", "Password salah!");
                }
            }

            //cek apakah role pemilik
            $pemilik = new pemilikAlat();
            $dataPemilik = $pemilik->cek_email_pemilik($request->email);
            if (!$dataPemilik->isEmpty()) {
                if (password_verify($request->password, $dataPemilik->first()->password_pemilik)) {
                    //diarahkan ke halaman pemilik
                    Session::put("role","pemilik");
                    Session::put("dataRole", $dataPemilik->first());
                    return redirect('/pemilik/beranda');
                } else {
                    return redirect()->back()->withInput()->with("error", "Password salah!");
                }
            }

            //cek apakah role tempat
            $tempat = new pihakTempat();
            $dataTempat = $tempat->cek_email_tempat($request->email);
            if (!$dataTempat->isEmpty()) {
                if (password_verify($request->password, $dataTempat->first()->password_tempat)) {
                    Session::put("role","tempat");
                    Session::put("dataRole", $dataTempat->first());
                    return redirect('/tempat/beranda');
                } else {
                    return redirect()->back()->withInput()->with("error", "Password salah!");
                }
            }
            else {
                return redirect()->back()->withInput()->with("error", "Akun tidak ditemukan! Silahkan register terlebih dahulu!");
            }
        }
    }

    public function logout(){
        Session::forget('role');
        if (Session::has("dataRole")) {
            Session::forget('dataRole');
        }
        return redirect("/login");
    }

    public function konfirmasiTempat(Request $request){
        $reg = registerTempat::find($request->id);
        
        $data = [
            "nama" => $reg->nama_tempat_reg,
            "pemilik" => $reg->nama_pemilik_tempat_reg,
            "email" => $reg->email_tempat_reg,
            "telepon" => $reg->telepon_tempat_reg,
            "alamat" => $reg->alamat_tempat_reg,
            "ktp" => $reg->ktp_tempat_reg,
            "npwp" => $reg->npwp_tempat_reg,
            "password" => $reg->password_tempat_reg,
            "saldo" => $reg->saldo_tempat_reg
        ];
        $tempat = new pihakTempat();
        $tempat->insertTempat($data);

        $data2 = [
            "id" => $request->id
        ];
        $regis = new registerTempat();
        $regis->deleteRegister($data2);

        return redirect()->back()->with("success", "Berhasil Konfirmasi Register!");
    }

    public function tolakKonfirmasiTempat(Request $request){
        $data2 = [
            "id" => $request->id
        ];
        $regis = new registerTempat();
        $regis->deleteRegister($data2);

        return redirect()->back()->with("success", "Berhasil Menolak Register!");
    }
}
