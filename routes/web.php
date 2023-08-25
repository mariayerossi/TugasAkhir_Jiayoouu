<?php

use App\Http\Controllers\LoginRegister;
use App\Http\Middleware\CekAdmin;
use App\Http\Middleware\CekPemilik;
use App\Http\Middleware\Guest;
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
Route::view("/register", "register");
Route::view("/login", "login")->middleware([Guest::class]);
Route::view("/registerTempat", "tempat.registerTempat");
Route::view("/registerPemilik", "pemilik.registerPemilik");

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

// -------------------------------
// HALAMAN PEMILIK ALAT
// -------------------------------
Route::view("/masterAlat", "pemilik.masterAlat");