<?php

namespace App\Http\Controllers;

use App\Mail\notifEmail;
use App\Models\notifikasiEmail as ModelsNotifikasiEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotifikasiEmail extends Controller
{
    public function sendEmail() {
        $dataNotif = [
            "subject" => "Testing email",
            "judul" => "Penawaran Alat Olahraga Baru",
            "nama_user" => "Maria Yerossi",
            "isi" => "Anda memiliki 1 penawaran alat olahraga baru yang masih belum diterima.Silahkan terima penawaran!"
        ];
        // Mail::to("maria.yerossi@gmail.com")->send(new notifEmail($data));
        $e = new ModelsNotifikasiEmail();
        $e->sendEmail("maria.yerossi@gmail.com",$dataNotif);
    }
}
