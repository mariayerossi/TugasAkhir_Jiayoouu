<?php

use App\Http\Controllers\LoginRegister;
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
Route::get('/register', function () {
    return view('register');
});
Route::get('/login', function () {
    return view('login');
})->middleware([Guest::class]);
Route::get('/registerTempat', function () {
    return view('tempat/registerTempat');
});
Route::get('/registerPemilik', function () {
    return view('pemilik/registerPemilik');
});

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
Route::get('/beranda', function () {
    return view('admin/beranda');
});
Route::get('/registrasi_tempat', function () {
    return view('admin/registrasi_tempat');
});