<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function hapusSessionMsg() {
        
        if (session()->has("success")) {
            session()->forget("success");
        }
    
        if (session()->has("error")) {
            session()->forget("success");
        }

        return response()->json(['success' => true]);
    }
}
