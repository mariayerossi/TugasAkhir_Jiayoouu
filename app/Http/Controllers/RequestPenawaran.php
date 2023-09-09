<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestPenawaran extends Controller
{
    public function ajukanPenawaran(Request $request) {
        $request->validate([
            "alat" => "required"
        ],[
            "required" => "alat olahraga tidak boleh kosong!"
        ]);
    }
}
