<?php

namespace App\Models;

use App\Mail\notifEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class notifikasiEmail extends Model
{
    use HasFactory;
    function sendEmail($email, $data) {
        Mail::to($email)->send(new notifEmail($data));
    }
}
