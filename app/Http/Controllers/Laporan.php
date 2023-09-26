<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\dtrans;
use App\Models\htrans;
use App\Models\lapanganOlahraga;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;

use PDF;
use Termwind\Components\Raw;

class Laporan extends Controller
{
    //PEMILIK ALAT
    public function laporanPendapatanPemilik(){
        $role = Session::get("dataRole")->id_pemilik;
        $trans = new dtrans();
        $allData = $trans->get_all_data_by_pemilik($role);
        $coba = DB::table('htrans')
                ->select("alat_olahraga.nama_alat","alat_olahraga.komisi_alat","htrans.durasi_sewa","htrans.tanggal_trans","dtrans.total_komisi_pemilik")
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->where("dtrans.fk_id_pemilik","=",$role)
                ->where("dtrans.fk_role_pemilik","=","Pemilik")
                ->get();
        // dd($coba);

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

        $param["disewakan"] = $coba;
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

        $date_mulai = new DateTime($startDate);
        $date_selesai = new DateTime($endDate);
        
        if ($date_selesai <= $date_mulai) {
            return redirect()->back()->with("error", "Tanggal selesai tidak sesuai!");
        }

        // Query berdasarkan rentang tanggal yang dipilih
        $trans = new dtrans();
        $allData = $trans->get_all_data_by_pemilik($role);
        $coba = DB::table('htrans')
            ->select("alat_olahraga.nama_alat","alat_olahraga.komisi_alat","htrans.durasi_sewa","htrans.tanggal_trans","dtrans.total_komisi_pemilik")
            ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
            ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
            ->where("dtrans.fk_id_pemilik","=",$role)
            ->where("dtrans.fk_role_pemilik","=","Pemilik")
            ->whereBetween('htrans.tanggal_trans', [$startDate, $endDate])
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

        $param["disewakan"] = $coba;
        $param["monthlyIncome"] = $monthlyIncomeData;
        return view("pemilik.laporan.laporanPendapatan")->with($param);
    }

