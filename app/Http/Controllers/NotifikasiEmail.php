<?php

namespace App\Http\Controllers;

use App\Mail\notifEmail;
use App\Models\notifikasiEmail as ModelsNotifikasiEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotifikasiEmail extends Controller
{
    public function sendEmail() {
        $data = [
            "subject" => "Testing email",
            "nama_customer" => "Maria Yerossi",
            "tagihan" => 50000
        ];
        // Mail::to("maria.yerossi@gmail.com")->send(new notifEmail($data));
        $e = new ModelsNotifikasiEmail();
        $e->sendEmail("maria.yerossi@gmail.com",$data);
    }
}
