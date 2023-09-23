<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\dtrans;
use App\Models\htrans;
use App\Models\lapanganOlahraga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;

use PDF;

class Laporan extends Controller
{
    //PEMILIK ALAT
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
            $year = date('Y', strtotime($dataHtrans->tanggal_trans));
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += $data->total_komisi_pemilik;
            }
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

    public function fiturPendapatanPemilik(Request $request){
        $request->validate([
            "tanggal_mulai" => 'required',
            "tanggal_selesai" => 'required'
        ],[
            "tanggal_mulai.required" => "tanggal mulai tidak boleh kosong!",
            "tanggal_selesai.required" => "tanggal selesai tidak boleh kosong!"
        ]);
        $role = Session::get("dataRole")->id_pemilik;
        $trans = new dtrans();

        $monthlyIncome = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        // if ($request->has('monthFilter') && $request->monthFilter != "" && $request->has('yearFilter') && $request->yearFilter != "") {
        //     $month = $request->input('monthFilter');
        //     $year = $request->input('yearFilter');

        //     // Query berdasarkan bulan dan tahun yang dipilih
        //     $allData = DB::table('dtrans')
        //         ->join('htrans', 'dtrans.fk_id_htrans', '=', 'htrans.id_htrans')
        //         ->whereMonth('htrans.tanggal_sewa', '=', $month)
        //         ->whereYear('htrans.tanggal_sewa', '=', $year)
        //         ->where('dtrans.fk_id_pemilik', '=', $role)
        //         ->where('dtrans.fk_role_pemilik', '=', "Pemilik")
        //         ->get();
            
        //     foreach ($allData as $data) {
        //         $dataHtrans = DB::table('htrans')->where("id_htrans", "=", $data->fk_id_htrans)->get()->first();

        //         $bulan = date('m', strtotime($dataHtrans->tanggal_sewa));
        //         $monthlyIncome[(int)$bulan] += $data->total_komisi_pemilik;
        //     }

        // }
        // else if ($request->has('monthFilter') && $request->monthFilter != "") {
        //     $month = $request->input('monthFilter');

        //     // Query berdasarkan bulan dan tahun yang dipilih
        //     $allData = DB::table('dtrans')
        //         ->join('htrans', 'dtrans.fk_id_htrans', '=', 'htrans.id_htrans')
        //         ->whereMonth('htrans.tanggal_sewa', '=', $month)
        //         ->where('dtrans.fk_id_pemilik', '=', $role)
        //         ->where('dtrans.fk_role_pemilik', '=', "Pemilik")
        //         ->get();
            
        //     foreach ($allData as $data) {
        //         $dataHtrans = DB::table('htrans')->where("id_htrans", "=", $data->fk_id_htrans)->get()->first();

        //         $bulan = date('m', strtotime($dataHtrans->tanggal_sewa));
        //         $monthlyIncome[(int)$bulan] += $data->total_komisi_pemilik;
        //     }

        // }
        // else if ($request->has('yearFilter') && $request->yearFilter != "") {
        //     $year = $request->input('yearFilter');

        //     // Query berdasarkan bulan dan tahun yang dipilih
        //     $allData = DB::table('dtrans')
        //         ->join('htrans', 'dtrans.fk_id_htrans', '=', 'htrans.id_htrans')
        //         ->whereYear('htrans.tanggal_sewa', '=', $year)
        //         ->where('dtrans.fk_id_pemilik', '=', $role)
        //         ->where('dtrans.fk_role_pemilik', '=', "Pemilik")
        //         ->get();
            
        //     foreach ($allData as $data) {
        //         $dataHtrans = DB::table('htrans')->where("id_htrans", "=", $data->fk_id_htrans)->get()->first();

        //         $bulan = date('m', strtotime($dataHtrans->tanggal_sewa));
        //         $monthlyIncome[(int)$bulan] += $data->total_komisi_pemilik;
        //     }

        // } 
        // else {
        //     $allData = $trans->get_all_data_by_pemilik($role);
        // }

        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_selesai');

        // Query berdasarkan rentang tanggal yang dipilih
        $allData = DB::table('dtrans')
            ->join('htrans', 'dtrans.fk_id_htrans', '=', 'htrans.id_htrans')
            ->whereBetween('htrans.tanggal_sewa', [$startDate, $endDate])
            ->where('dtrans.fk_id_pemilik', '=', $role)
            ->where('dtrans.fk_role_pemilik', '=', "Pemilik")
            ->get();
        
        foreach ($allData as $data) {
            $dataHtrans = DB::table('htrans')->where("id_htrans", "=", $data->fk_id_htrans)->get()->first();
            $bulan = date('m', strtotime($dataHtrans->tanggal_sewa));
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

    public function pendapatanPemilikCetakPDF(){
    	$role = Session::get("dataRole")->id_pemilik;
        $trans = new dtrans();
        $data = $trans->get_all_data_by_pemilik($role);
 
    	$pdf = PDF::loadview('pemilik.laporan.laporanPendapatan_pdf',['data'=>$data]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanStokPemilik() {
        $role = Session::get("dataRole")->id_pemilik;
        $alat = new alatOlahraga();
        $allData = $alat->get_all_data($role, "Pemilik");

        $param["alat"] = $allData;
        return view("pemilik.laporan.laporanStok")->with($param);
    }

    public function stokPemilikCetakPDF(){
    	$role = Session::get("dataRole")->id_pemilik;
        $alat = new alatOlahraga();
        $data = $alat->get_all_data($role, "Pemilik");
 
    	$pdf = PDF::loadview('pemilik.laporan.laporanStok_pdf',['data'=>$data]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanDisewakanPemilik() {
        $role = Session::get("dataRole")->id_pemilik;
        $dtrans = new dtrans();
        $allData = $dtrans->get_all_data_by_pemilik($role);

        $monthlyIncome = [];
        // $yearlyMonthlyIncome = [];
        // $currentYear = date('Y'); // Ambil tahun saat ini

        // for ($year = $currentYear -1; $year <= $currentYear; $year++) {
        //     for ($i = 1; $i <= 12; $i++) {
        //         $yearlyMonthlyIncome[$year][$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        //     }
        // }

        // foreach ($allData as $data) {
        //     $dataHtrans = DB::table('htrans')->where("id_htrans", "=", $data->fk_id_htrans)->get();
        //     $year = date('Y', strtotime($dataHtrans->first()->tanggal_sewa));
        //     $bulan = date('m', strtotime($dataHtrans->first()->tanggal_sewa));
        
        //     $yearlyMonthlyIncome[$year][(int)$bulan] += $dataHtrans->count();
        // }

        for ($i = 1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        // foreach ($allData as $data) {
        //     $dataHtrans = DB::table('htrans')->where("id_htrans","=",$data->fk_id_htrans)->get();

        //     $bulan = date('m', strtotime($dataHtrans->first()->tanggal_sewa));
        //     $monthlyIncome[(int)$bulan] += $dataHtrans->count();
        // }

        foreach ($allData as $data) {
            $dataHtrans = DB::table('htrans')->where("id_htrans","=",$data->fk_id_htrans)->get();
            $year = date('Y', strtotime($dataHtrans->first()->tanggal_sewa));
            $bulan = date('m', strtotime($dataHtrans->first()->tanggal_sewa));
    
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += $dataHtrans->count();
            }
            
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        // $monthlyIncomeData = array_values($monthlyIncome);

        $param["disewakan"] = $allData;
        $param["monthlyIncome"] = $monthlyIncomeData;
        // $param["yearlyMonthlyIncome"] = $yearlyMonthlyIncome;
        return view("pemilik.laporan.laporanDisewakan")->with($param);
    }

    public function disewakanPemilikCetakPDF(){
    	$role = Session::get("dataRole")->id_pemilik;
        $dtrans = new dtrans();
        $data = $dtrans->get_all_data_by_pemilik($role);
 
    	$pdf = PDF::loadview('pemilik.laporan.laporanDisewakan_pdf',['data'=>$data]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanTempatPemilik() {
        //tampilkan tempat olahraga yang kerjasama sm pemilik dan total komisinya
        $role = Session::get("dataRole")->id_pemilik;
        $allData = DB::table('pihak_tempat')
            ->select('pihak_tempat.nama_tempat', DB::raw('SUM(dtrans.total_komisi_pemilik) as total_komisi'), DB::raw('COUNT(dtrans.fk_id_alat) as jumlah'))
            ->join('htrans', 'pihak_tempat.id_tempat', '=', 'htrans.fk_id_tempat')
            ->join('dtrans', 'htrans.id_htrans', '=', 'dtrans.fk_id_htrans')
            ->where('dtrans.fk_id_pemilik', '=', $role)
            ->where('dtrans.fk_role_pemilik', '=', 'Pemilik')
            ->groupBy('pihak_tempat.id_tempat', 'pihak_tempat.nama_tempat')  // tambahkan 'pihak_tempat.nama_tempat' ke GROUP BY
            ->get();

        $param["tempat"] = $allData;
        return view("pemilik.laporan.laporanTempat")->with($param);
    }

    public function tempatPemilikCetakPDF() {
        $role = Session::get("dataRole")->id_pemilik;
        $data = DB::table('pihak_tempat')
            ->select('pihak_tempat.nama_tempat', DB::raw('SUM(dtrans.total_komisi_pemilik) as total_komisi'), DB::raw('COUNT(dtrans.fk_id_alat) as jumlah'))
            ->join('htrans', 'pihak_tempat.id_tempat', '=', 'htrans.fk_id_tempat')
            ->join('dtrans', 'htrans.id_htrans', '=', 'dtrans.fk_id_htrans')
            ->where('dtrans.fk_id_pemilik', '=', $role)
            ->where('dtrans.fk_role_pemilik', '=', 'Pemilik')
            ->groupBy('pihak_tempat.id_tempat', 'pihak_tempat.nama_tempat')  // tambahkan 'pihak_tempat.nama_tempat' ke GROUP BY
            ->get();
 
    	$pdf = PDF::loadview('pemilik.laporan.laporanTempat_pdf',['data'=>$data]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }
}
