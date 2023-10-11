<?php

namespace App\Http\Controllers;

use App\Models\kerusakanAlat as ModelsKerusakanAlat;
use Illuminate\Http\Request;

class KerusakanAlat extends Controller
{
    public function ajukanKerusakan(Request $request) {
        // $data = $request->all();
        
        // $totalForms = count($request->input('id_dtrans'));
        // dd($request->input('id_dtrans', []));

        // $data = [];
        // for ($i = 0; $i < $totalForms; $i++) {
        //     // Mengecek apakah unsur kesengajaan dan foto telah diberikan
        //     $unsur = $request->input('unsur' . $i);
        //     $foto = $request->file('foto' . $i);
            
        //     if (!is_null($unsur) && !is_null($foto)) {

        //         // Contoh: menyimpan ke database
        //         // $dtrans = DTrans::find($request->input('id_dtrans.' . $i)); // Sesuaikan dengan model Anda

        //         // Simpan foto
        //         // $fotoPath = $foto->store('bukti_kerusakan', 'public');
                
        //         // // Update data dtrans
        //         // $dtrans->unsur_kesengajaan = $unsur;
        //         // $dtrans->foto_bukti = $fotoPath;
        //         // $dtrans->save();
        //         array_push($data,[
        //             "unsur" => $unsur
        //         ]);
        //     }
        // }
        // dd($data);


        // Mengambil semua data dari request
        $allIdDtrans = $request->input('id_dtrans', []);
        $allData = $request->all();

        foreach ($allIdDtrans as $index => $idDtransValue) {
            if(isset($allData['unsur' . $index + 1])) {
                $unsurValue = $allData['unsur' . $index + 1];
                dd($unsurValue);

                // Proses upload foto
                $fotoName = null;
                if ($request->hasFile('foto' . $index + 1)) {
                    $foto = $request->file('foto' . $index + 1);
                    $destinasi = "/upload";
                    $fotoName = uniqid() . "." . $foto->getClientOriginalExtension();
                    $foto->move(public_path($destinasi), $fotoName);
                }

                $data = [
                    "id_dtrans" => $idDtransValue,
                    "unsur" => $unsurValue,
                    "foto" => $fotoName
                ];

                $ker = new ModelsKerusakanAlat();
                $ker->insertKerusakanAlat($data);
            } else {
                // Kode untuk menangani form yang tidak lengkap, misalnya kembalikan dengan pesan kesalahan
            }
        }
    }
}
