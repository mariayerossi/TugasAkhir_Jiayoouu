<?php

use App\Http\Controllers\AlatOlahraga;
use App\Http\Controllers\KategoriOlahraga;
use App\Http\Controllers\KomplainRequest;
use App\Http\Controllers\LapanganOlahraga;
use App\Http\Controllers\LoginRegister;
use App\Http\Controllers\Negosiasi;
use App\Http\Controllers\RequestPenawaran;
use App\Http\Controllers\RequestPermintaan;
use App\Http\Controllers\SewaSendiri;
use App\Http\Middleware\CekAdmin;
use App\Http\Middleware\CekPemilik;
use App\Http\Middleware\CekTempat;
use App\Http\Middleware\Guest;
use App\Models\alatOlahraga as ModelsAlatOlahraga;
use App\Models\htrans as ModelsHtrans;
use App\Models\dtrans as ModelsDtrans;
use App\Models\slotWaktu as ModelsSlotWaktu;
use App\Models\customer;
use App\Models\filesAlatOlahraga;
use App\Models\filesLapanganOlahraga;
use App\Models\kategori;
use App\Models\lapanganOlahraga as ModelsLapanganOlahraga;
use App\Models\negosiasi as ModelsNegosiasi;
use App\Models\komplainRequest as ModelsKomplainRequest;
use App\Models\pemilikAlat;
use App\Models\pihakTempat;
use App\Models\registerTempat;
use App\Models\requestPenawaran as ModelsRequestPenawaran;
use App\Models\requestPermintaan as ModelsRequestPermintaan;
use App\Models\sewaSendiri as ModelsSewaSendiri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});

// -------------------------------
// TAMPILAN LOGIN REGISTER
// -------------------------------
Route::view("/register", "register")->middleware([Guest::class]);
Route::view("/login", "login")->middleware([Guest::class]);
Route::view("/registerTempat", "tempat.registerTempat")->middleware([Guest::class]);
Route::view("/registerPemilik", "pemilik.registerPemilik")->middleware([Guest::class]);

// -------------------------------
// PROSES LOGIN REGISTER
// -------------------------------
Route::post("/registerUser", [LoginRegister::class, "registerUser"]);
Route::post("/registerPemilik", [LoginRegister::class, "registerPemilik"]);
Route::post("/registerTempat", [LoginRegister::class, "registerTempat"]);
Route::post("/login", [LoginRegister::class, "login"]);
Route::get("/logout", [LoginRegister::class, "logout"]);

