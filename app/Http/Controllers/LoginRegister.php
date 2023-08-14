<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginRegister extends Controller
{
    //Register User
    public function registerUser(Request $request){
        $request->validate([
            "nama" => 'required|min:5|string',
        ], [
            "required" => ":attribute lengkap tidak boleh kosong!",
            "min" => ":attribute lengkap tidak valid!"
        ]);
        // return redirect()->back()->with("success", "Berhasil Register!");
    }

    // Register Pemilik

    //Register Tempat
}
