<?php

namespace App\Http\Controllers;

use App\Models\dtrans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Laporan extends Controller
{
    public function laporanPendapatanPemilik(Request $request){
        $role = Session::get("dataRole")->id_pemilik;
        $trans = new dtrans();
        $allData = $trans->get_all_data_by_pemilik($role);

        $monthlyIncome = [];
        for ($i=1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        foreach ($allData as $data) {
            $dataHtrans = DB::table('htrans')->where("id_htrans","=",$data->fk_id_htrans)->get()->first();

            $bulan = date('m', strtotime($dataHtrans->tanggal_trans));
            $monthlyIncome[(int)$bulan] += $data->total_komisi_pemilik;
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        $param["disewakan"] = $allData;
        $param["monthlyIncome"] = $monthlyIncomeData;
        return view("pemilik.laporan.laporanPendapatan")->with($param);
    }
}