// -------------------------------
// HALAMAN ADMIN
// -------------------------------
Route::prefix("/admin")->group(function(){
    Route::get("/beranda", function () {
        $cust = new customer();
        $param["jumlahCustomer"] = $cust->count_all_data_admin();
        $alat = new ModelsAlatOlahraga();
        $param["jumlahAlat"] = $alat->count_all_data_admin();
        $lapangan = new ModelsLapanganOlahraga();
        $param["jumlahLapangan"] = $lapangan->count_all_data_admin();
        return view("admin.beranda")->with($param);
    })->middleware([CekAdmin::class]);
    Route::get("/registrasi_tempat", function () {
        $reg = new registerTempat();
        $param["register"] = $reg->get_all_data();
        return view("admin.registrasi_tempat")->with($param);
    })->middleware([CekAdmin::class]);
    Route::get("/konfirmasiTempat/{id}", [LoginRegister::class, "konfirmasiTempat"]);
    Route::get("/tolakKonfirmasiTempat/{id}", [LoginRegister::class, "tolakKonfirmasiTempat"]);
    Route::get("/masterKategori", function () {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        return view("admin.masterKategori")->with($param);
    })->middleware([CekAdmin::class]);
    Route::post("/tambahKategori", [KategoriOlahraga::class, "tambahKategori"]);
    Route::get("/hapusKategori/{id}", [KategoriOlahraga::class, "hapusKategori"]);
    Route::get("/daftarCustomer", function () {
        $cust = new customer();
        $param["customer"] = $cust->get_all_data();
        return view("admin.users.daftarCustomer")->with($param);
    })->middleware([CekAdmin::class]);
    Route::get("/daftarPemilik", function () {
        $pemilik = new pemilikAlat();
        $param["pemilik"] = $pemilik->get_all_data();
        return view("admin.users.daftarPemilik")->with($param);
    })->middleware([CekAdmin::class]);
    Route::get("/daftarTempat", function () {
        $tempat = new pihakTempat();
        $param["tempat"] = $tempat->get_all_data();
        return view("admin.users.daftarTempat")->with($param);
    })->middleware([CekAdmin::class]);

    //Bagian Alat Olahraga
    Route::prefix("/alat")->group(function(){
        Route::get("/cariAlat", function () {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            $alat = new ModelsAlatOlahraga();
            $param["alat"] = $alat->get_all_data_for_admin();
            $files = new filesAlatOlahraga();
            $param["files"] = $files;
            return view("admin.produk.cariAlat")->with($param);
        })->middleware([CekAdmin::class]);
        Route::get("/detailAlatUmum/{id}", function ($id) {//melihat detail alat olahraga milik org lain
            $alat = new ModelsAlatOlahraga();
            $param["alat"] = $alat->get_all_data_by_id($id);
            $files = new filesAlatOlahraga();
            $param["files"] = $files->get_all_data($id);
            return view("admin.produk.detailAlatUmum")->with($param);
        })->middleware([CekAdmin::class]);
        Route::get("/searchAlat", [AlatOlahraga::class, "searchAlat"]);
    });

    //Bagian Lapangan Olahraga
    Route::prefix("/lapangan")->group(function(){
        Route::get("/cariLapangan", function () {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            $lapa = new ModelsLapanganOlahraga();
            $param["lapangan"] = $lapa->get_all_data_for_admin();
            $files = new filesLapanganOlahraga();
            $param["files"] = $files;
            return view("admin.produk.cariLapangan")->with($param);
        })->middleware([CekAdmin::class]);
        Route::get("/searchLapangan", [LapanganOlahraga::class, "searchLapangan"]);
        Route::get("/detailLapanganUmum/{id}", function ($id) {//melihat detail lapangan olahraga milik org lain
            $lapa = new ModelsLapanganOlahraga();
            $param["lapangan"] = $lapa->get_all_data_by_id($id);
            $files = new filesLapanganOlahraga();
            $param["files"] = $files->get_all_data($id);
            $slot = new ModelsSlotWaktu();
            $param["slot"] = $slot->get_all_data_by_lapangan($id);

            $per = new ModelsRequestPermintaan();
            $param["permintaan"] = $per->get_all_data_by_lapangan($id);
            $pen = new ModelsRequestPenawaran();
            $param["penawaran"] = $pen->get_all_data_by_lapangan($id);
            $sewa = new ModelsSewaSendiri();
            $param["sewa"] = $sewa->get_all_data_by_lapangan($id);
            return view("admin.produk.detailLapanganUmum")->with($param);
        })->middleware([CekAdmin::class]);
    });

    Route::prefix("/transaksi")->group(function(){
        Route::get("/daftarTransaksi", function () {
            $trans = new ModelsHtrans();
            $param["trans"] = $trans->get_all_data_by_admin();
            return view("admin.transaksi.daftarTransaksi")->with($param);
        })->middleware([CekAdmin::class]);
        Route::get("/detailTransaksi/{id}", function ($id) {
            $htrans = new ModelsHtrans();
            $param["htrans"] = $htrans->get_all_data_by_id($id);
            $dtrans = new ModelsDtrans();
            $param["dtrans"] = $dtrans->get_all_data_by_id_htrans($id);
            return view("admin.transaksi.detailTransaksi")->with($param);
        })->middleware([CekAdmin::class]);
    });

    Route::prefix("/komplain")->group(function(){
        Route::prefix("/request")->group(function(){
            Route::get("/daftarKomplain", function () {
                $komp = new ModelsKomplainRequest();
                $param["komplain"] = $komp->get_all_data_by_admin();
                return view("admin.komplain.request.daftarKomplain")->with($param);
            })->middleware([CekAdmin::class]);
        });
    });
});

