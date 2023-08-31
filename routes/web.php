<?php

use App\Http\Controllers\AlatOlahraga;
use App\Http\Controllers\KategoriOlahraga;
use App\Http\Controllers\LapanganOlahraga;
use App\Http\Controllers\LoginRegister;
use App\Http\Middleware\CekAdmin;
use App\Http\Middleware\CekPemilik;
use App\Http\Middleware\CekTempat;
use App\Http\Middleware\Guest;
use App\Models\alatOlahraga as ModelsAlatOlahraga;
use App\Models\filesAlatOlahraga;
use App\Models\filesLapanganOlahraga;
use App\Models\kategori;
use App\Models\lapanganOlahraga as ModelsLapanganOlahraga;
use Illuminate\Http\Request;
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
Route::view("/beranda", "admin.beranda")->middleware([CekAdmin::class]);
Route::view("/registrasi_tempat", "admin.registrasi_tempat")->middleware([CekAdmin::class]);
Route::get("/konfirmasiTempat/{id}", [LoginRegister::class, "konfirmasiTempat"]);
Route::get("/masterKategori", function () {
    $kat = new kategori();
    $param["kategori"] = $kat->get_all_data();
    return view("admin.masterKategori")->with($param);
})->middleware([CekAdmin::class]);
Route::post("/tambahKategori", [KategoriOlahraga::class, "tambahKategori"]);
Route::get("/hapusKategori/{id}", [KategoriOlahraga::class, "hapusKategori"]);

// -------------------------------
// HALAMAN PEMILIK ALAT
// -------------------------------
Route::get("/masterAlatdiPemilik", function () {
    $kat = new kategori();
    $param["kategori"] = $kat->get_all_data();
    return view("pemilik.masterAlat")->with($param);
})->middleware([CekPemilik::class]);
Route::post("/tambahAlatdiPemilik", [AlatOlahraga::class, "tambahAlat"]);
Route::get("/daftarAlatdiPemilik", function () {
    $id = Session::get("dataRole")->id_pemilik;
    $files = new filesAlatOlahraga();
    $alat = new ModelsAlatOlahraga();
    $param["alat"] = $alat->get_all_data($id, "Pemilik");
    $param["files"] = $files;
    return view("pemilik.daftarAlat")->with($param);
})->middleware([CekPemilik::class]);
Route::get("/lihatDetaildiPemilik/{id}", function ($id) {
    $alat = new ModelsAlatOlahraga();
    $param["alat"] = $alat->get_all_data_by_id($id);
    $files = new filesAlatOlahraga();
    $param["files"] = $files->get_all_data($id);
    return view("pemilik.detailAlat")->with($param);
    // echo $id;
})->middleware([CekPemilik::class]);
Route::get("/editAlatdiPemilik/{id}", function ($id) {
    $kat = new kategori();
    $param["kategori"] = $kat->get_all_data();
    $alat = new ModelsAlatOlahraga();
    $param["alat"] = $alat->get_all_data_by_id($id);
    $files = new filesAlatOlahraga();
    $param["files"] = $files->get_all_data($id);
    return view("pemilik.editAlat")->with($param);
    // echo $id;
})->middleware([CekPemilik::class]);
Route::get("/berandaPemilik", function () {
    $id = Session::get("dataRole")->id_pemilik;
    $alat = new ModelsAlatOlahraga();
    $param["jumlahAlat"] = $alat->count_all_data($id, "Pemilik");
    return view("pemilik.beranda")->with($param);
})->middleware([CekPemilik::class]);
Route::post("/editAlatdiPemilik", [AlatOlahraga::class, "editAlat"]);

// -------------------------------
// HALAMAN PIHAK TEMPAT
// -------------------------------
Route::get("/berandaTempat", function () {
    $role = Session::get("dataRole")->id_tempat;
    $lapa = new ModelsLapanganOlahraga();
    $param["jumlahLapangan"] = $lapa->count_all_data($role);
    return view("tempat.beranda")->with($param);
})->middleware([CekTempat::class]);
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
Route::get("/lihatDetailLapangan/{id}", function ($id) {
    $lap = new ModelsLapanganOlahraga();
    $param["lapangan"] = $lap->get_all_data_by_id($id);
    $files = new filesLapanganOlahraga();
    $param["files"] = $files->get_all_data($id);
    return view("tempat.lapangan.detailLapangan")->with($param);
    // echo $id;
})->middleware([CekTempat::class]);
Route::get("/editLapangan/{id}", function ($id) {
    $kat = new kategori();
    $param["kategori"] = $kat->get_all_data();
    $lap = new ModelsLapanganOlahraga();
    $param["lapangan"] = $lap->get_all_data_by_id($id);
    $files = new filesLapanganOlahraga();
    $param["files"] = $files->get_all_data($id);
    return view("tempat.lapangan.editLapangan")->with($param);
    // echo $id;
})->middleware([CekTempat::class]);
Route::post("/editLapangan", [LapanganOlahraga::class, "editLapangan"]);

//Master alat di tempat olahraga
Route::get("/masterAlatdiTempat", function () {
    $kat = new kategori();
    $param["kategori"] = $kat->get_all_data();
    return view("tempat.alat.masterAlat")->with($param);
})->middleware([CekTempat::class]);
Route::post("/tambahAlatdiTempat", [AlatOlahraga::class, "tambahAlat"]);
Route::get("/daftarAlatdiTempat", function () {
    $id = Session::get("dataRole")->id_tempat;
    $files = new filesAlatOlahraga();
    $alat = new ModelsAlatOlahraga();
    $param["alat"] = $alat->get_all_data($id, "Tempat");
    $param["files"] = $files;
    return view("tempat.alat.daftarAlat")->with($param);
})->middleware([CekTempat::class]);
Route::get("/lihatDetaildiTempat/{id}", function ($id) {
    $alat = new ModelsAlatOlahraga();
    $param["alat"] = $alat->get_all_data_by_id($id);
    $files = new filesAlatOlahraga();
    $param["files"] = $files->get_all_data($id);
    return view("tempat.alat.detailAlat")->with($param);
})->middleware([CekTempat::class]);
Route::get("/editAlatdiTempat/{id}", function ($id) {
    $kat = new kategori();
    $param["kategori"] = $kat->get_all_data();
    $alat = new ModelsAlatOlahraga();
    $param["alat"] = $alat->get_all_data_by_id($id);
    $files = new filesAlatOlahraga();
    $param["files"] = $files->get_all_data($id);
    return view("tempat.alat.editAlat")->with($param);
})->middleware([CekTempat::class]);
Route::post("/editAlatdiTempat", [AlatOlahraga::class, "editAlat"]);
Route::get("/cariAlat", function () {
    $kat = new kategori();
    $param["kategori"] = $kat->get_all_data();
    $alat = new ModelsAlatOlahraga();
    $param["alat"] = $alat->get_all_data2();
    $files = new filesAlatOlahraga();
    $param["files"] = $files;
    return view("tempat.cariAlat")->with($param);
})->middleware([CekTempat::class]);