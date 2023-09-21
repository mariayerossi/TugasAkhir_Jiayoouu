<?php

namespace App\Http\Controllers;

use App\Models\dtrans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;

use PDF;

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

    public function fiturPendapatan(Request $request){
        $role = Session::get("dataRole")->id_pemilik;
        $trans = new dtrans();

        $monthlyIncome = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        if ($request->has('monthFilter') && $request->monthFilter != "" && $request->has('yearFilter') && $request->yearFilter != "") {
            $month = $request->input('monthFilter');
            $year = $request->input('yearFilter');

            // Query berdasarkan bulan dan tahun yang dipilih
            $allData = DB::table('dtrans')
                ->join('htrans', 'dtrans.fk_id_htrans', '=', 'htrans.id_htrans')
                ->whereMonth('htrans.tanggal_sewa', '=', $month)
                ->whereYear('htrans.tanggal_sewa', '=', $year)
                ->where('dtrans.fk_id_pemilik', '=', $role)
                ->where('dtrans.fk_role_pemilik', '=', "Pemilik")
                ->get();
            
            foreach ($allData as $data) {
                $dataHtrans = DB::table('htrans')->where("id_htrans", "=", $data->fk_id_htrans)->get()->first();

                $bulan = date('m', strtotime($dataHtrans->tanggal_sewa));
                $monthlyIncome[(int)$bulan] += $data->total_komisi_pemilik;
            }

        }
        else if ($request->has('monthFilter') && $request->monthFilter != "") {
            $month = $request->input('monthFilter');

            // Query berdasarkan bulan dan tahun yang dipilih
            $allData = DB::table('dtrans')
                ->join('htrans', 'dtrans.fk_id_htrans', '=', 'htrans.id_htrans')
                ->whereMonth('htrans.tanggal_sewa', '=', $month)
                ->where('dtrans.fk_id_pemilik', '=', $role)
                ->where('dtrans.fk_role_pemilik', '=', "Pemilik")
                ->get();
            
            foreach ($allData as $data) {
                $dataHtrans = DB::table('htrans')->where("id_htrans", "=", $data->fk_id_htrans)->get()->first();

                $bulan = date('m', strtotime($dataHtrans->tanggal_sewa));
                $monthlyIncome[(int)$bulan] += $data->total_komisi_pemilik;
            }

        }
        else if ($request->has('yearFilter') && $request->yearFilter != "") {
            $year = $request->input('yearFilter');

            // Query berdasarkan bulan dan tahun yang dipilih
            $allData = DB::table('dtrans')
                ->join('htrans', 'dtrans.fk_id_htrans', '=', 'htrans.id_htrans')
                ->whereYear('htrans.tanggal_sewa', '=', $year)
                ->where('dtrans.fk_id_pemilik', '=', $role)
                ->where('dtrans.fk_role_pemilik', '=', "Pemilik")
                ->get();
            
            foreach ($allData as $data) {
                $dataHtrans = DB::table('htrans')->where("id_htrans", "=", $data->fk_id_htrans)->get()->first();

                $bulan = date('m', strtotime($dataHtrans->tanggal_sewa));
                $monthlyIncome[(int)$bulan] += $data->total_komisi_pemilik;
            }

        } 
        else {
            $allData = $trans->get_all_data_by_pemilik($role);
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

    public function cetakPDF(){
    	$data = dtrans::all();
 
    	$pdf = PDF::loadview('laporan_pdf',['data'=>$data]);
    	return $pdf->download('laporan-pendapatan-pdf');
    }
}