// -------------------------------
// HALAMAN PEMILIK ALAT
// -------------------------------
Route::prefix("/pemilik")->group(function(){
    Route::get("/beranda", function () {
        $id = Session::get("dataRole")->id_pemilik;
        $alat = new ModelsAlatOlahraga();
        $param["jumlahAlat"] = $alat->count_all_data($id, "Pemilik");
        $minta = new ModelsRequestPermintaan();
        $param["jumlahPermintaan"] = $minta->count_all_data_pemilik($id);
        $tawar = new ModelsRequestPenawaran();
        $param["jumlahPenawaran"] = $tawar->count_all_data_pemilik($id);
        return view("pemilik.beranda")->with($param);
    })->middleware([CekPemilik::class]);
    Route::get("/masterAlat", function () {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        return view("pemilik.alat.masterAlat")->with($param);
    })->middleware([CekPemilik::class]);
    Route::post("/tambahAlat", [AlatOlahraga::class, "tambahAlat"]);
    Route::get("/daftarAlat", function () {
        $id = Session::get("dataRole")->id_pemilik;
        $files = new filesAlatOlahraga();
        $alat = new ModelsAlatOlahraga();
        $param["alat"] = $alat->get_all_data($id, "Pemilik");
        $param["files"] = $files;
        return view("pemilik.alat.daftarAlat")->with($param);
    })->middleware([CekPemilik::class]);
    Route::get("/lihatDetail/{id}", function ($id) {//melihat detail alat olahraga miliknya
        $alat = new ModelsAlatOlahraga();
        $param["alat"] = $alat->get_all_data_by_id($id);
        $files = new filesAlatOlahraga();
        $param["files"] = $files->get_all_data($id);
        return view("pemilik.alat.detailAlat")->with($param);
        // echo $id;
    })->middleware([CekPemilik::class]);
    Route::get("/editAlat/{id}", function ($id) {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        $alat = new ModelsAlatOlahraga();
        $param["alat"] = $alat->get_all_data_by_id($id);
        $files = new filesAlatOlahraga();
        $param["files"] = $files->get_all_data($id);
        return view("pemilik.alat.editAlat")->with($param);
        // echo $id;
    })->middleware([CekPemilik::class]);
    Route::post("/editAlat", [AlatOlahraga::class, "editAlat"]);
    Route::get("/cariLapangan", function () {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        $lapa = new ModelsLapanganOlahraga();
        $param["lapangan"] = $lapa->get_all_data2();
        $files = new filesLapanganOlahraga();
        $param["files"] = $files;
        return view("pemilik.cariLapangan")->with($param);
    })->middleware([CekPemilik::class]);
    Route::get("/searchLapangan", [LapanganOlahraga::class, "searchLapangan"]);
    Route::get("/detailLapanganUmum/{id}", function ($id) {//melihat detail lapangan olahraga milik org lain
        $lapa = new ModelsLapanganOlahraga();
        $param["lapangan"] = $lapa->get_all_data_by_id($id);
        $files = new filesLapanganOlahraga();
        $param["files"] = $files->get_all_data($id);
        $role = Session::get("dataRole")->id_pemilik;
        $alat = new ModelsAlatOlahraga();
        $param["alat"] = $alat->get_all_data_status($role);
        $slot = new ModelsSlotWaktu();
        $param["slot"] = $slot->get_all_data_by_lapangan($id);

        $per = new ModelsRequestPermintaan();
        $param["permintaan"] = $per->get_all_data_by_lapangan($id);
        $pen = new ModelsRequestPenawaran();
        $param["penawaran"] = $pen->get_all_data_by_lapangan($id);
        $sewa = new ModelsSewaSendiri();
        $param["sewa"] = $sewa->get_all_data_by_lapangan($id);
        return view("pemilik.detailLapanganUmum")->with($param);
    })->middleware([CekPemilik::class]);
    Route::post('editHargaKomisi',[AlatOlahraga::class, "editHargaKomisi"]);

    //Request permintaan
    Route::prefix("/permintaan")->group(function(){
        Route::get("/daftarPermintaan", function () {
            $role = Session::get("dataRole")->id_pemilik;
            $req = new ModelsRequestPermintaan();
            $param["baru"] = $req->get_all_data_by_pemilik_baru($role);
            $param["diterima"] = $req->get_all_data_by_pemilik_diterima($role);
            $param["ditolak"] = $req->get_all_data_by_pemilik_ditolak($role);
            $param["selesai"] = $req->get_all_data_by_pemilik_selesai($role);
            $param["dibatalkan"] = $req->get_all_data_by_pemilik_dibatalkan($role);
            $param["dikomplain"] = $req->get_all_data_by_pemilik_dikomplain($role);
            return view("pemilik.permintaan.daftarPermintaan")->with($param);
        })->middleware([CekPemilik::class]);
        Route::get("/detailPermintaanNego/{id}", function ($id) {
            $role = Session::get("dataRole")->id_pemilik;
            $req = new ModelsRequestPermintaan();
            $param["permintaan"] = $req->get_all_data_by_id($id);
            $nego = new ModelsNegosiasi();
            $param["nego"] = $nego->get_all_data_by_id_permintaan($id);
            $komplain = new ModelsKomplainRequest();
            $param["komplain"] = $komplain->get_all_data_by_id_req_pemilik($id, "Permintaan", $role);
            return view("pemilik.permintaan.detailPermintaanNego")->with($param);
        })->middleware([CekPemilik::class]);
        Route::post("/terimaPermintaan", [RequestPermintaan::class, "terimaPermintaan"]);
        Route::post("/tolakPermintaan", [RequestPermintaan::class, "tolakPermintaan"]);

        //Bagian negosiasi
        Route::prefix("/negosiasi")->group(function(){
            Route::post("tambahNego", [Negosiasi::class, "tambahNegoPermintaan"]);
        });
    });

    //Request penawaran
    Route::prefix("/penawaran")->group(function(){
        Route::post("/requestPenawaranAlat", [RequestPenawaran::class, "ajukanPenawaran"]);
        Route::get("/daftarPenawaran", function () {
            $role = Session::get("dataRole")->id_pemilik;
            $req = new ModelsRequestPenawaran();
            $param["baru"] = $req->get_all_data_by_pemilik_baru($role);
            $param["diterima"] = $req->get_all_data_by_pemilik_diterima($role);
            $param["ditolak"] = $req->get_all_data_by_pemilik_ditolak($role);
            $param["selesai"] = $req->get_all_data_by_pemilik_selesai($role);
            $param["dibatalkan"] = $req->get_all_data_by_pemilik_dibatalkan($role);
            $param["dikomplain"] = $req->get_all_data_by_pemilik_dikomplain($role);
            return view("pemilik.penawaran.daftarPenawaran")->with($param);
        })->middleware([CekPemilik::class]);
        Route::get("/detailPenawaranNego/{id}", function ($id) {
            $role = Session::get("dataRole")->id_pemilik;
            $req = new ModelsRequestPenawaran();
            $param["penawaran"] = $req->get_all_data_by_id($id);
            $nego = new ModelsNegosiasi();
            $param["nego"] = $nego->get_all_data_by_id_penawaran($id);
            $komplain = new ModelsKomplainRequest();
            $param["komplain"] = $komplain->get_all_data_by_id_req_pemilik($id, "Penawaran", $role);
            return view("pemilik.penawaran.detailPenawaranNego")->with($param);
        })->middleware([CekPemilik::class]);
        Route::post("/batalPenawaran", [RequestPenawaran::class, "batalPenawaran"]);
        Route::post("/konfirmasiPenawaran", [RequestPenawaran::class, "konfirmasiPenawaran"]);

        //Bagian negosiasi
        Route::prefix("/negosiasi")->group(function(){
            Route::post("tambahNego", [Negosiasi::class, "tambahNegoPenawaran"]);
        });
    });

    Route::prefix("/komplain")->group(function(){
        Route::post("tambahKomplain", [KomplainRequest::class, "tambahKomplain"]);
    });

    //Bagian transaksi
    Route::prefix("/disewakan")->group(function(){
        Route::get("/daftarDisewakan", function () {
            $role = Session::get("dataRole")->id_pemilik;
            $trans = new ModelsDtrans();
            $param["disewakan"] = $trans->get_all_data_by_pemilik($role);
            return view("pemilik.disewakan.daftarDisewakan")->with($param);
        })->middleware([CekPemilik::class]);
    });
});

