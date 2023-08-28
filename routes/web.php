<?php

use App\Http\Controllers\AlatOlahraga;
use App\Http\Controllers\KategoriOlahraga;
use App\Http\Controllers\LoginRegister;
use App\Http\Middleware\CekAdmin;
use App\Http\Middleware\CekPemilik;
use App\Http\Middleware\Guest;
use App\Models\alatOlahraga as ModelsAlatOlahraga;
use App\Models\filesAlatOlahraga;
use App\Models\kategori;
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
    //ngambil data alat olahraga (blm termasuk file)
    $role = Session::get("dataRole")->id_pemilik;
    $files = new filesAlatOlahraga();
    $alat = new ModelsAlatOlahraga();
    $param["alat"] = $alat->get_all_data($role);
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