    public function pendapatanPemilikCetakPDF(){
    	$role = Session::get("dataRole")->id_pemilik;
        $data = DB::table('htrans')
                ->select("alat_olahraga.nama_alat","alat_olahraga.komisi_alat","htrans.durasi_sewa","htrans.tanggal_trans","dtrans.total_komisi_pemilik")
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->where("dtrans.fk_id_pemilik","=",$role)
                ->where("dtrans.fk_role_pemilik","=","Pemilik")
                ->get();
 
    	$pdf = PDF::loadview('pemilik.laporan.laporanPendapatan_pdf',['data'=>$data]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanStokPemilik() {
        $role = Session::get("dataRole")->id_pemilik;
        // $alat = new alatOlahraga();
        // $allData = $alat->get_all_data($role, "Pemilik");
        $allData = DB::table('alat_olahraga')
                    ->select("files_alat.nama_file_alat","alat_olahraga.nama_alat", DB::raw('count(dtrans.id_dtrans) as totalRequest'),"alat_olahraga.kategori_alat", "alat_olahraga.komisi_alat","alat_olahraga.status_alat","alat_olahraga.created_at")
                    ->joinSub(function($query) {
                        $query->select("fk_id_alat", "nama_file_alat")
                            ->from('files_alat')
                            ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                    }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                    ->leftJoin("dtrans","alat_olahraga.id_alat","=","dtrans.fk_id_alat")
                    ->where("alat_olahraga.pemilik_alat","=",$role)
                    ->where("alat_olahraga.role_pemilik_alat","=","Pemilik")
                    ->groupBy("alat_olahraga.nama_alat", "alat_olahraga.kategori_alat", "alat_olahraga.komisi_alat","alat_olahraga.status_alat","alat_olahraga.created_at")
                    ->get();

        // dd($allData);

        $param["alat"] = $allData;
        return view("pemilik.laporan.laporanStok")->with($param);
    }

    public function stokPemilikCetakPDF(){
    	$role = Session::get("dataRole")->id_pemilik;
        $data = DB::table('alat_olahraga')
                    ->select("files_alat.nama_file_alat","alat_olahraga.nama_alat", DB::raw('count(dtrans.id_dtrans) as totalRequest'),"alat_olahraga.kategori_alat", "alat_olahraga.komisi_alat","alat_olahraga.status_alat","alat_olahraga.created_at")
                    ->joinSub(function($query) {
                        $query->select("fk_id_alat", "nama_file_alat")
                            ->from('files_alat')
                            ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                    }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                    ->leftJoin("dtrans","alat_olahraga.id_alat","=","dtrans.fk_id_alat")
                    ->where("alat_olahraga.pemilik_alat","=",$role)
                    ->where("alat_olahraga.role_pemilik_alat","=","Pemilik")
                    ->groupBy("alat_olahraga.nama_alat", "alat_olahraga.kategori_alat", "alat_olahraga.komisi_alat","alat_olahraga.status_alat","alat_olahraga.created_at")
                    ->get();
 
    	$pdf = PDF::loadview('pemilik.laporan.laporanStok_pdf',['data'=>$data]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanDisewakanPemilik() {
        $role = Session::get("dataRole")->id_pemilik;
        $dtrans = new dtrans();
        $allData = $dtrans->get_all_data_by_pemilik($role);
        $coba = DB::table('dtrans')
                ->select("htrans.tanggal_sewa","alat_olahraga.nama_alat","dtrans.harga_sewa_alat","htrans.durasi_sewa","dtrans.subtotal_alat")
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->rightJoin("htrans", "dtrans.fk_id_htrans","=","htrans.id_htrans")
                ->where("dtrans.fk_id_pemilik","=",$role)
                ->where("dtrans.fk_role_pemilik","=","Pemilik")
                ->get();
        // dd($coba);

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

        $param["disewakan"] = $coba;
        $param["monthlyIncome"] = $monthlyIncomeData;
        // $param["yearlyMonthlyIncome"] = $yearlyMonthlyIncome;
        return view("pemilik.laporan.laporanDisewakan")->with($param);
    }

    public function disewakanPemilikCetakPDF(){
    	$role = Session::get("dataRole")->id_pemilik;
        $data = DB::table('dtrans')
                ->select("htrans.tanggal_sewa","alat_olahraga.nama_alat","dtrans.harga_sewa_alat","htrans.durasi_sewa","dtrans.subtotal_alat")
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->rightJoin("htrans", "dtrans.fk_id_htrans","=","htrans.id_htrans")
                ->where("dtrans.fk_id_pemilik","=",$role)
                ->where("dtrans.fk_role_pemilik","=","Pemilik")
                ->get();
 
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

    //PIHAK TEMPAT OLAHRAGA
    public function laporanPendapatanTempat(){
        $role = Session::get("dataRole")->id_tempat;
        $trans = new htrans();
        $allData = $trans->get_all_data_by_tempat($role);
        $coba = DB::table('htrans')
            ->select(
                'htrans.id_htrans',
                "htrans.kode_trans",
                DB::raw('SUM(dtrans.total_komisi_tempat) as total_komisi'),
                'htrans.subtotal_lapangan',
                'htrans.tanggal_trans',
                DB::raw('COUNT(dtrans.id_dtrans) as alat'),
                "lapangan_olahraga.nama_lapangan"
            )
            ->leftJoin("dtrans", "htrans.id_htrans", "=", "dtrans.fk_id_htrans")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->where("htrans.fk_id_tempat", "=", $role)
            ->groupBy(
                'htrans.id_htrans',
                'htrans.kode_trans',
                'htrans.subtotal_lapangan',
                'htrans.tanggal_trans',
                "lapangan_olahraga.nama_lapangan"
            )
            ->get();

        $monthlyIncome = [];
        for ($i=1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        foreach ($allData as $data) {
            $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$data->id_htrans)->sum("total_komisi_tempat");
            // dd($dataDtrans);
            $bulan = date('m', strtotime($data->tanggal_trans));
            $year = date('Y', strtotime($data->tanggal_trans));
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += $data->subtotal_lapangan+$dataDtrans;
            }
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        $param["trans"] = $coba;
        $param["monthlyIncome"] = $monthlyIncomeData;
        return view("tempat.laporan.laporanPendapatan")->with($param);
    }

    public function fiturPendapatanTempat(Request $request) {
        $request->validate([
            "tanggal_mulai" => 'required',
            "tanggal_selesai" => 'required'
        ],[
            "tanggal_mulai.required" => "tanggal mulai tidak boleh kosong!",
            "tanggal_selesai.required" => "tanggal selesai tidak boleh kosong!"
        ]);
        $role = Session::get("dataRole")->id_tempat;

        $monthlyIncome = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_selesai');

        $date_mulai = new DateTime($startDate);
        $date_selesai = new DateTime($endDate);
        
        if ($date_selesai <= $date_mulai) {
            return redirect()->back()->with("error", "Tanggal selesai tidak sesuai!");
        }
        
        $allData = DB::table('htrans')
                    ->select(
                        'htrans.id_htrans',
                        "htrans.kode_trans",
                        DB::raw('SUM(dtrans.total_komisi_tempat) as total_komisi'),
                        'htrans.subtotal_lapangan',
                        'htrans.tanggal_trans',
                        DB::raw('COUNT(dtrans.id_dtrans) as alat'),
                        "lapangan_olahraga.nama_lapangan"
                    )
                    ->leftJoin("dtrans", "htrans.id_htrans", "=", "dtrans.fk_id_htrans")
                    ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
                    ->whereBetween('htrans.tanggal_trans', [$startDate, $endDate])
                    ->where("htrans.fk_id_tempat", "=", $role)
                    ->groupBy(
                        'htrans.id_htrans',
                        'htrans.kode_trans',
                        'htrans.subtotal_lapangan',
                        'htrans.tanggal_trans',
                        "lapangan_olahraga.nama_lapangan"
                    )
                    ->get();
        
        foreach ($allData as $data) {
            $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$data->id_htrans)->sum("total_komisi_tempat");
            // dd($dataDtrans);
            $bulan = date('m', strtotime($data->tanggal_trans));
            $year = date('Y', strtotime($data->tanggal_trans));
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += $data->subtotal_lapangan+$dataDtrans;
            }
        }

        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        $param["trans"] = $allData;
        $param["monthlyIncome"] = $monthlyIncomeData;
        return view("tempat.laporan.laporanPendapatan")->with($param);
    }

    public function pendapatanTempatCetakPDF() {
        $role = Session::get("dataRole")->id_tempat;
        $data = DB::table('htrans')
            ->select(
                'htrans.id_htrans',
                "htrans.kode_trans",
                DB::raw('SUM(dtrans.total_komisi_tempat) as total_komisi'),
                'htrans.subtotal_lapangan',
                'htrans.tanggal_trans',
                DB::raw('COUNT(dtrans.id_dtrans) as alat'),
                "lapangan_olahraga.nama_lapangan"
            )
            ->leftJoin("dtrans", "htrans.id_htrans", "=", "dtrans.fk_id_htrans")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->where("htrans.fk_id_tempat", "=", $role)
            ->groupBy(
                'htrans.id_htrans',
                'htrans.kode_trans',
                'htrans.subtotal_lapangan',
                'htrans.tanggal_trans',
                "lapangan_olahraga.nama_lapangan"
            )
            ->get();
 
    	$pdf = PDF::loadview('tempat.laporan.laporanPendapatan_pdf',['data'=>$data]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanStokTempat() {
        $role = Session::get("dataRole")->id_tempat;
        $allData = DB::table('alat_olahraga')
                ->select("alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.req_harga_sewa as harga_permintaan", "request_penawaran.req_harga_sewa as harga_penawaran", "alat_olahraga.komisi_alat", "alat_olahraga.kategori_alat")
                ->leftJoin("request_permintaan", "alat_olahraga.id_alat", "=", "request_permintaan.req_id_alat")
                ->leftJoin("request_penawaran", "alat_olahraga.id_alat", "=", "request_penawaran.req_id_alat")
                ->leftJoin("sewa_sendiri", "alat_olahraga.id_alat", "=", "sewa_sendiri.req_id_alat")
                ->where(function ($query) use ($role) {
                    $query->where("request_permintaan.fk_id_tempat", "=", $role)
                        ->orWhere("request_penawaran.fk_id_tempat", "=", $role)
                        ->orWhere("sewa_sendiri.fk_id_tempat", "=", $role);
                })
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->get();

        // dd($allData);
        $param["stok"] = $allData;
        return view("tempat.laporan.laporanStok")->with($param);
    }

    public function stokTempatCetakPDF() {
        $role = Session::get("dataRole")->id_tempat;
        $data = DB::table('alat_olahraga')
                ->select("alat_olahraga.nama_alat", "files_alat.nama_file_alat", "request_permintaan.req_harga_sewa as harga_permintaan", "request_penawaran.req_harga_sewa as harga_penawaran", "alat_olahraga.komisi_alat", "alat_olahraga.kategori_alat")
                ->leftJoin("request_permintaan", "alat_olahraga.id_alat", "=", "request_permintaan.req_id_alat")
                ->leftJoin("request_penawaran", "alat_olahraga.id_alat", "=", "request_penawaran.req_id_alat")
                ->leftJoin("sewa_sendiri", "alat_olahraga.id_alat", "=", "sewa_sendiri.req_id_alat")
                ->where(function ($query) use ($role) {
                    $query->where("request_permintaan.fk_id_tempat", "=", $role)
                        ->orWhere("request_penawaran.fk_id_tempat", "=", $role)
                        ->orWhere("sewa_sendiri.fk_id_tempat", "=", $role);
                })
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->get();

        $pdf = PDF::loadview('tempat.laporan.laporanStok_pdf',['data'=>$data]);
        // return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanDisewakanTempat() {
        $role = Session::get("dataRole")->id_tempat;

        // $dtrans = new dtrans();
        // $allData = $dtrans->get_all_data_by_pemilik($role);
        $coba = DB::table('dtrans')
                ->select("dtrans.fk_id_htrans","htrans.tanggal_sewa","alat_olahraga.nama_alat","dtrans.harga_sewa_alat","htrans.durasi_sewa","dtrans.subtotal_alat")
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->rightJoin("htrans", "dtrans.fk_id_htrans","=","htrans.id_htrans")
                ->where("htrans.fk_id_tempat","=",$role)
                ->get();
        // dd($coba);

        $monthlyIncome = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        foreach ($coba as $data) {
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

        $param["disewakan"] = $coba;
        $param["monthlyIncome"] = $monthlyIncomeData;
        return view("tempat.laporan.laporanDisewakan")->with($param);
    }

    public function disewakanTempatCetakPDF() {
        $role = Session::get("dataRole")->id_tempat;
        $data = DB::table('dtrans')
                ->select("htrans.tanggal_sewa","alat_olahraga.nama_alat","dtrans.harga_sewa_alat","htrans.durasi_sewa","dtrans.subtotal_alat")
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->rightJoin("htrans", "dtrans.fk_id_htrans","=","htrans.id_htrans")
                ->where("htrans.fk_id_tempat","=",$role)
                ->get();
                
        $pdf = PDF::loadview('tempat.laporan.laporanDisewakan_pdf',['data'=>$data]);
        // return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanLapangan() {
        $role = Session::get("dataRole")->id_tempat;

        $allData = DB::table('lapangan_olahraga')
                    ->select(
                        "lapangan_olahraga.id_lapangan",
                        "files_lapangan.nama_file_lapangan",
                        "lapangan_olahraga.nama_lapangan",
                        DB::raw('COUNT(htrans.id_htrans) as total_sewa'),
                        "lapangan_olahraga.harga_sewa_lapangan",
                        "lapangan_olahraga.status_lapangan",
                        DB::raw('SUM(htrans.subtotal_lapangan) as total_pendapatan')
                    )
                    ->leftJoin("htrans", "lapangan_olahraga.id_lapangan", "=", "htrans.fk_id_lapangan")
                    ->joinSub(function($query) {
                        $query->select("fk_id_lapangan", "nama_file_lapangan")
                            ->from('files_lapangan')
                            ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                    }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                    ->where("lapangan_olahraga.pemilik_lapangan", "=", $role)
                    ->groupBy(
                        "lapangan_olahraga.id_lapangan",
                        "lapangan_olahraga.nama_lapangan",
                        "lapangan_olahraga.harga_sewa_lapangan",
                        "lapangan_olahraga.status_lapangan"
                    )
                    ->get();

        // dd($allData);

        $monthlyIncome = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        $dataHtrans = DB::table('htrans')->where("fk_id_tempat","=",$role)->get();

        foreach ($dataHtrans as $data) {
            if ($data->id_htrans != null) {
                $year = date('Y', strtotime($data->tanggal_sewa));
                $bulan = date('m', strtotime($data->tanggal_sewa));
        
                if ($year == date('Y')) {
                    $monthlyIncome[(int)$bulan] = $dataHtrans->count();
                }
            }
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        // $monthlyIncomeData = array_values($monthlyIncome);

        $param["lapangan"] = $allData;
        $param["monthlyIncome"] = $monthlyIncomeData;
        return view("tempat.laporan.laporanLapangan")->with($param);
    }

    public function LapanganCetakPDF() {
        $role = Session::get("dataRole")->id_tempat;

        $data = DB::table('lapangan_olahraga')
                    ->select(
                        "lapangan_olahraga.id_lapangan",
                        "files_lapangan.nama_file_lapangan",
                        "lapangan_olahraga.nama_lapangan",
                        DB::raw('COUNT(htrans.id_htrans) as total_sewa'),
                        "lapangan_olahraga.harga_sewa_lapangan",
                        "lapangan_olahraga.status_lapangan",
                        DB::raw('SUM(htrans.subtotal_lapangan) as total_pendapatan')
                    )
                    ->leftJoin("htrans", "lapangan_olahraga.id_lapangan", "=", "htrans.fk_id_lapangan")
                    ->joinSub(function($query) {
                        $query->select("fk_id_lapangan", "nama_file_lapangan")
                            ->from('files_lapangan')
                            ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                    }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                    ->where("lapangan_olahraga.pemilik_lapangan", "=", $role)
                    ->groupBy(
                        "lapangan_olahraga.id_lapangan",
                        "lapangan_olahraga.nama_lapangan",
                        "lapangan_olahraga.harga_sewa_lapangan",
                        "lapangan_olahraga.status_lapangan"
                    )
                    ->get();
        $pdf = PDF::loadview('tempat.laporan.laporanLapangan_pdf',['data'=>$data]);
        // return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanAlatAdmin() {
        $dtrans = DB::table('dtrans')->where("deleted_at","=",null)->get();

        $coba = DB::table('alat_olahraga')
                ->select(
                    "alat_olahraga.id_alat",
                    "files_alat.nama_file_alat",
                    "alat_olahraga.nama_alat",
                    DB::raw('COUNT(htrans.id_htrans) as total_sewa'),
                    "dtrans.harga_sewa_alat",
                    "alat_olahraga.status_alat",
                    DB::raw('SUM(htrans.subtotal_alat) as total_pendapatan')
                )
                ->leftJoin("dtrans", "alat_olahraga.id_alat", "=", "dtrans.fk_id_alat")
                ->leftJoin("htrans", "dtrans.fk_id_htrans", "=", "htrans.id_htrans")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->groupBy(
                    "alat_olahraga.id_alat",
                    "alat_olahraga.nama_alat",
                    "dtrans.harga_sewa_alat",
                    "alat_olahraga.status_alat"
                )
                ->get();
        // dd($coba);

        $monthlyIncome = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        foreach ($dtrans as $data) {
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

        $param["alat"] = $coba;
        $param["monthlyIncome"] = $monthlyIncomeData;
        // $param["yearlyMonthlyIncome"] = $yearlyMonthlyIncome;
        return view("admin.laporan.laporanAlat")->with($param);
    }

    public function AlatAdminCetakPDF() {
        $data = DB::table('alat_olahraga')
                ->select(
                    "alat_olahraga.id_alat",
                    "files_alat.nama_file_alat",
                    "alat_olahraga.nama_alat",
                    DB::raw('COUNT(htrans.id_htrans) as total_sewa'),
                    "dtrans.harga_sewa_alat",
                    "alat_olahraga.status_alat",
                    DB::raw('SUM(htrans.subtotal_alat) as total_pendapatan')
                )
                ->leftJoin("dtrans", "alat_olahraga.id_alat", "=", "dtrans.fk_id_alat")
                ->leftJoin("htrans", "dtrans.fk_id_htrans", "=", "htrans.id_htrans")
                ->joinSub(function($query) {
                    $query->select("fk_id_alat", "nama_file_alat")
                        ->from('files_alat')
                        ->whereRaw('id_file_alat = (select min(id_file_alat) from files_alat as f2 where f2.fk_id_alat = files_alat.fk_id_alat)');
                }, 'files_alat', 'alat_olahraga.id_alat', '=', 'files_alat.fk_id_alat')
                ->groupBy(
                    "alat_olahraga.id_alat",
                    "alat_olahraga.nama_alat",
                    "dtrans.harga_sewa_alat",
                    "alat_olahraga.status_alat"
                )
                ->get();
        $pdf = PDF::loadview('admin.laporan.laporanAlat_pdf',['data'=>$data]);
        // return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }
}