// -------------------------------
// HALAMAN PIHAK TEMPAT
// -------------------------------
Route::prefix("/tempat")->group(function(){
    Route::get("/beranda", function () {
        $role = Session::get("dataRole")->id_tempat;
        $lapa = new ModelsLapanganOlahraga();
        $param["jumlahLapangan"] = $lapa->count_all_data($role);
        $minta = new ModelsRequestPermintaan();
        $param["jumlahPermintaan"] = $minta->count_all_data_tempat($role);
        $tawar = new ModelsRequestPenawaran();
        $param["jumlahPenawaran"] = $tawar->count_all_data_pemilik($role);
        return view("tempat.beranda")->with($param);
    })->middleware([CekTempat::class]);
    Route::get("/cariAlat", function () {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        $alat = new ModelsAlatOlahraga();
        $param["alat"] = $alat->get_all_data2();
        $files = new filesAlatOlahraga();
        $param["files"] = $files;
        return view("tempat.cariAlat")->with($param);
    })->middleware([CekTempat::class]);
    Route::get("/searchAlat", [AlatOlahraga::class, "searchAlat"]);
    Route::get("/detailAlatUmum/{id}", function ($id) {//melihat detail alat olahraga milik org lain
        $alat = new ModelsAlatOlahraga();
        $param["alat"] = $alat->get_all_data_by_id($id);
        $files = new filesAlatOlahraga();
        $param["files"] = $files->get_all_data($id);
        $role = Session::get("dataRole")->id_tempat;
        $lapa = new ModelsLapanganOlahraga();
        $param["lapangan"] = $lapa->get_all_data_status($role);
        return view("tempat.detailAlatUmum")->with($param);
    })->middleware([CekTempat::class]);
    Route::post("/requestPermintaanAlat", [RequestPermintaan::class, "ajukanPermintaan"]);

    //Bagian lapangan olahraga
    Route::prefix("/lapangan")->group(function(){
        Route::get("/masterLapangan", function () {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            return view("tempat.lapangan.masterLapangan")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/tambahLapangan", [LapanganOlahraga::class, "tambahLapangan"]);
        Route::get("/daftarLapangan", function () {
            $role = Session::get("dataRole")->id_tempat;
            $files = new filesLapanganOlahraga();
            $lap = new ModelsLapanganOlahraga();
            $param["lapangan"] = $lap->get_all_data($role);
            $param["files"] = $files;
            return view("tempat.lapangan.daftarLapangan")->with($param);
        })->middleware([CekTempat::class]);
        Route::get("/lihatDetailLapangan/{id}", function ($id) {//melihat detail lapangan olahraga miliknya
            $lap = new ModelsLapanganOlahraga();
            $param["lapangan"] = $lap->get_all_data_by_id($id);
            $files = new filesLapanganOlahraga();
            $param["files"] = $files->get_all_data($id);
            $alat = new ModelsAlatOlahraga();
            $param["alat"] = $alat->get_all_data3(Session::get("dataRole")->id_tempat);
            $slot = new ModelsSlotWaktu();
            $param["slot"] = $slot->get_all_data_by_lapangan($id);

            $per = new ModelsRequestPermintaan();
            $param["permintaan"] = $per->get_all_data_by_lapangan($id);
            $pen = new ModelsRequestPenawaran();
            $param["penawaran"] = $pen->get_all_data_by_lapangan($id);

            $sewa = new ModelsSewaSendiri();
            $param["sewa"] = $sewa->get_all_data_by_lapangan($id);
            return view("tempat.lapangan.detailLapangan")->with($param);
        })->middleware([CekTempat::class]);
        Route::get("/editLapangan/{id}", function ($id) {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            $lap = new ModelsLapanganOlahraga();
            $param["lapangan"] = $lap->get_all_data_by_id($id);
            $files = new filesLapanganOlahraga();
            $param["files"] = $files->get_all_data($id);
            $slot = new ModelsSlotWaktu();
            $param["slot"] = $slot->get_all_data_by_lapangan($id);
            return view("tempat.lapangan.editLapangan")->with($param);
            // echo $id;
        })->middleware([CekTempat::class]);
        Route::post("/editLapangan", [LapanganOlahraga::class, "editLapangan"]);
    });

    //Bagian alat olahraga
    Route::prefix("/alat")->group(function(){
        Route::get("/masterAlat", function () {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            return view("tempat.alat.masterAlat")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/tambahAlat", [AlatOlahraga::class, "tambahAlat"]);
        Route::get("/daftarAlat", function () {
            $id = Session::get("dataRole")->id_tempat;
            $files = new filesAlatOlahraga();
            $alat = new ModelsAlatOlahraga();
            $param["alat"] = $alat->get_all_data($id, "Tempat");
            $param["files"] = $files;
            return view("tempat.alat.daftarAlat")->with($param);
        })->middleware([CekTempat::class]);
        Route::get("/lihatDetail/{id}", function ($id) {//melihat detail alat olahraga miliknya
            $alat = new ModelsAlatOlahraga();
            $param["alat"] = $alat->get_all_data_by_id($id);
            $files = new filesAlatOlahraga();
            $param["files"] = $files->get_all_data($id);
            return view("tempat.alat.detailAlat")->with($param);
        })->middleware([CekTempat::class]);
        Route::get("/editAlat/{id}", function ($id) {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            $alat = new ModelsAlatOlahraga();
            $param["alat"] = $alat->get_all_data_by_id($id);
            $files = new filesAlatOlahraga();
            $param["files"] = $files->get_all_data($id);
            return view("tempat.alat.editAlat")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/editAlat", [AlatOlahraga::class, "editAlat"]);
    });

    //Request permintaan
    Route::prefix("/permintaan")->group(function(){
        Route::post("/requestPermintaanAlat", [RequestPermintaan::class, "ajukanPermintaan"]);
        Route::get("/daftarPermintaan", function () {
            $role = Session::get("dataRole")->id_tempat;
            $req = new ModelsRequestPermintaan();
            $param["baru"] = $req->get_all_data_by_tempat_baru($role);
            $param["diterima"] = $req->get_all_data_by_tempat_diterima($role);
            $param["ditolak"] = $req->get_all_data_by_tempat_ditolak($role);
            $param["selesai"] = $req->get_all_data_by_tempat_selesai($role);
            $param["dibatalkan"] = $req->get_all_data_by_tempat_dibatalkan($role);
            $param["dikomplain"] = $req->get_all_data_by_tempat_dikomplain($role);
            return view("tempat.permintaan.daftarPermintaan")->with($param);
        })->middleware([CekTempat::class]);
        Route::get("/detailPermintaanNego/{id}", function ($id) {
            $role = Session::get("dataRole")->id_tempat;
            $req = new ModelsRequestPermintaan();
            $param["permintaan"] = $req->get_all_data_by_id($id);
            $nego = new ModelsNegosiasi();
            $param["nego"] = $nego->get_all_data_by_id_permintaan($id);
            $komplain = new ModelsKomplainRequest();
            $param["komplain"] = $komplain->get_all_data_by_id_req_tempat($id, "Permintaan", $role);
            return view("tempat.permintaan.detailPermintaanNego")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/batalPermintaan", [RequestPermintaan::class, "batalPermintaan"]);
        Route::post("/editHargaSewa", [RequestPermintaan::class, "editHargaSewa"]);

        //Bagian negosiasi
        Route::prefix("/negosiasi")->group(function(){
            Route::post("tambahNego", [Negosiasi::class, "tambahNegoPermintaan"]);
        });
    });

    //Request penawaran
    Route::prefix("/penawaran")->group(function(){
        Route::get("/daftarPenawaran", function () {
            $role = Session::get("dataRole")->id_tempat;
            $req = new ModelsRequestPenawaran();
            $param["baru"] = $req->get_all_data_by_tempat_baru($role);
            $param["diterima"] = $req->get_all_data_by_tempat_diterima($role);
            $param["ditolak"] = $req->get_all_data_by_tempat_ditolak($role);
            $param["selesai"] = $req->get_all_data_by_tempat_selesai($role);
            $param["dibatalkan"] = $req->get_all_data_by_tempat_dibatalkan($role);
            $param["dikomplain"] = $req->get_all_data_by_tempat_dikomplain($role);
            return view("tempat.penawaran.daftarPenawaran")->with($param);
        })->middleware([CekTempat::class]);
        Route::get("/detailPenawaranNego/{id}", function ($id) {
            $role = Session::get("dataRole")->id_tempat;
            $req = new ModelsRequestPenawaran();
            $param["penawaran"] = $req->get_all_data_by_id($id);
            $nego = new ModelsNegosiasi();
            $param["nego"] = $nego->get_all_data_by_id_penawaran($id);
            $komplain = new ModelsKomplainRequest();
            $param["komplain"] = $komplain->get_all_data_by_id_req_tempat($id, "Penawaran", $role);
            return view("tempat.penawaran.detailPenawaranNego")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/terimaPenawaran", [RequestPenawaran::class, "terimaPenawaran"]);
        Route::post("/tolakPenawaran", [RequestPenawaran::class, "tolakPenawaran"]);
        Route::post("/editHargaSewa", [RequestPenawaran::class, "editHargaSewa"]);
        Route::post("/editDurasi", [RequestPenawaran::class, "editDurasi"]);

        Route::prefix("/negosiasi")->group(function(){
            Route::post("tambahNego", [Negosiasi::class, "tambahNegoPenawaran"]);
        });
    });

    //Bagian komplain
    Route::prefix("/komplain")->group(function(){
        Route::post("tambahKomplain", [KomplainRequest::class, "tambahKomplain"]);
    });

    //Bagian sewa sendiri
    Route::prefix("/sewa")->group(function(){
        Route::post("/tambahSewaSendiri", [SewaSendiri::class, "tambahSewaSendiri"]);
        Route::post("/hapusSewaSendiri", [SewaSendiri::class, "hapusSewaSendiri"]);
    });

    //Bagian transaksi
    Route::prefix("/transaksi")->group(function(){
        Route::get("/daftarTransaksi", function () {
            $role = Session::get("dataRole")->id_tempat;
            $trans = new ModelsHtrans();
            $param["baru"] = $trans->get_all_data_by_tempat_baru($role);
            $param["diterima"] = $trans->get_all_data_by_tempat_diterima($role);
            $param["berlangsung"] = $trans->get_all_data_by_tempat_berlangsung($role);
            $param["ditolak"] = $trans->get_all_data_by_tempat_ditolak($role);
            $param["selesai"] = $trans->get_all_data_by_tempat_selesai($role);
            $param["dikomplain"] = $trans->get_all_data_by_tempat_dikomplain($role);
            $param["dibatalkan"] = $trans->get_all_data_by_tempat_dibatalkan($role);
            return view("tempat.transaksi.daftarTransaksi")->with($param);
        })->middleware([CekTempat::class]);
        Route::get("/detailTransaksi/{id}", function ($id) {
            $htrans = new ModelsHtrans();
            $param["htrans"] = $htrans->get_all_data_by_id($id);
            $dtrans = new ModelsDtrans();
            $param["dtrans"] = $dtrans->get_all_data_by_id_htrans($id);
            return view("tempat.transaksi.detailTransaksi")->with($param);
        })->middleware([CekTempat::class]);
    });
});

// -------------------------------
// HALAMAN CUSTOMER
// -------------------------------
Route::prefix("/customer")->group(function(){
    Route::view("/beranda", "customer.beranda");
});