<?php

use App\Http\Controllers\KategoriOlahraga;
use App\Http\Controllers\LoginRegister;
use App\Http\Middleware\CekAdmin;
use App\Http\Middleware\CekPemilik;
use App\Http\Middleware\Guest;
use App\Models\kategori;
use Illuminate\Support\Facades\Route;

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
Route::view("/masterAlat", "pemilik.masterAlat")->middleware([CekPemilik::class]);