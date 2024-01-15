<?php

namespace App\Http\Controllers;

use App\Models\notifikasi as ModelsNotifikasi;
use Illuminate\Http\Request;

class Notifikasi extends Controller
{
    public function editStatusDibaca($id, Request $request) {
        $data = [
            "id" => $id,
            "status" => "Dibaca"
        ];
        $notif = new ModelsNotifikasi();
        $notif->updateStatus($data);

        $tujuan = $request->input('tujuan');

        return response()->json(['success' => true, 'redirect' => $tujuan]);
    }
}
