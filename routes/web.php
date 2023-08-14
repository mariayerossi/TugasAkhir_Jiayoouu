<?php

use App\Http\Controllers\LoginRegister;
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


Route::get('/register', function () {
    return view('register');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/registerTempat', function () {
    return view('tempat/registerTempat');
});

Route::get('/registerPemilik', function () {
    return view('pemilik/registerPemilik');
});

Route::post("/registerUser", [LoginRegister::class, "registerUser"]);
Route::post("/registerPemilik", [LoginRegister::class, "registerPemilik"]);
