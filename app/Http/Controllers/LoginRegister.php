<?php

namespace App\Http\Controllers;

use App\Models\customer;
use App\Models\pemilikAlat;
use App\Models\pihakTempat;
use App\Models\registerTempat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\notifikasiEmail;

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
            "password" => 'required|min:8|max:32|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/',
            "konfirmasi" => 'required|min:8|max:32|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/'
        ], [
            "nama.regex" => ":attribute tidak boleh mengandung angka!",
            "nama.required" => ":attribute lengkap tidak boleh kosong!",
            "required" => ":attribute tidak boleh kosong!",
            "nama.min" => ":attribute lengkap tidak valid!",
            "email.required" => "alamat :attribute tidak boleh kosong!",
            "email.email" => "alamat :attribute tidak valid!",
            "telepon.required" => "nomer :attribute tidak boleh kosong!",
            "telepon.regex" => "nomer :attribute tidak valid!",
            "password.min" => ":attribute harus memiliki setidaknya 8 karakter!",
            "password.max" => ":attribute hanya boleh memiliki maksimal 32 karakter!",
            "password.regex" => ":attribute harus mengandung huruf, minimal satu angka, dan minimal satu simbol!",
            "konfirmasi.required" => ":attribute password tidak boleh kosong!",
            "konfirmasi.min" => ":attribute password harus memiliki setidaknya 8 karakter!",
            "konfirmasi.max" => ":attribute password hanya boleh memiliki maksimal 32 karakter!",
            "konfirmasi.regex" => ":attribute password harus mengandung huruf, minimal satu angka, dan minimal satu simbol!"
        ]);

        if ($request->password == $request->konfirmasi) {
            // cek apakah ada email serupa
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
                    "nama"=>ucwords($request->nama),
                    "email"=>$request->email,
                    "telepon"=>$request->telepon,
                    "password" => $hash_password,
                    "saldo" => $enkripsiSaldo
                ];
                $user = new customer();
                $id = $user->insertUser($data);

                $dataNotif = [
                    "subject" => "⚠️Verifikasi Akun Anda!⚠️",
                    "judul" => "Mohon untuk Verifikasi Akun Anda Terlebih Dahulu!",
                    "nama_user" => ucwords($request->nama),
                    "url" => "https://sportiva.my.id/verifikasiUser/".$id,
                    "button" => "Verifikasi Sekarang",
                    "isi" => "Anda baru saja mendaftarkan email ini di Sportiva. Silahkan Verifikasi akun anda sebelum login ya! Terima Kasih telah menggunakan Sportiva! 😊"
                ];
                $e = new notifikasiEmail();
                $e->sendEmail($request->email, $dataNotif);
        
                return redirect()->back()->with("success", "Berhasil Register! Verifikasi Akun akan dikirim melalui email");
            }
        }
        else {
            return redirect()->back()->withInput()->with("error", "Konfirmasi password salah!");
        }
    }

    public function verifikasiUser(Request $request) {
        date_default_timezone_set("Asia/Jakarta");
        $tgl = date("Y-m-d H:i:s");

        $data = [
            "id" => $request->id,
            "tanggal" => $tgl
        ];
        $user = new customer();
        $user->verifikasiEmail($data);
        return view("verifikasiEmail");
    }

    // Register Pemilik
    public function registerPemilik(Request $request){
        $request->validate([
            "nama" => 'required|min:5|regex:/^[^0-9]*$/',
            "email" => 'required|email',
            "telepon" => 'required|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3,4}[-\s\.]?[0-9]{4,6}$/',
            "ktp" => 'required|max:5120',
            "password" => 'required|min:8|max:32|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/',
            "konfirmasi" => 'required|min:8|max:32|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/'
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
            "password.max" => ":attribute hanya boleh memiliki maksimal 32 karakter!",
            "password.regex" => ":attribute harus mengandung huruf, minimal satu angka, dan minimal satu simbol!",
            "konfirmasi.required" => ":attribute password tidak boleh kosong!",
            "konfirmasi.min" => ":attribute password harus memiliki setidaknya 8 karakter!",
            "konfirmasi.max" => ":attribute password hanya boleh memiliki maksimal 32 karakter!",
            "konfirmasi.regex" => ":attribute password harus mengandung huruf, minimal satu angka, dan minimal satu simbol!"
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
                    "nama"=>ucwords($request->nama),
                    "email"=>$request->email,
                    "telepon"=>$request->telepon,
                    "ktp" =>$ktp,
                    "password" => $hash_password,
                    "saldo" => $enkripsiSaldo
                ];
                $pemilik = new pemilikAlat();
                $id = $pemilik->insertPemilik($data);

                $dataNotif = [
                    "subject" => "⚠️Verifikasi Akun Anda!⚠️",
                    "judul" => "Mohon untuk Verifikasi Akun Anda Terlebih Dahulu!",
                    "nama_user" => ucwords($request->nama),
                    "url" => "https://sportiva.my.id/verifikasiPemilik/".$id,
                    "button" => "Verifikasi Sekarang",
                    "isi" => "Anda baru saja mendaftarkan email ini di Sportiva. Silahkan Verifikasi akun anda sebelum login ya! Terima Kasih telah menggunakan Sportiva! 😊"
                ];
                $e = new notifikasiEmail();
                $e->sendEmail($request->email, $dataNotif);
        
                return redirect()->back()->with("success", "Berhasil Register! Verifikasi Akun akan dikirim melalui email");
            }
        }
        else {
            return redirect()->back()->withInput()->with("error", "Konfirmasi password salah!");
        }
    }

    public function verifikasiPemilik(Request $request) {
        date_default_timezone_set("Asia/Jakarta");
        $tgl = date("Y-m-d H:i:s");

        $data = [
            "id" => $request->id,
            "tanggal" => $tgl
        ];
        $pemilik = new pemilikAlat();
        $pemilik->verifikasiEmail($data);
        return view("verifikasiEmail");
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
            "password" => 'required|min:8|max:32|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/',
            "konfirmasi" => 'required|min:8|max:32|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).+$/'
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
            "password.max" => ":attribute hanya boleh memiliki maksimal 32 karakter!",
            "password.regex" => ":attribute harus mengandung huruf, minimal satu angka, dan minimal satu simbol!",
            "konfirmasi.required" => ":attribute password tidak boleh kosong!",
            "konfirmasi.min" => ":attribute password harus memiliki setidaknya 8 karakter!",
            "konfirmasi.max" => ":attribute password hanya boleh memiliki maksimal 32 karakter!",
            "konfirmasi.regex" => ":attribute password harus mengandung huruf, minimal satu angka, dan minimal satu simbol!"
        ]);

        if ($request->password == $request->konfirmasi) {
            //cek apakah ada email serupa
            $user = new customer();
            $data1 = $user->cek_email_user($request->email);

            $pemilik = new pemilikAlat();
            $data2 = $pemilik->cek_email_pemilik($request->email);

            $tempat = new pihakTempat();
            $data3 = $tempat->cek_email_tempat($request->email);
            
            $reg = new registerTempat();
            $data4 = $reg->cek_email_tempat($request->email);

            if (!$data1->isEmpty() || !$data2->isEmpty() || !$data3->isEmpty() || !$data4->isEmpty()) {
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
                    "nama" => ucwords($request->nama),
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
                $id = $reg->insertRegister($data);

                $dataNotif = [
                    "subject" => "⚠️Verifikasi Akun Anda!⚠️",
                    "judul" => "Mohon untuk Verifikasi Akun Anda Terlebih Dahulu!",
                    "nama_user" => ucwords($request->nama),
                    "url" => "https://sportiva.my.id/verifikasiTempat/".$id,
                    "button" => "Verifikasi Sekarang",
                    "isi" => "Anda baru saja mendaftarkan email ini di Sportiva. Silahkan Verifikasi akun anda sebelum login ya! Terima Kasih telah menggunakan Sportiva! 😊"
                ];
                $e = new notifikasiEmail();
                $e->sendEmail($request->email, $dataNotif);

                return redirect()->back()->with("success", "Verifikasi Akun akan dikirim melalui email!");
            }
        }
        else {
            return redirect()->back()->withInput()->with("error", "Konfirmasi password salah!");
        }
    }

    public function verifikasiTempat(Request $request) {
        date_default_timezone_set("Asia/Jakarta");
        $tgl = date("Y-m-d H:i:s");

        $data = [
            "id" => $request->id,
            "tanggal" => $tgl
        ];
        $temp = new registerTempat();
        $temp->verifikasiEmail($data);
        return view("verifikasiEmail");
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
                    if ($dataUser->first()->email_verified_at != null) {
                        Session::put("role","customer");
                        Session::put("dataRole", $dataUser->first());
                        return redirect('/customer/beranda');
                    }
                    else {
                        return redirect()->back()->withInput()->with("error", "Gagal Login! Anda belum melakukan verifikasi email!");
                    }
                } else {
                    return redirect()->back()->withInput()->with("error", "Password salah!");
                }
            }

            //cek apakah role pemilik
            $pemilik = new pemilikAlat();
            $dataPemilik = $pemilik->cek_email_pemilik($request->email);
            if (!$dataPemilik->isEmpty()) {
                if (password_verify($request->password, $dataPemilik->first()->password_pemilik)) {
                    if ($dataPemilik->first()->email_verified_at != null) {
                        //diarahkan ke halaman pemilik
                        Session::put("role","pemilik");
                        Session::put("dataRole", $dataPemilik->first());
                        return redirect('/pemilik/beranda');
                    }
                    else {
                        return redirect()->back()->withInput()->with("error", "Gagal Login! Anda belum melakukan verifikasi email!");
                    }
                } else {
                    return redirect()->back()->withInput()->with("error", "Password salah!");
                }
            }

            //cek apakah role tempat
            $reg = new registerTempat();
            $dataReg = $reg->cek_email_tempat($request->email);

            $tempat = new pihakTempat();
            $dataTempat = $tempat->cek_email_tempat($request->email);
            if (!$dataTempat->isEmpty()) {
                if (password_verify($request->password, $dataTempat->first()->password_tempat)) {
                    if ($dataTempat->first()->email_verified_at != null) {
                        Session::put("role","tempat");
                        Session::put("dataRole", $dataTempat->first());
                        return redirect('/tempat/beranda');
                    }
                    else {
                        return redirect()->back()->withInput()->with("error", "Gagal Login! Anda belum melakukan verifikasi email!");
                    }
                } else {
                    return redirect()->back()->withInput()->with("error", "Password salah!");
                }
            }
            else if (!$dataReg->isEmpty()) {
                return redirect()->back()->withInput()->with("error", "Akun anda masih menunggu konfirmasi Admin!");
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
            "saldo" => $reg->saldo_tempat_reg,
            "veri" => $reg->email_verified_at
        ];
        $tempat = new pihakTempat();
        $tempat->insertTempat($data);

        $data2 = [
            "id" => $request->id
        ];
        $regis = new registerTempat();
        $regis->deleteRegister($data2);

        return response()->json(['success' => true, 'message' => 'Berhasil Konfirmasi Register!']);
    }

    public function tolakKonfirmasiTempat(Request $request){
        $data2 = [
            "id" => $request->id
        ];
        $regis = new registerTempat();
        $regis->deleteRegister($data2);

        return response()->json(['success' => true, 'message' => 'Berhasil Menolak Register!']);
    }
}
