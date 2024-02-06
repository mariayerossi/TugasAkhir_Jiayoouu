<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\dtrans;
use App\Models\htrans;
use App\Models\komplainRequest;
use App\Models\komplainTrans;
use App\Models\lapanganOlahraga;
use App\Models\requestPenawaran;
use App\Models\requestPermintaan;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;

use PDF;
use Termwind\Components\Raw;

class Laporan extends Controller
{
    //LAPORAN PEMILIK ALAT
    public function laporanPendapatanPemilik(){
        $role = Session::get("dataRole")->id_pemilik;
        // $trans = new dtrans();
        // $allData = $trans->get_all_data_by_pemilik($role);
        $coba = DB::table('htrans')
                ->select("alat_olahraga.nama_alat","alat_olahraga.komisi_alat","htrans.durasi_sewa","htrans.tanggal_trans","dtrans.total_komisi_pemilik", "dtrans.pendapatan_website_alat", "extend_htrans.durasi_extend", "extend_dtrans.total_komisi_pemilik as komisi_extend","extend_dtrans.pendapatan_website_alat as pendapatan_extend")
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->where("dtrans.fk_id_pemilik","=",$role)
                ->where("htrans.status_trans","=","Selesai")
                ->get();
        // dd($coba);

        $monthlyIncome = [];
        for ($i=1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        foreach ($coba as $data) {
            $bulan = date('m', strtotime($data->tanggal_trans));
            $year = date('Y', strtotime($data->tanggal_trans));
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += ($data->total_komisi_pemilik - $data->pendapatan_website_alat) + ($data->komisi_extend - $data->pendapatan_extend);
            }
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        $currentYear = date('Y');
        $yearlyIncome = [
            $currentYear - 4 => 0,
            $currentYear - 3 => 0,
            $currentYear - 2 => 0,
            $currentYear - 1 => 0,
            $currentYear => 0
        ];

        // Menghitung pendapatan tahunan
        foreach ($coba as $data) {
            $year = date('Y', strtotime($data->tanggal_trans));
            if (isset($yearlyIncome[$year])) {
                $yearlyIncome[$year] += ($data->total_komisi_pemilik - $data->pendapatan_website_alat) + ($data->komisi_extend - $data->pendapatan_extend);
            }
        }

        // Mengkonversi $yearlyIncome ke array biasa
        $yearlyIncomeData = array_values($yearlyIncome);
        $param["yearlyIncome"] = $yearlyIncomeData;

        $param["disewakan"] = $coba;
        $param["tanggal_mulai"] = null;
        $param["tanggal_selesai"] = null;
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

        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_selesai');

        $date_mulai = new DateTime($startDate);
        $date_selesai = new DateTime($endDate);
        
        if ($date_selesai <= $date_mulai) {
            return redirect()->back()->with("error", "Tanggal selesai tidak sesuai!");
        }

        // Query berdasarkan rentang tanggal yang dipilih
        // $trans = new dtrans();
        // $allData = $trans->get_all_data_by_pemilik($role);
        $coba = DB::table('htrans')
            ->select("alat_olahraga.nama_alat","alat_olahraga.komisi_alat","htrans.durasi_sewa","htrans.tanggal_trans","dtrans.total_komisi_pemilik", "dtrans.pendapatan_website_alat", "extend_htrans.durasi_extend", "extend_dtrans.total_komisi_pemilik as komisi_extend","extend_dtrans.pendapatan_website_alat as pendapatan_extend")
            ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
            ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
            ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
            ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
            ->where("dtrans.fk_id_pemilik","=",$role)
            ->where("htrans.status_trans","=","Selesai")
            ->whereBetween('htrans.tanggal_trans', [$startDate, $endDate])
            ->get();
        
        foreach ($coba as $data) {
            $bulan = date('m', strtotime($data->tanggal_trans));
            $year = date('Y', strtotime($data->tanggal_trans));
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += ($data->total_komisi_pemilik - $data->pendapatan_website_alat) + ($data->komisi_extend - $data->pendapatan_extend);
            }
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        $currentYear = date('Y');
        $yearlyIncome = [
            $currentYear - 4 => 0,
            $currentYear - 3 => 0,
            $currentYear - 2 => 0,
            $currentYear - 1 => 0,
            $currentYear => 0
        ];

        // Menghitung pendapatan tahunan
        foreach ($coba as $data) {
            $year = date('Y', strtotime($data->tanggal_trans));
            if (isset($yearlyIncome[$year])) {
                $yearlyIncome[$year] += ($data->total_komisi_pemilik - $data->pendapatan_website_alat) + ($data->komisi_extend - $data->pendapatan_extend);
            }
        }

        // Mengkonversi $yearlyIncome ke array biasa
        $yearlyIncomeData = array_values($yearlyIncome);
        $param["yearlyIncome"] = $yearlyIncomeData;

        $param["disewakan"] = $coba;
        $param["tanggal_mulai"] = $startDate;
        $param["tanggal_selesai"] = $endDate;
        $param["monthlyIncome"] = $monthlyIncomeData;
        return view("pemilik.laporan.laporanPendapatan")->with($param);
    }

    public function pendapatanPemilikCetakPDF(){
    	$role = Session::get("dataRole")->id_pemilik;
        $data = DB::table('htrans')
                ->select("alat_olahraga.nama_alat","alat_olahraga.komisi_alat","htrans.durasi_sewa","htrans.tanggal_trans","dtrans.total_komisi_pemilik", "dtrans.pendapatan_website_alat", "extend_htrans.durasi_extend", "extend_dtrans.total_komisi_pemilik as komisi_extend","extend_dtrans.pendapatan_website_alat as pendapatan_extend")
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->where("dtrans.fk_id_pemilik","=",$role)
                ->where("htrans.status_trans","=","Selesai")
                ->get();
 
    	$pdf = PDF::loadview('pemilik.laporan.laporanPendapatan_pdf',['data'=>$data, 'tanggal_mulai'=>null, 'tanggal_selesai'=>null]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function pendapatanPemilikCetakPDF2(Request $request){
    	$role = Session::get("dataRole")->id_pemilik;

        $data = DB::table('htrans')
                ->select("alat_olahraga.nama_alat","alat_olahraga.komisi_alat","htrans.durasi_sewa","htrans.tanggal_trans","dtrans.total_komisi_pemilik", "dtrans.pendapatan_website_alat", "extend_htrans.durasi_extend", "extend_dtrans.total_komisi_pemilik as komisi_extend","extend_dtrans.pendapatan_website_alat as pendapatan_extend")
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->where("dtrans.fk_id_pemilik","=",$role)
                ->where("htrans.status_trans","=","Selesai")
                ->whereBetween('htrans.tanggal_trans', [$request->mulai, $request->selesai])
                ->get();
        // dd($request->mulai);
 
    	$pdf = PDF::loadview('pemilik.laporan.laporanPendapatan_pdf',['data'=>$data, 'tanggal_mulai'=>$request->mulai, 'tanggal_selesai'=>$request->selesai]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanStokPemilik() {
        $role = Session::get("dataRole")->id_pemilik;
        // $alat = new alatOlahraga();
        // $allData = $alat->get_all_data($role, "Pemilik");
        $allData = DB::table('alat_olahraga')
                    ->select("alat_olahraga.id_alat","alat_olahraga.nama_alat", DB::raw('count(dtrans.id_dtrans) as totalRequest'), DB::raw('sum(dtrans.total_komisi_pemilik) as totalKomisi'), DB::raw('sum(extend_dtrans.total_komisi_pemilik) as totalKomisiExt'),"alat_olahraga.status_alat","kategori.nama_kategori")
                    ->leftJoin("dtrans","alat_olahraga.id_alat","=","dtrans.fk_id_alat")
                    ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                    ->leftJoin("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
                    ->join("kategori","alat_olahraga.fk_id_kategori","=","kategori.id_kategori")
                    ->where("alat_olahraga.fk_id_pemilik","=",$role)
                    ->where("htrans.status_trans","!=","Dibatalkan")
                    ->where("htrans.status_trans","!=","Ditolak")
                    ->groupBy("alat_olahraga.id_alat","alat_olahraga.nama_alat", "alat_olahraga.status_alat","kategori.nama_kategori")
                    ->get();

        // dd($allData);

        $param["alat"] = $allData;
        return view("pemilik.laporan.laporanStok")->with($param);
    }

    public function stokPemilikCetakPDF(){
    	$role = Session::get("dataRole")->id_pemilik;
        $data = DB::table('alat_olahraga')
            ->select("alat_olahraga.id_alat","alat_olahraga.nama_alat", DB::raw('count(dtrans.id_dtrans) as totalRequest'), DB::raw('sum(dtrans.total_komisi_pemilik) as totalKomisi'), DB::raw('sum(extend_dtrans.total_komisi_pemilik) as totalKomisiExt'),"alat_olahraga.status_alat","kategori.nama_kategori")
            ->leftJoin("dtrans","alat_olahraga.id_alat","=","dtrans.fk_id_alat")
            ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
            ->leftJoin("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
            ->join("kategori","alat_olahraga.fk_id_kategori","=","kategori.id_kategori")
            ->where("alat_olahraga.fk_id_pemilik","=",$role)
            ->where("htrans.status_trans","!=","Dibatalkan")
            ->where("htrans.status_trans","!=","Ditolak")
            ->groupBy("alat_olahraga.id_alat","alat_olahraga.nama_alat", "alat_olahraga.status_alat","kategori.nama_kategori")
            ->get();
 
    	$pdf = PDF::loadview('pemilik.laporan.laporanStok_pdf',['data'=>$data]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanDisewakanPemilik() {
        $role = Session::get("dataRole")->id_pemilik;
        $dtrans = new dtrans();
        $allData = $dtrans->get_all_data_by_pemilik($role);
        // $coba = DB::table('dtrans')
        //         ->select("htrans.tanggal_sewa","alat_olahraga.nama_alat","dtrans.harga_sewa_alat","htrans.durasi_sewa","dtrans.subtotal_alat")
        //         ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
        //         ->rightJoin("htrans", "dtrans.fk_id_htrans","=","htrans.id_htrans")
        //         ->where("dtrans.fk_id_pemilik","=",$role)
        //         ->get();
        $coba = DB::table("alat_olahraga")
                    ->select(
                        "alat_olahraga.id_alat",
                        "alat_olahraga.nama_alat",
                        "alat_olahraga.komisi_alat",
                        DB::raw('SUM(htrans.durasi_sewa) as total_durasi'),
                        DB::raw('SUM(dtrans.total_komisi_pemilik) as total_pendapatan'),
                        DB::raw('COUNT(dtrans.id_dtrans) as total_sewa'),
                        DB::raw('SUM(extend_htrans.durasi_extend) as durasi_extend'),
                        DB::raw('SUM(extend_dtrans.total_komisi_pemilik) as komisi_extend'),
                    )
                    ->join("dtrans","alat_olahraga.id_alat","=","dtrans.fk_id_alat")
                    ->rightJoin("htrans", "dtrans.fk_id_htrans","=","htrans.id_htrans")
                    ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                    ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                    ->where("htrans.status_trans","!=","Dibatalkan")
                    ->where("htrans.status_trans","!=","Ditolak")
                    ->where("alat_olahraga.fk_id_pemilik","=",$role)
                    ->groupBy(
                        "alat_olahraga.id_alat",
                        "alat_olahraga.nama_alat",
                        "alat_olahraga.komisi_alat",
                    )
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
        $data = DB::table("alat_olahraga")
                ->select(
                    "alat_olahraga.id_alat",
                    "alat_olahraga.nama_alat",
                    "alat_olahraga.komisi_alat",
                    DB::raw('SUM(htrans.durasi_sewa) as total_durasi'),
                    DB::raw('SUM(dtrans.total_komisi_pemilik) as total_pendapatan'),
                    DB::raw('COUNT(dtrans.id_dtrans) as total_sewa'),
                    DB::raw('SUM(extend_htrans.durasi_extend) as durasi_extend'),
                    DB::raw('SUM(extend_dtrans.total_komisi_pemilik) as komisi_extend'),
                )
                ->join("dtrans","alat_olahraga.id_alat","=","dtrans.fk_id_alat")
                ->rightJoin("htrans", "dtrans.fk_id_htrans","=","htrans.id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->where("htrans.status_trans","!=","Dibatalkan")
                ->where("htrans.status_trans","!=","Ditolak")
                ->where("alat_olahraga.fk_id_pemilik","=",$role)
                ->groupBy(
                    "alat_olahraga.id_alat",
                    "alat_olahraga.nama_alat",
                    "alat_olahraga.komisi_alat",
                )
                ->get();
 
    	$pdf = PDF::loadview('pemilik.laporan.laporanDisewakan_pdf',['data'=>$data]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanTempatPemilik() {
        //tampilkan tempat olahraga yang kerjasama sm pemilik dan total komisinya
        $role = Session::get("dataRole")->id_pemilik;
        $allData = DB::table('pihak_tempat')
            ->select('pihak_tempat.nama_tempat', DB::raw('SUM(dtrans.total_komisi_pemilik) as total_komisi'), DB::raw('COUNT(dtrans.fk_id_alat) as jumlah'), DB::raw('SUM(extend_dtrans.total_komisi_pemilik) as komisi_extend'))
            ->join('htrans', 'pihak_tempat.id_tempat', '=', 'htrans.fk_id_tempat')
            ->join('dtrans', 'htrans.id_htrans', '=', 'dtrans.fk_id_htrans')
            ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
            ->where("htrans.status_trans","!=","Dibatalkan")
            ->where("htrans.status_trans","!=","Ditolak")
            ->where('dtrans.fk_id_pemilik', '=', $role)
            ->groupBy('pihak_tempat.id_tempat', 'pihak_tempat.nama_tempat')  // tambahkan 'pihak_tempat.nama_tempat' ke GROUP BY
            ->get();
            // dd($allData);

        $param["tempat"] = $allData;
        return view("pemilik.laporan.laporanTempat")->with($param);
    }

    public function tempatPemilikCetakPDF() {
        $role = Session::get("dataRole")->id_pemilik;
        $data = DB::table('pihak_tempat')
            ->select('pihak_tempat.nama_tempat', DB::raw('SUM(dtrans.total_komisi_pemilik) as total_komisi'), DB::raw('COUNT(dtrans.fk_id_alat) as jumlah'), DB::raw('SUM(extend_dtrans.total_komisi_pemilik) as komisi_extend'))
            ->join('htrans', 'pihak_tempat.id_tempat', '=', 'htrans.fk_id_tempat')
            ->join('dtrans', 'htrans.id_htrans', '=', 'dtrans.fk_id_htrans')
            ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
            ->where("htrans.status_trans","!=","Dibatalkan")
            ->where("htrans.status_trans","!=","Ditolak")
            ->where('dtrans.fk_id_pemilik', '=', $role)
            ->groupBy('pihak_tempat.id_tempat', 'pihak_tempat.nama_tempat')  // tambahkan 'pihak_tempat.nama_tempat' ke GROUP BY
            ->get();
 
    	$pdf = PDF::loadview('pemilik.laporan.laporanTempat_pdf',['data'=>$data]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    //----------------------------------------------------------------------------------------------------

    //LAPORAN PIHAK TEMPAT OLAHRAGA
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
                "lapangan_olahraga.nama_lapangan",
                "htrans.pendapatan_website_lapangan",
                DB::raw('SUM(extend_dtrans.total_komisi_tempat) as komisi_extend'),
                'extend_htrans.subtotal_lapangan as subtotal_ext',
                "extend_htrans.pendapatan_website_lapangan as pendapatan_ext",
                'extend_htrans.id_extend_htrans'
            )
            ->leftJoin("dtrans", "htrans.id_htrans", "=", "dtrans.fk_id_htrans")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
            ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
            ->where("htrans.status_trans","=","Selesai")
            ->where("htrans.fk_id_tempat", "=", $role)
            ->groupBy(
                'htrans.id_htrans',
                'htrans.kode_trans',
                'htrans.subtotal_lapangan',
                'htrans.tanggal_trans',
                "lapangan_olahraga.nama_lapangan",
                "htrans.pendapatan_website_lapangan",
                'extend_htrans.subtotal_lapangan',
                "extend_htrans.pendapatan_website_lapangan",
                'extend_htrans.id_extend_htrans'
            )
            ->get();
        // dd($coba);
        $monthlyIncome = [];
        for ($i=1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        foreach ($coba as $data) {
            // $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$data->id_htrans)->sum("total_komisi_tempat");
            // $dataExtendDtrans = DB::table('extend_dtrans')->where("fk_id_extend_htrans","=",$data->id_extend_htrans)->sum("total_komisi_tempat");
            // dd($data->id_extend_htrans);
            $bulan = date('m', strtotime($data->tanggal_trans));
            $year = date('Y', strtotime($data->tanggal_trans));
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += ($data->subtotal_lapangan + $data->total_komisi - $data->pendapatan_website_lapangan) + ($data->subtotal_ext + $data->komisi_extend - $data->pendapatan_ext);
            }
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        // Inisialisasi pendapatan tahunan untuk 5 tahun terakhir
        $currentYear = date('Y');
        $yearlyIncome = [
            $currentYear - 4 => 0,
            $currentYear - 3 => 0,
            $currentYear - 2 => 0,
            $currentYear - 1 => 0,
            $currentYear => 0
        ];

        // Menghitung pendapatan tahunan
        foreach ($coba as $data) {
            $year = date('Y', strtotime($data->tanggal_trans));
            if (isset($yearlyIncome[$year])) {
                $yearlyIncome[$year] += ($data->subtotal_lapangan + $data->total_komisi - $data->pendapatan_website_lapangan) + ($data->subtotal_ext + $data->komisi_extend - $data->pendapatan_ext);
            }
        }

        // Mengkonversi $yearlyIncome ke array biasa
        $yearlyIncomeData = array_values($yearlyIncome);
        $param["yearlyIncome"] = $yearlyIncomeData;

        $param["trans"] = $coba;
        $param["tanggal_mulai"] = null;
        $param["tanggal_selesai"] = null;
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
                        "lapangan_olahraga.nama_lapangan",
                        "htrans.pendapatan_website_lapangan",
                        DB::raw('SUM(extend_dtrans.total_komisi_tempat) as komisi_extend'),
                        'extend_htrans.subtotal_lapangan as subtotal_ext',
                        "extend_htrans.pendapatan_website_lapangan as pendapatan_ext",
                        'extend_htrans.id_extend_htrans'
                    )
                    ->leftJoin("dtrans", "htrans.id_htrans", "=", "dtrans.fk_id_htrans")
                    ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
                    ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                    ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                    ->whereBetween('htrans.tanggal_trans', [$startDate, $endDate])
                    ->where("htrans.fk_id_tempat", "=", $role)
                    ->where("htrans.status_trans","=","Selesai")
                    ->groupBy(
                        'htrans.id_htrans',
                        'htrans.kode_trans',
                        'htrans.subtotal_lapangan',
                        'htrans.tanggal_trans',
                        "lapangan_olahraga.nama_lapangan",
                        "htrans.pendapatan_website_lapangan",
                        'extend_htrans.subtotal_lapangan',
                        "extend_htrans.pendapatan_website_lapangan",
                        'extend_htrans.id_extend_htrans'
                    )
                    ->get();
        
        foreach ($allData as $data) {
            $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$data->id_htrans)->sum("total_komisi_tempat");
            // dd($dataDtrans);
            $bulan = date('m', strtotime($data->tanggal_trans));
            $year = date('Y', strtotime($data->tanggal_trans));
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += ($data->subtotal_lapangan + $data->total_komisi - $data->pendapatan_website_lapangan) + ($data->subtotal_ext + $data->komisi_extend - $data->pendapatan_ext);
            }
        }

        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        // Inisialisasi pendapatan tahunan untuk 5 tahun terakhir
        $currentYear = date('Y');
        $yearlyIncome = [
            $currentYear - 4 => 0,
            $currentYear - 3 => 0,
            $currentYear - 2 => 0,
            $currentYear - 1 => 0,
            $currentYear => 0
        ];

        // Menghitung pendapatan tahunan
        foreach ($allData as $data) {
            $year = date('Y', strtotime($data->tanggal_trans));
            if (isset($yearlyIncome[$year])) {
                $yearlyIncome[$year] += ($data->subtotal_lapangan + $data->total_komisi - $data->pendapatan_website_lapangan) + ($data->subtotal_ext + $data->komisi_extend - $data->pendapatan_ext);
            }
        }

        // Mengkonversi $yearlyIncome ke array biasa
        $yearlyIncomeData = array_values($yearlyIncome);
        $param["yearlyIncome"] = $yearlyIncomeData;

        $param["trans"] = $allData;
        $param["tanggal_mulai"] = $startDate;
        $param["tanggal_selesai"] = $endDate;
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
                    "lapangan_olahraga.nama_lapangan",
                    "htrans.pendapatan_website_lapangan",
                    DB::raw('SUM(extend_dtrans.total_komisi_tempat) as komisi_extend'),
                    'extend_htrans.subtotal_lapangan as subtotal_ext',
                    "extend_htrans.pendapatan_website_lapangan as pendapatan_ext",
                    'extend_htrans.id_extend_htrans'
                )
                ->leftJoin("dtrans", "htrans.id_htrans", "=", "dtrans.fk_id_htrans")
                ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->where("htrans.status_trans","=","Selesai")
                ->where("htrans.fk_id_tempat", "=", $role)
                ->groupBy(
                    'htrans.id_htrans',
                    'htrans.kode_trans',
                    'htrans.subtotal_lapangan',
                    'htrans.tanggal_trans',
                    "lapangan_olahraga.nama_lapangan",
                    "htrans.pendapatan_website_lapangan",
                    'extend_htrans.subtotal_lapangan',
                    "extend_htrans.pendapatan_website_lapangan",
                    'extend_htrans.id_extend_htrans'
                )
                ->get();
 
    	$pdf = PDF::loadview('tempat.laporan.laporanPendapatan_pdf',['data'=>$data, 'tanggal_mulai'=>null, 'tanggal_selesai'=>null]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function pendapatanTempatCetakPDF2(Request $request) {
        $role = Session::get("dataRole")->id_tempat;
        $data = DB::table('htrans')
            ->select(
                'htrans.id_htrans',
                "htrans.kode_trans",
                DB::raw('SUM(dtrans.total_komisi_tempat) as total_komisi'),
                'htrans.subtotal_lapangan',
                'htrans.tanggal_trans',
                DB::raw('COUNT(dtrans.id_dtrans) as alat'),
                "lapangan_olahraga.nama_lapangan",
                "htrans.pendapatan_website_lapangan",
                DB::raw('SUM(extend_dtrans.total_komisi_tempat) as komisi_extend'),
                'extend_htrans.subtotal_lapangan as subtotal_ext',
                "extend_htrans.pendapatan_website_lapangan as pendapatan_ext",
                'extend_htrans.id_extend_htrans'
            )
            ->leftJoin("dtrans", "htrans.id_htrans", "=", "dtrans.fk_id_htrans")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
            ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
            ->whereBetween('htrans.tanggal_trans', [$request->mulai, $request->selesai])
            ->where("htrans.fk_id_tempat", "=", $role)
            ->where("htrans.status_trans","=","Selesai")
            ->groupBy(
                'htrans.id_htrans',
                'htrans.kode_trans',
                'htrans.subtotal_lapangan',
                'htrans.tanggal_trans',
                "lapangan_olahraga.nama_lapangan",
                "htrans.pendapatan_website_lapangan",
                'extend_htrans.subtotal_lapangan',
                "extend_htrans.pendapatan_website_lapangan",
                'extend_htrans.id_extend_htrans'
            )
            ->get();
            // dd($request->mulai);
 
    	$pdf = PDF::loadview('tempat.laporan.laporanPendapatan_pdf',['data'=>$data, 'tanggal_mulai'=>$request->mulai, 'tanggal_selesai'=>$request->selesai]);
    	// return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanStokTempat() {
        $role = Session::get("dataRole")->id_tempat;
        $allData = DB::table('alat_olahraga')
                ->select("alat_olahraga.id_alat","alat_olahraga.nama_alat", "request_permintaan.req_harga_sewa as harga_permintaan", DB::raw('SUM(dtrans.total_komisi_tempat) as total_komisi'), DB::raw('SUM(extend_dtrans.total_komisi_tempat) as total_komisi_ext'), "request_penawaran.req_harga_sewa as harga_penawaran", "alat_olahraga.komisi_alat", "kategori.nama_kategori")
                ->leftJoin("request_permintaan", "alat_olahraga.id_alat", "=", "request_permintaan.req_id_alat")
                ->leftJoin("request_penawaran", "alat_olahraga.id_alat", "=", "request_penawaran.req_id_alat")
                ->leftJoin("sewa_sendiri", "alat_olahraga.id_alat", "=", "sewa_sendiri.req_id_alat")
                ->leftJoin("dtrans","alat_olahraga.id_alat","=","dtrans.fk_id_alat")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->join("kategori","alat_olahraga.fk_id_kategori","=","kategori.id_kategori")
                ->orWhere(function ($query) use ($role) {
                    $query->where("request_permintaan.fk_id_tempat", "=", $role)
                        ->where("request_permintaan.status_permintaan", "=", "Disewakan");
                })
                ->orWhere(function ($query) use ($role) {
                    $query->where("request_penawaran.fk_id_tempat", "=", $role)
                        ->where("request_penawaran.status_penawaran", "=", "Disewakan");
                })
                ->orWhere(function ($query) use ($role) {
                    $query->where("sewa_sendiri.fk_id_tempat", "=", $role);
                })
                ->groupBy("alat_olahraga.id_alat","alat_olahraga.nama_alat", "request_permintaan.req_harga_sewa", "request_penawaran.req_harga_sewa", "alat_olahraga.komisi_alat", "kategori.nama_kategori")
                ->get();

        // dd($allData);
        $param["stok"] = $allData;
        return view("tempat.laporan.laporanStok")->with($param);
    }

    public function stokTempatCetakPDF() {
        $role = Session::get("dataRole")->id_tempat;
        $data = DB::table('alat_olahraga')
                ->select("alat_olahraga.id_alat","alat_olahraga.nama_alat", "request_permintaan.req_harga_sewa as harga_permintaan", DB::raw('SUM(dtrans.total_komisi_tempat) as total_komisi'), DB::raw('SUM(extend_dtrans.total_komisi_tempat) as total_komisi_ext'), "request_penawaran.req_harga_sewa as harga_penawaran", "alat_olahraga.komisi_alat", "kategori.nama_kategori")
                ->leftJoin("request_permintaan", "alat_olahraga.id_alat", "=", "request_permintaan.req_id_alat")
                ->leftJoin("request_penawaran", "alat_olahraga.id_alat", "=", "request_penawaran.req_id_alat")
                ->leftJoin("sewa_sendiri", "alat_olahraga.id_alat", "=", "sewa_sendiri.req_id_alat")
                ->leftJoin("dtrans","alat_olahraga.id_alat","=","dtrans.fk_id_alat")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->join("kategori","alat_olahraga.fk_id_kategori","=","kategori.id_kategori")
                ->orWhere(function ($query) use ($role) {
                    $query->where("request_permintaan.fk_id_tempat", "=", $role)
                        ->where("request_permintaan.status_permintaan", "=", "Disewakan");
                })
                ->orWhere(function ($query) use ($role) {
                    $query->where("request_penawaran.fk_id_tempat", "=", $role)
                        ->where("request_penawaran.status_penawaran", "=", "Disewakan");
                })
                ->orWhere(function ($query) use ($role) {
                    $query->where("sewa_sendiri.fk_id_tempat", "=", $role);
                })
                ->groupBy("alat_olahraga.id_alat","alat_olahraga.nama_alat", "request_permintaan.req_harga_sewa", "request_penawaran.req_harga_sewa", "alat_olahraga.komisi_alat", "kategori.nama_kategori")
                ->get();

        $pdf = PDF::loadview('tempat.laporan.laporanStok_pdf',['data'=>$data]);
        // return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanDisewakanTempat() {
        $role = Session::get("dataRole")->id_tempat;

        // $dtrans = new dtrans();
        // $allData = $dtrans->get_all_data_by_pemilik($role);
        // $coba = DB::table('dtrans')
        //         ->select("dtrans.fk_id_htrans","htrans.tanggal_sewa","alat_olahraga.nama_alat","dtrans.harga_sewa_alat","htrans.durasi_sewa","dtrans.subtotal_alat")
        //         ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
        //         ->rightJoin("htrans", "dtrans.fk_id_htrans","=","htrans.id_htrans")
        //         ->where("htrans.fk_id_tempat","=",$role)
        //         ->get();
        $coba = DB::table('alat_olahraga')
                ->select(
                    "alat_olahraga.id_alat",
                    "alat_olahraga.nama_alat",
                    DB::raw('COUNT(dtrans.id_dtrans) as total_sewa'),
                    "dtrans.harga_sewa_alat",
                    "alat_olahraga.komisi_alat",
                    DB::raw('SUM(htrans.durasi_sewa) as total_durasi'),
                    DB::raw('SUM(dtrans.total_komisi_tempat) as total_pendapatan'),
                    "alat_olahraga.status_alat",
                    "alat_olahraga.fk_id_pemilik",
                    "alat_olahraga.fk_id_tempat",
                    DB::raw('SUM(extend_htrans.durasi_extend) as durasi_ext'),
                    DB::raw('SUM(extend_dtrans.total_komisi_tempat) as komisi_ext'),
                )
                ->join("dtrans", "alat_olahraga.id_alat", "=", "dtrans.fk_id_alat")
                ->join("htrans", "dtrans.fk_id_htrans","=","htrans.id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->where("htrans.fk_id_tempat", "=", $role)
                ->where("htrans.status_trans","=","Selesai")
                ->groupBy(
                    "alat_olahraga.id_alat",
                    "alat_olahraga.nama_alat",
                    "dtrans.harga_sewa_alat",
                    "alat_olahraga.komisi_alat",
                    "alat_olahraga.status_alat",
                    "alat_olahraga.fk_id_pemilik",
                    "alat_olahraga.fk_id_tempat"
                )
                ->get();
        // dd($coba);

        $param["disewakan"] = $coba;
        return view("tempat.laporan.laporanDisewakan")->with($param);
    }

    public function disewakanTempatCetakPDF() {
        $role = Session::get("dataRole")->id_tempat;
        $data = DB::table('alat_olahraga')
                ->select(
                    "alat_olahraga.id_alat",
                    "alat_olahraga.nama_alat",
                    DB::raw('COUNT(dtrans.id_dtrans) as total_sewa'),
                    "dtrans.harga_sewa_alat",
                    "alat_olahraga.komisi_alat",
                    DB::raw('SUM(htrans.durasi_sewa) as total_durasi'),
                    DB::raw('SUM(dtrans.total_komisi_tempat) as total_pendapatan'),
                    "alat_olahraga.status_alat",
                    "alat_olahraga.fk_id_pemilik",
                    "alat_olahraga.fk_id_tempat",
                    DB::raw('SUM(extend_htrans.durasi_extend) as durasi_ext'),
                    DB::raw('SUM(extend_dtrans.total_komisi_tempat) as komisi_ext'),
                )
                ->join("dtrans", "alat_olahraga.id_alat", "=", "dtrans.fk_id_alat")
                ->join("htrans", "dtrans.fk_id_htrans","=","htrans.id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->where("htrans.fk_id_tempat", "=", $role)
                ->where("htrans.status_trans","=","Selesai")
                ->groupBy(
                    "alat_olahraga.id_alat",
                    "alat_olahraga.nama_alat",
                    "dtrans.harga_sewa_alat",
                    "alat_olahraga.komisi_alat",
                    "alat_olahraga.status_alat",
                    "alat_olahraga.fk_id_pemilik",
                    "alat_olahraga.fk_id_tempat"
                )
                ->get();
                
        $pdf = PDF::loadview('tempat.laporan.laporanDisewakan_pdf',['data'=>$data]);
        // return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanPerAlatTempat(Request $request) {
        $endDate1 = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate1)));
    
        // Mengambil tanggal satu bulan sebelumnya dari tanggal hari ini
        $startDate = date('Y-m-d', strtotime('-1 month', strtotime($endDate)));
        $startDate = date('Y-m-d', strtotime('+1 day', strtotime($startDate)));

        $dataAlat = DB::table('alat_olahraga')
            ->where("id_alat","=",$request->id)
            ->get()
            ->first();
        
        $req = DB::table('request_permintaan')
            ->where("req_id_alat","=",$request->id)
            ->where("fk_id_tempat","=",Session::get("dataRole")->id_tempat)
            ->get()
            ->first();
        if ($req == null) {
            $req = DB::table('request_penawaran')
            ->where("req_id_alat","=",$request->id)
            ->where("fk_id_tempat","=",Session::get("dataRole")->id_tempat)
            ->get()
            ->first();
        }
    
        $dataDtrans = DB::table('dtrans')
            ->select("htrans.tanggal_sewa","dtrans.total_komisi_tempat")
            ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
            ->where("dtrans.fk_id_alat","=",$request->id)
            ->whereBetween('htrans.tanggal_sewa', [$startDate, $endDate])
            ->where("htrans.status_trans","=","Selesai")
            ->get();
    
        $monthlyIncome = [];
        $total = [];
        $total2 = [];
        foreach (new DatePeriod(new DateTime($startDate), new DateInterval('P1D'), new DateTime($endDate)) as $date) {
            $dateStr = $date->format('Y-m-d');
            $monthlyIncome[$dateStr] = 0;
            $total[$dateStr] = 0;
            $total2[$dateStr] = 0;
        }
        // dd($monthlyIncome);
    
        foreach ($dataDtrans as $data) {
            $sewaDate = date('Y-m-d', strtotime($data->tanggal_sewa));
        
            if ($sewaDate >= $startDate && $sewaDate <= $endDate) {
                $day = date('Y-m-d', strtotime($sewaDate));
                $monthlyIncome[$day] += 1;
                $total[$day] += $data->total_komisi_tempat * 0.91;
                $total2[$day] += $data->total_komisi_tempat;
            }
        }
        // dd($monthlyIncome);
        
        $labels = [];
        $currentDate = $startDate;
        
        while (strtotime($currentDate) <= strtotime($endDate)) {
            $labels[] = date('j M', strtotime($currentDate));
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
    
        $monthlyIncomeData = array_values($monthlyIncome); // Mengambil nilai dari array asosiatif

        $currentMonth = end($monthlyIncomeData);
        $previousMonth = prev($monthlyIncomeData);

        if ($previousMonth == 0) {
            $increasePercentage = ($currentMonth > 0) ? 100 : 0;
        } else {
            $increasePercentage = round((($currentMonth - $previousMonth) / $previousMonth) * 100);
        }

        $increasePercentage = number_format($increasePercentage, 2); // Format persentase dengan 2 desimal
        $totalData = array_values($total);
        $totalData2 = array_values($total2);
        // ...

        $filter = "1 Bulan";

        $param["monthlyLabels"] = $labels;
        // dd($labels);
        // dd($monthlyIncomeData);
        $param["filter"] = $filter;
        $param["monthlyIncome"] = $monthlyIncomeData;
        $param["alat"] = $dataAlat;
        $param["dtrans"] = $dataDtrans;
        $param["request"] = $req;
        $param["mulai"] = $startDate;
        $param["selesai"] = $endDate1;
        $param["increasePercentage"] = $increasePercentage;
        $param["total"] = $totalData;
        $param["totalKotor"] = $totalData2;
        return view("tempat.laporan.laporanPerAlat")->with($param);
    }

    public function fiturPerAlat(Request $request) {
        // dd($request->filter);
        $endDate1 = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate1)));
    
        // Mengambil tanggal satu bulan sebelumnya dari tanggal hari ini
        $startDate = date('Y-m-d', strtotime('-'.$request->filter.' month', strtotime($endDate)));
        $startDate = date('Y-m-d', strtotime('+1 day', strtotime($startDate)));

        $dataAlat = DB::table('alat_olahraga')
            ->where("id_alat","=",$request->id)
            ->get()
            ->first();
        
        $req = DB::table('request_permintaan')
            ->where("req_id_alat","=",$request->id)
            ->where("fk_id_tempat","=",Session::get("dataRole")->id_tempat)
            ->get()
            ->first();
        if ($req == null) {
            $req = DB::table('request_penawaran')
            ->where("req_id_alat","=",$request->id)
            ->where("fk_id_tempat","=",Session::get("dataRole")->id_tempat)
            ->get()
            ->first();
        }
    
        $dataDtrans = DB::table('dtrans')
            ->select("htrans.tanggal_sewa","dtrans.total_komisi_tempat")
            ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
            ->where("dtrans.fk_id_alat","=",$request->id)
            ->whereBetween('htrans.tanggal_sewa', [$startDate, $endDate])
            ->get();
        // dd($dataDtrans);
    
        if ($request->filter == "1" || $request->filter == "2") {
            $monthlyIncome = [];
            $total = [];
            $total2 = [];
            foreach (new DatePeriod(new DateTime($startDate), new DateInterval('P1D'), new DateTime($endDate)) as $date) {
                $dateStr = $date->format('Y-m-d');
                $monthlyIncome[$dateStr] = 0;
                $total[$dateStr] = 0;
                $total2[$dateStr] = 0;
            }
            // dd($monthlyIncome);
        
            foreach ($dataDtrans as $data) {
                $sewaDate = date('Y-m-d', strtotime($data->tanggal_sewa));
            
                if ($sewaDate >= $startDate && $sewaDate <= $endDate) {
                    $day = date('Y-m-d', strtotime($sewaDate));
                    // dd($day);
                    $monthlyIncome[$day] += 1;
                    $total[$day] += $data->total_komisi_tempat * 0.91;
                    $total2[$day] += $data->total_komisi_tempat;
                }
            }
            // dd($monthlyIncome);
            
            $labels = [];
            $currentDate = $startDate;
            
            while (strtotime($currentDate) <= strtotime($endDate)) {
                $labels[] = date('j M y', strtotime($currentDate));
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
        
            $monthlyIncomeData = array_values($monthlyIncome); // Mengambil nilai dari array asosiatif
            $totalData = array_values($total);
            $totalData2 = array_values($total2);

            $currentMonth = end($monthlyIncomeData);
            $previousMonth = prev($monthlyIncomeData);

            if ($previousMonth == 0) {
                $increasePercentage = ($currentMonth > 0) ? 100 : 0;
            } else {
                $increasePercentage = round((($currentMonth - $previousMonth) / $previousMonth) * 100);
            }

            $increasePercentage = number_format($increasePercentage, 2); // Format persentase dengan 2 desimal
            
            $filter = $request->filter . " Bulan";

            $param["monthlyLabels"] = $labels;
            $param["monthlyIncome"] = $monthlyIncomeData;
            $param["filter"] = $filter;
            $param["alat"] = $dataAlat;
            $param["dtrans"] = $dataDtrans;
            $param["request"] = $req;
            $param["mulai"] = $startDate;
            $param["selesai"] = $endDate1;
            $param["total"] = $totalData;
            $param["totalKotor"] = $totalData2;
            $param["increasePercentage"] = $increasePercentage;
        }
        else if ($request->filter >= "3" && $request->filter < "36") {
            $endDate1 = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate1)));

            // Set the start date to July (7 months before the current date)
            $startDate = date('Y-m-d', strtotime('-'.$request->filter.' months', strtotime($endDate)));
            $startDate = date('Y-m-d', strtotime('first day of next month', strtotime($startDate)));

            $monthlyIncome = [];
            $total = [];
            $total2 = [];
            foreach (new DatePeriod(new DateTime($startDate), new DateInterval('P1D'), new DateTime($endDate)) as $date) {
                $dateStr = $date->format('Y-m');
                $monthlyIncome[$dateStr] = 0;
                $total[$dateStr] = 0;
                $total2[$dateStr] = 0;
            }
            // dd($monthlyIncome);
        
            foreach ($dataDtrans as $data) {
                $sewaDate = date('Y-m', strtotime($data->tanggal_sewa));
            
                if ($sewaDate >= $startDate && $sewaDate <= $endDate) {
                    $day = date('Y-m', strtotime($sewaDate));
                    // dd($day);
                    $monthlyIncome[$day] += 1;
                    $total[$day] += $data->total_komisi_tempat * 0.91;
                    $total2[$day] += $data->total_komisi_tempat;
                }
            }
            // dd($total);
            
            $labels = [];
            $currentDate = new DateTime($startDate);
            $endDateTime = new DateTime();

            while ($currentDate <= $endDateTime) {
                $labels[] = $currentDate->format('M Y');
                $currentDate->add(new DateInterval('P1M'));
            }

            $monthlyIncomeData = array_values($monthlyIncome);
            $totalData = array_values($total);
            $totalData2 = array_values($total2);

            $currentMonth = end($monthlyIncomeData);
            $previousMonth = prev($monthlyIncomeData);

            if ($previousMonth == 0) {
                $increasePercentage = ($currentMonth > 0) ? 100 : 0;
            } else {
                $increasePercentage = round((($currentMonth - $previousMonth) / $previousMonth) * 100);
            }

            $increasePercentage = number_format($increasePercentage, 2);

            $filter = $request->filter . " Bulan";

            $param["monthlyLabels"] = $labels;
            $param["monthlyIncome"] = $monthlyIncomeData;
            $param["filter"] = $filter;
            $param["alat"] = $dataAlat;
            $param["dtrans"] = $dataDtrans;
            $param["request"] = $req;
            $param["mulai"] = $startDate;
            $param["selesai"] = $endDate1;
            $param["total"] = $totalData;
            $param["totalKotor"] = $totalData2;
            $param["increasePercentage"] = $increasePercentage;

        }
        else if ($request->filter == "36") {
            // dd("ye");
            $endDate1 = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate1)));

            // Set the start date to July (7 months before the current date)
            $startDate = date('Y-m-d', strtotime('-3 years', strtotime($endDate)));
            $startDate = date('Y-m-d', strtotime('first day of next month', strtotime($startDate)));

            $monthlyIncome = [];
            $total = [];
            $total2 = [];
            foreach (new DatePeriod(new DateTime($startDate), new DateInterval('P1D'), new DateTime($endDate)) as $date) {
                $dateStr = $date->format('Y');
                $monthlyIncome[$dateStr] = 0;
                $total[$dateStr] = 0;
                $total2[$dateStr] = 0;
            }
            // dd($monthlyIncome);
        
            foreach ($dataDtrans as $data) {
                $sewaDate = date('Y', strtotime($data->tanggal_sewa));
                // dd($sewaDate);
            
                if ($sewaDate >= $startDate && $sewaDate <= $endDate) {
                    $year = date('Y', strtotime($sewaDate));
                    // dd($day);
                    $monthlyIncome[$year] += 1;
                    $total[$year] += $data->total_komisi_tempat * 0.91;
                    $total2[$year] += $data->total_komisi_tempat;
                }
            }
            // dd($monthlyIncome);
            
            $labels = [];
            $currentDate = new DateTime($startDate);
            $endDateTime = new DateTime();

            while ($currentDate <= $endDateTime) {
                $labels[] = $currentDate->format('Y');
                $currentDate->add(new DateInterval('P1Y'));
            }

            $monthlyIncomeData = array_values($monthlyIncome);
            // dd($monthlyIncomeData);
            $totalData = array_values($total);
            $totalData2 = array_values($total2);

            $currentMonth = end($monthlyIncomeData);
            $previousMonth = prev($monthlyIncomeData);

            if ($previousMonth == 0) {
                $increasePercentage = ($currentMonth > 0) ? 100 : 0;
            } else {
                $increasePercentage = round((($currentMonth - $previousMonth) / $previousMonth) * 100);
            }

            $increasePercentage = number_format($increasePercentage, 2);

            $filter = $request->filter . " Bulan";

            $param["monthlyLabels"] = $labels;
            $param["monthlyIncome"] = $monthlyIncomeData;
            $param["filter"] = $filter;
            $param["alat"] = $dataAlat;
            $param["dtrans"] = $dataDtrans;
            $param["request"] = $req;
            $param["mulai"] = $startDate;
            $param["selesai"] = $endDate1;
            $param["total"] = $totalData;
            $param["totalKotor"] = $totalData2;
            $param["increasePercentage"] = $increasePercentage;

        }
        return view("tempat.laporan.laporanPerAlat")->with($param);
    }

    public function laporanPerAlatCetakPDF(Request $request) {
        $arr = explode(" ", $request->filter);
        $filter = $arr[0];

        $endDate1 = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate1)));
    
        // Mengambil tanggal satu bulan sebelumnya dari tanggal hari ini
        $startDate = date('Y-m-d', strtotime('-'.$filter.' month', strtotime($endDate)));
        $startDate = date('Y-m-d', strtotime('+1 day', strtotime($startDate)));

        $dataAlat = DB::table('alat_olahraga')
            ->where("id_alat","=",$request->id)
            ->get()
            ->first();
        
        $req = DB::table('request_permintaan')
            ->where("req_id_alat","=",$request->id)
            ->where("fk_id_tempat","=",Session::get("dataRole")->id_tempat)
            ->get()
            ->first();
        if ($req == null) {
            $req = DB::table('request_penawaran')
            ->where("req_id_alat","=",$request->id)
            ->where("fk_id_tempat","=",Session::get("dataRole")->id_tempat)
            ->get()
            ->first();
        }
    
        $dataDtrans = DB::table('dtrans')
            ->select("htrans.tanggal_sewa","dtrans.total_komisi_tempat")
            ->join("htrans","dtrans.fk_id_htrans","=","htrans.id_htrans")
            ->where("dtrans.fk_id_alat","=",$request->id)
            ->whereBetween('htrans.tanggal_sewa', [$startDate, $endDate])
            ->get();
        // dd($dataDtrans);
    
        if ($filter == "1" || $filter == "2") {
            $monthlyIncome = [];
            $total = [];
            $total2 = [];
            foreach (new DatePeriod(new DateTime($startDate), new DateInterval('P1D'), new DateTime($endDate)) as $date) {
                $dateStr = $date->format('Y-m-d');
                $monthlyIncome[$dateStr] = 0;
                $total[$dateStr] = 0;
                $total2[$dateStr] = 0;
            }
            // dd($monthlyIncome);
        
            foreach ($dataDtrans as $data) {
                $sewaDate = date('Y-m-d', strtotime($data->tanggal_sewa));
            
                if ($sewaDate >= $startDate && $sewaDate <= $endDate) {
                    $day = date('Y-m-d', strtotime($sewaDate));
                    // dd($day);
                    $monthlyIncome[$day] += 1;
                    $total[$day] += $data->total_komisi_tempat * 0.91;
                    $total2[$day] += $data->total_komisi_tempat;
                }
            }
            // dd($monthlyIncome);
            
            $labels = [];
            $currentDate = $startDate;
            
            while (strtotime($currentDate) <= strtotime($endDate)) {
                $labels[] = date('j M y', strtotime($currentDate));
                $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            }
        
            $monthlyIncomeData = array_values($monthlyIncome); // Mengambil nilai dari array asosiatif
            $totalData = array_values($total);
            $totalData2 = array_values($total2);

            $currentMonth = end($monthlyIncomeData);
            $previousMonth = prev($monthlyIncomeData);

            if ($previousMonth == 0) {
                $increasePercentage = ($currentMonth > 0) ? 100 : 0;
            } else {
                $increasePercentage = round((($currentMonth - $previousMonth) / $previousMonth) * 100);
            }

            $increasePercentage = number_format($increasePercentage, 2); // Format persentase dengan 2 desimal
            
            $filter = $filter . " Bulan";

            $param["monthlyLabels"] = $labels;
            $param["monthlyIncome"] = $monthlyIncomeData;
            $param["filter"] = $filter;
            $param["alat"] = $dataAlat;
            $param["dtrans"] = $dataDtrans;
            $param["request"] = $req;
            $param["mulai"] = $startDate;
            $param["selesai"] = $endDate1;
            $param["total"] = $totalData;
            $param["totalKotor"] = $totalData2;
            $param["increasePercentage"] = $increasePercentage;
        }
        else if ($filter >= "3" && $filter < "36") {
            $endDate1 = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate1)));

            // Set the start date to July (7 months before the current date)
            $startDate = date('Y-m-d', strtotime('-'.$filter.' months', strtotime($endDate)));
            $startDate = date('Y-m-d', strtotime('first day of next month', strtotime($startDate)));

            $monthlyIncome = [];
            $total = [];
            $total2 = [];
            foreach (new DatePeriod(new DateTime($startDate), new DateInterval('P1D'), new DateTime($endDate)) as $date) {
                $dateStr = $date->format('Y-m');
                $monthlyIncome[$dateStr] = 0;
                $total[$dateStr] = 0;
                $total2[$dateStr] = 0;
            }
            // dd($monthlyIncome);
        
            foreach ($dataDtrans as $data) {
                $sewaDate = date('Y-m', strtotime($data->tanggal_sewa));
            
                if ($sewaDate >= $startDate && $sewaDate <= $endDate) {
                    $day = date('Y-m', strtotime($sewaDate));
                    // dd($day);
                    $monthlyIncome[$day] += 1;
                    $total[$day] += $data->total_komisi_tempat * 0.91;
                    $total2[$day] += $data->total_komisi_tempat;
                }
            }
            // dd($total);
            
            $labels = [];
            $currentDate = new DateTime($startDate);
            $endDateTime = new DateTime();

            while ($currentDate <= $endDateTime) {
                $labels[] = $currentDate->format('M Y');
                $currentDate->add(new DateInterval('P1M'));
            }

            $monthlyIncomeData = array_values($monthlyIncome);
            $totalData = array_values($total);
            $totalData2 = array_values($total2);

            $currentMonth = end($monthlyIncomeData);
            $previousMonth = prev($monthlyIncomeData);

            if ($previousMonth == 0) {
                $increasePercentage = ($currentMonth > 0) ? 100 : 0;
            } else {
                $increasePercentage = round((($currentMonth - $previousMonth) / $previousMonth) * 100);
            }

            $increasePercentage = number_format($increasePercentage, 2);

            $filter = $filter . " Bulan";

            $param["monthlyLabels"] = $labels;
            $param["monthlyIncome"] = $monthlyIncomeData;
            $param["filter"] = $filter;
            $param["alat"] = $dataAlat;
            $param["dtrans"] = $dataDtrans;
            $param["request"] = $req;
            $param["mulai"] = $startDate;
            $param["selesai"] = $endDate1;
            $param["total"] = $totalData;
            $param["totalKotor"] = $totalData2;
            $param["increasePercentage"] = $increasePercentage;

        }
        else if ($filter == "36") {
            // dd("ye");
            $endDate1 = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+1 day', strtotime($endDate1)));

            // Set the start date to July (7 months before the current date)
            $startDate = date('Y-m-d', strtotime('-3 years', strtotime($endDate)));
            $startDate = date('Y-m-d', strtotime('first day of next month', strtotime($startDate)));

            $monthlyIncome = [];
            $total = [];
            $total2 = [];
            foreach (new DatePeriod(new DateTime($startDate), new DateInterval('P1D'), new DateTime($endDate)) as $date) {
                $dateStr = $date->format('Y');
                $monthlyIncome[$dateStr] = 0;
                $total[$dateStr] = 0;
                $total2[$dateStr] = 0;
            }
            // dd($monthlyIncome);
        
            foreach ($dataDtrans as $data) {
                $sewaDate = date('Y', strtotime($data->tanggal_sewa));
                // dd($sewaDate);
            
                if ($sewaDate >= $startDate && $sewaDate <= $endDate) {
                    $year = date('Y', strtotime($sewaDate));
                    // dd($day);
                    $monthlyIncome[$year] += 1;
                    $total[$year] += $data->total_komisi_tempat * 0.91;
                    $total2[$year] += $data->total_komisi_tempat;
                }
            }
            // dd($monthlyIncome);
            
            $labels = [];
            $currentDate = new DateTime($startDate);
            $endDateTime = new DateTime();

            while ($currentDate <= $endDateTime) {
                $labels[] = $currentDate->format('Y');
                $currentDate->add(new DateInterval('P1Y'));
            }

            $monthlyIncomeData = array_values($monthlyIncome);
            // dd($monthlyIncomeData);
            $totalData = array_values($total);
            $totalData2 = array_values($total2);

            $currentMonth = end($monthlyIncomeData);
            $previousMonth = prev($monthlyIncomeData);

            if ($previousMonth == 0) {
                $increasePercentage = ($currentMonth > 0) ? 100 : 0;
            } else {
                $increasePercentage = round((($currentMonth - $previousMonth) / $previousMonth) * 100);
            }

            $increasePercentage = number_format($increasePercentage, 2);

            $filter = $filter . " Bulan";

            $param["monthlyLabels"] = $labels;
            $param["monthlyIncome"] = $monthlyIncomeData;
            $param["filter"] = $filter;
            $param["alat"] = $dataAlat;
            $param["dtrans"] = $dataDtrans;
            $param["request"] = $req;
            $param["mulai"] = $startDate;
            $param["selesai"] = $endDate1;
            $param["total"] = $totalData;
            $param["totalKotor"] = $totalData2;
            $param["increasePercentage"] = $increasePercentage;

        }
        // dd($monthlyIncomeData);
        $pdf = PDF::loadview('tempat.laporan.laporanPerAlat_pdf',['monthlyIncome'=> $monthlyIncomeData, 'monthlyLabels' => $labels, 'total' => $totalData, 'totalKotor' => $totalData2]);
        // return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanLapangan() {
        $role = Session::get("dataRole")->id_tempat;

        $allData = DB::table('lapangan_olahraga')
                    ->select(
                        "lapangan_olahraga.id_lapangan",
                        "lapangan_olahraga.nama_lapangan",
                        DB::raw('COUNT(htrans.id_htrans) as total_sewa'),
                        "lapangan_olahraga.harga_sewa_lapangan",
                        DB::raw('SUM(htrans.durasi_sewa) as total_durasi'),
                        "lapangan_olahraga.status_lapangan",
                        DB::raw('SUM(htrans.subtotal_lapangan) as total_pendapatan'),
                        DB::raw('SUM(extend_htrans.durasi_extend) as durasi_ext'),
                        DB::raw('SUM(extend_htrans.subtotal_lapangan) as subtotal_ext'),
                    )
                    ->leftJoin("htrans", "lapangan_olahraga.id_lapangan", "=", "htrans.fk_id_lapangan")
                    ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                    ->where("htrans.status_trans","!=","Dibatalkan")
                    ->where("htrans.status_trans","!=","Ditolak")
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
        // dd($dataHtrans);
        foreach ($dataHtrans as $data) {
            $year = date('Y', strtotime($data->tanggal_sewa));
            $bulan = date('m', strtotime($data->tanggal_sewa));
    
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] = $allData->count();
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

    public function lapanganCetakPDF() {
        $role = Session::get("dataRole")->id_tempat;

        $data = DB::table('lapangan_olahraga')
                ->select(
                    "lapangan_olahraga.id_lapangan",
                    "lapangan_olahraga.nama_lapangan",
                    DB::raw('COUNT(htrans.id_htrans) as total_sewa'),
                    "lapangan_olahraga.harga_sewa_lapangan",
                    DB::raw('SUM(htrans.durasi_sewa) as total_durasi'),
                    "lapangan_olahraga.status_lapangan",
                    DB::raw('SUM(htrans.subtotal_lapangan) as total_pendapatan'),
                    DB::raw('SUM(extend_htrans.durasi_extend) as durasi_ext'),
                    DB::raw('SUM(extend_htrans.subtotal_lapangan) as subtotal_ext'),
                )
                ->leftJoin("htrans", "lapangan_olahraga.id_lapangan", "=", "htrans.fk_id_lapangan")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->where("htrans.status_trans","!=","Dibatalkan")
                ->where("htrans.status_trans","!=","Ditolak")
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

    //-------------------------------------------------------------------------------------------

    //LAPORAN ADMIN
    public function laporanPendapatanAdmin() {
        $coba = DB::table('htrans')
                ->select(
                    "htrans.kode_trans",
                    "htrans.pendapatan_website_lapangan as pendapatan_lapangan",
                    DB::raw('SUM(dtrans.pendapatan_website_alat) as pendapatan_alat'),
                    DB::raw('COUNT(dtrans.id_dtrans) as jumlah_alat'),
                    "htrans.tanggal_trans",
                    "lapangan_olahraga.nama_lapangan",
                    "extend_htrans.pendapatan_website_lapangan as lapangan_ext",
                    DB::raw('SUM(extend_dtrans.pendapatan_website_alat) as alat_ext')
                )
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->join("lapangan_olahraga","htrans.fk_id_lapangan","=","lapangan_olahraga.id_lapangan")
                ->where("htrans.status_trans","=","Selesai")
                ->groupBy(
                    "htrans.kode_trans",
                    "htrans.pendapatan_website_lapangan",
                    "htrans.tanggal_trans",
                    "lapangan_olahraga.nama_lapangan",
                    "extend_htrans.pendapatan_website_lapangan"
                )
                ->get();
        // dd($coba);

        $monthlyIncome = [];
        for ($i=1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        foreach ($coba as $data) {
            $bulan = date('m', strtotime($data->tanggal_trans));
            $year = date('Y', strtotime($data->tanggal_trans));
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += ($data->pendapatan_lapangan + $data->pendapatan_alat) + ($data->lapangan_ext + $data->alat_ext);
            }
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        $currentYear = date('Y');
        $yearlyIncome = [
            $currentYear - 4 => 0,
            $currentYear - 3 => 0,
            $currentYear - 2 => 0,
            $currentYear - 1 => 0,
            $currentYear => 0
        ];

        // Menghitung pendapatan tahunan
        foreach ($coba as $data) {
            $year = date('Y', strtotime($data->tanggal_trans));
            if (isset($yearlyIncome[$year])) {
                $yearlyIncome[$year] += ($data->pendapatan_lapangan + $data->pendapatan_alat) + ($data->lapangan_ext + $data->alat_ext);
            }
        }

        // Mengkonversi $yearlyIncome ke array biasa
        $yearlyIncomeData = array_values($yearlyIncome);
        $param["yearlyIncome"] = $yearlyIncomeData;

        $param["trans"] = $coba;
        $param["tanggal_mulai"] = null;
        $param["tanggal_selesai"] = null;
        $param["monthlyIncome"] = $monthlyIncomeData;
        return view("admin.laporan.laporanPendapatan")->with($param);
    }

    public function fiturPendapatanAdmin(Request $request) {
        $request->validate([
            "tanggal_mulai" => 'required',
            "tanggal_selesai" => 'required'
        ],[
            "tanggal_mulai.required" => "tanggal mulai tidak boleh kosong!",
            "tanggal_selesai.required" => "tanggal selesai tidak boleh kosong!"
        ]);

        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_selesai');

        $date_mulai = new DateTime($startDate);
        $date_selesai = new DateTime($endDate);
        
        if ($date_selesai <= $date_mulai) {
            return redirect()->back()->with("error", "Tanggal selesai tidak sesuai!");
        }

        $coba = DB::table('htrans')
                ->select(
                    "htrans.kode_trans",
                    "htrans.pendapatan_website_lapangan as pendapatan_lapangan",
                    DB::raw('SUM(dtrans.pendapatan_website_alat) as pendapatan_alat'),
                    DB::raw('COUNT(dtrans.id_dtrans) as jumlah_alat'),
                    "htrans.tanggal_trans",
                    "lapangan_olahraga.nama_lapangan",
                    "extend_htrans.pendapatan_website_lapangan as lapangan_ext",
                    DB::raw('SUM(extend_dtrans.pendapatan_website_alat) as alat_ext')
                )
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->join("lapangan_olahraga","htrans.fk_id_lapangan","=","lapangan_olahraga.id_lapangan")
                ->where("htrans.status_trans","=","Selesai")
                ->whereBetween('htrans.tanggal_trans', [$startDate, $endDate])
                ->groupBy(
                    "htrans.kode_trans",
                    "htrans.pendapatan_website_lapangan",
                    "htrans.tanggal_trans",
                    "lapangan_olahraga.nama_lapangan",
                    "extend_htrans.pendapatan_website_lapangan"
                )
                ->get();
        // dd($coba);

        $monthlyIncome = [];
        for ($i=1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        foreach ($coba as $data) {
            $bulan = date('m', strtotime($data->tanggal_trans));
            $year = date('Y', strtotime($data->tanggal_trans));
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += ($data->pendapatan_lapangan + $data->pendapatan_alat) + ($data->lapangan_ext + $data->alat_ext);
            }
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        $currentYear = date('Y');
        $yearlyIncome = [
            $currentYear - 4 => 0,
            $currentYear - 3 => 0,
            $currentYear - 2 => 0,
            $currentYear - 1 => 0,
            $currentYear => 0
        ];

        // Menghitung pendapatan tahunan
        foreach ($coba as $data) {
            $year = date('Y', strtotime($data->tanggal_trans));
            if (isset($yearlyIncome[$year])) {
                $yearlyIncome[$year] += ($data->pendapatan_lapangan + $data->pendapatan_alat) + ($data->lapangan_ext + $data->alat_ext);
            }
        }

        // Mengkonversi $yearlyIncome ke array biasa
        $yearlyIncomeData = array_values($yearlyIncome);
        $param["yearlyIncome"] = $yearlyIncomeData;

        $param["trans"] = $coba;
        $param["tanggal_mulai"] = $startDate;
        $param["tanggal_selesai"] = $endDate;
        $param["monthlyIncome"] = $monthlyIncomeData;
        return view("admin.laporan.laporanPendapatan")->with($param);
    }

    public function pendapatanAdminCetakPDF() {
        $data = DB::table('htrans')
                ->select(
                    "htrans.kode_trans",
                    "htrans.pendapatan_website_lapangan as pendapatan_lapangan",
                    DB::raw('SUM(dtrans.pendapatan_website_alat) as pendapatan_alat'),
                    DB::raw('COUNT(dtrans.id_dtrans) as jumlah_alat'),
                    "htrans.tanggal_trans",
                    "lapangan_olahraga.nama_lapangan",
                    "extend_htrans.pendapatan_website_lapangan as lapangan_ext",
                    DB::raw('SUM(extend_dtrans.pendapatan_website_alat) as alat_ext')
                )
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->join("lapangan_olahraga","htrans.fk_id_lapangan","=","lapangan_olahraga.id_lapangan")
                ->where("htrans.status_trans","=","Selesai")
                ->groupBy(
                    "htrans.kode_trans",
                    "htrans.pendapatan_website_lapangan",
                    "htrans.tanggal_trans",
                    "lapangan_olahraga.nama_lapangan",
                    "extend_htrans.pendapatan_website_lapangan"
                )
                ->get();
        
        $pdf = PDF::loadview('admin.laporan.laporanPendapatan_pdf',['data'=>$data, 'tanggal_mulai'=>null, 'tanggal_selesai'=>null]);
        // return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function pendapatanAdminCetakPDF2(Request $request) {
        $data = DB::table('htrans')
                ->select(
                    "htrans.kode_trans",
                    "htrans.pendapatan_website_lapangan as pendapatan_lapangan",
                    DB::raw('SUM(dtrans.pendapatan_website_alat) as pendapatan_alat'),
                    DB::raw('COUNT(dtrans.id_dtrans) as jumlah_alat'),
                    "htrans.tanggal_trans",
                    "lapangan_olahraga.nama_lapangan",
                    "extend_htrans.pendapatan_website_lapangan as lapangan_ext",
                    DB::raw('SUM(extend_dtrans.pendapatan_website_alat) as alat_ext')
                )
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->join("lapangan_olahraga","htrans.fk_id_lapangan","=","lapangan_olahraga.id_lapangan")
                ->where("htrans.status_trans","=","Selesai")
                ->whereBetween('htrans.tanggal_trans', [$request->mulai, $request->selesai])
                ->groupBy(
                    "htrans.kode_trans",
                    "htrans.pendapatan_website_lapangan",
                    "htrans.tanggal_trans",
                    "lapangan_olahraga.nama_lapangan",
                    "extend_htrans.pendapatan_website_lapangan"
                )
                ->get();
        
        $pdf = PDF::loadview('admin.laporan.laporanPendapatan_pdf',['data'=>$data, 'tanggal_mulai'=>$request->mulai, 'tanggal_selesai'=>$request->selesai]);
        // return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function laporanAlatAdmin() {
        $dtrans = DB::table('dtrans')->where("deleted_at","=",null)->get();

        $coba = DB::table('alat_olahraga')
                ->select(
                    "alat_olahraga.id_alat",
                    "alat_olahraga.nama_alat",
                    DB::raw('COUNT(htrans.id_htrans) as total_sewa'),
                    "dtrans.harga_sewa_alat",
                    "alat_olahraga.status_alat",
                    DB::raw('SUM(dtrans.pendapatan_website_alat) as total_pendapatan'),
                    DB::raw('SUM(extend_dtrans.pendapatan_website_alat) as pendapatan_ext'),
                )
                ->leftJoin("dtrans", "alat_olahraga.id_alat", "=", "dtrans.fk_id_alat")
                ->leftJoin("htrans", "dtrans.fk_id_htrans", "=", "htrans.id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->where("htrans.status_trans","!=","Dibatalkan")
                ->where("htrans.status_trans","!=","Ditolak")
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

    public function alatAdminCetakPDF() {
        $data = DB::table('alat_olahraga')
                ->select(
                    "alat_olahraga.id_alat",
                    "alat_olahraga.nama_alat",
                    DB::raw('COUNT(htrans.id_htrans) as total_sewa'),
                    "dtrans.harga_sewa_alat",
                    "alat_olahraga.status_alat",
                    DB::raw('SUM(dtrans.pendapatan_website_alat) as total_pendapatan'),
                    DB::raw('SUM(extend_dtrans.pendapatan_website_alat) as pendapatan_ext'),
                )
                ->leftJoin("dtrans", "alat_olahraga.id_alat", "=", "dtrans.fk_id_alat")
                ->leftJoin("htrans", "dtrans.fk_id_htrans", "=", "htrans.id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->where("htrans.status_trans","!=","Dibatalkan")
                ->where("htrans.status_trans","!=","Ditolak")
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

    public function laporanTempatAdmin() {
        $coba = DB::table('pihak_tempat')
                ->select(
                    "pihak_tempat.id_tempat",
                    "pihak_tempat.nama_tempat",
                    DB::raw('COUNT(DISTINCT htrans.id_htrans) as jumlah_trans'),
                    DB::raw('SUM(DISTINCT htrans.pendapatan_website_lapangan) as total_lapangan'),
                    // DB::raw('SUM(DISTINCT dtrans.total_komisi_tempat) as total_alat'),
                    DB::raw('COUNT(DISTINCT lapangan_olahraga.id_lapangan) as jumlah_lapangan'),
                    DB::raw('SUM(DISTINCT extend_htrans.pendapatan_website_lapangan) as lapangan_ext'),
                    // DB::raw('SUM(DISTINCT extend_dtrans.total_komisi_tempat) as alat_ext'),
                )
                ->leftJoin("lapangan_olahraga","pihak_tempat.id_tempat","=","lapangan_olahraga.pemilik_lapangan")
                ->leftJoin("htrans", "pihak_tempat.id_tempat", "=", "htrans.fk_id_tempat")
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->where("htrans.status_trans","!=","Dibatalkan")
                ->where("htrans.status_trans","!=","Ditolak")
                ->groupBy(
                    "pihak_tempat.id_tempat",
                    "pihak_tempat.nama_tempat"
                )
                ->get();
        // dd($coba);

        $monthlyIncome = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        foreach ($coba as $data) {
            $dataHtrans = DB::table('htrans')->where("fk_id_tempat","=",$data->id_tempat)->get();
            if (!$dataHtrans->isEmpty()) {
                $year = date('Y', strtotime($dataHtrans->first()->tanggal_sewa));
                $bulan = date('m', strtotime($dataHtrans->first()->tanggal_sewa));
        
                if ($year == date('Y')) {
                    $monthlyIncome[(int)$bulan] += $coba->count();
                }
            }
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        // $monthlyIncomeData = array_values($monthlyIncome);

        $param["tempat"] = $coba;
        $param["monthlyIncome"] = $monthlyIncomeData;
        // $param["yearlyMonthlyIncome"] = $yearlyMonthlyIncome;
        return view("admin.laporan.laporanTempat")->with($param);
    }

    public function tempatAdminCetakPDF() {
        $data = DB::table('pihak_tempat')
                ->select(
                    "pihak_tempat.id_tempat",
                    "pihak_tempat.nama_tempat",
                    DB::raw('COUNT(DISTINCT htrans.id_htrans) as jumlah_trans'),
                    DB::raw('SUM(DISTINCT htrans.pendapatan_website_lapangan) as total_lapangan'),
                    // DB::raw('SUM(DISTINCT dtrans.total_komisi_tempat) as total_alat'),
                    DB::raw('COUNT(DISTINCT lapangan_olahraga.id_lapangan) as jumlah_lapangan'),
                    DB::raw('SUM(DISTINCT extend_htrans.pendapatan_website_lapangan) as lapangan_ext'),
                    // DB::raw('SUM(DISTINCT extend_dtrans.total_komisi_tempat) as alat_ext'),
                )
                ->leftJoin("lapangan_olahraga","pihak_tempat.id_tempat","=","lapangan_olahraga.pemilik_lapangan")
                ->leftJoin("htrans", "pihak_tempat.id_tempat", "=", "htrans.fk_id_tempat")
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->where("htrans.status_trans","!=","Dibatalkan")
                ->where("htrans.status_trans","!=","Ditolak")
                ->groupBy(
                    "pihak_tempat.id_tempat",
                    "pihak_tempat.nama_tempat"
                )
                ->get();
        $pdf = PDF::loadview('admin.laporan.laporanTempat_pdf',['data'=>$data]);
        // return $pdf->download('laporan-pendapatan-pdf');
        return $pdf->stream();
    }

    public function berandaAdmin() {
        $req = new komplainRequest();
        $param["jumlahKomplainReq"] = $req->count_all_data_admin();
        $komtrans = new komplainTrans();
        $param["jumlahKomplainTrans"] = $komtrans->count_all_data_admin();
        $trans = new htrans();
        $param["jumlahTransaksi"] = $trans->count_all_data_admin();

        $coba = DB::table('htrans')
                ->select(
                    "htrans.kode_trans",
                    "htrans.pendapatan_website_lapangan as pendapatan_lapangan",
                    DB::raw('SUM(dtrans.pendapatan_website_alat) as pendapatan_alat'),
                    DB::raw('COUNT(dtrans.id_dtrans) as jumlah_alat'),
                    "htrans.tanggal_trans",
                    "lapangan_olahraga.nama_lapangan",
                    "extend_htrans.pendapatan_website_lapangan as lapangan_ext",
                    DB::raw('SUM(extend_dtrans.pendapatan_website_alat) as alat_ext')
                )
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->join("lapangan_olahraga","htrans.fk_id_lapangan","=","lapangan_olahraga.id_lapangan")
                ->where("htrans.status_trans","=","Selesai")
                ->groupBy(
                    "htrans.kode_trans",
                    "htrans.pendapatan_website_lapangan",
                    "htrans.tanggal_trans",
                    "lapangan_olahraga.nama_lapangan",
                    "extend_htrans.pendapatan_website_lapangan"
                )
                ->get();
        // dd($coba);

        $monthlyIncome = [];
        for ($i=1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        foreach ($coba as $data) {
            $bulan = date('m', strtotime($data->tanggal_trans));
            $year = date('Y', strtotime($data->tanggal_trans));
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += ($data->pendapatan_lapangan + $data->pendapatan_alat) + ($data->lapangan_ext + $data->alat_ext);
            }
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }
        $param["monthlyIncome"] = $monthlyIncomeData;

        // Inisialisasi pendapatan tahunan untuk 5 tahun terakhir
        $currentYear = date('Y');
        $yearlyIncome = [
            $currentYear - 4 => 0,
            $currentYear - 3 => 0,
            $currentYear - 2 => 0,
            $currentYear - 1 => 0,
            $currentYear => 0
        ];

        // Menghitung pendapatan tahunan
        foreach ($coba as $data) {
            $year = date('Y', strtotime($data->tanggal_trans));
            if (isset($yearlyIncome[$year])) {
                $yearlyIncome[$year] += ($data->pendapatan_lapangan + $data->pendapatan_alat) + ($data->lapangan_ext + $data->alat_ext);
            }
        }

        // Mengkonversi $yearlyIncome ke array biasa
        $yearlyIncomeData = array_values($yearlyIncome);
        $param["yearlyIncome"] = $yearlyIncomeData;

        return view("admin.beranda")->with($param);
    }

    public function berandaPemilik() {
        $id = Session::get("dataRole")->id_pemilik;
        $alat = new alatOlahraga();
        $param["jumlahAlat"] = $alat->count_all_data($id);
        $minta = new requestPermintaan();
        $param["jumlahPermintaan"] = $minta->count_all_data_pemilik($id);
        $tawar = new requestPenawaran();
        $param["jumlahPenawaran"] = $tawar->count_all_data_pemilik($id);

        $coba = DB::table('htrans')
                ->select("alat_olahraga.nama_alat","alat_olahraga.komisi_alat","htrans.durasi_sewa","htrans.tanggal_trans","dtrans.total_komisi_pemilik", "dtrans.pendapatan_website_alat", "extend_htrans.durasi_extend", "extend_dtrans.total_komisi_pemilik as komisi_extend","extend_dtrans.pendapatan_website_alat as pendapatan_extend")
                ->leftJoin("dtrans","htrans.id_htrans","=","dtrans.fk_id_htrans")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
                ->join("alat_olahraga","dtrans.fk_id_alat","=","alat_olahraga.id_alat")
                ->where("dtrans.fk_id_pemilik","=",$id)
                ->where("htrans.status_trans","=","Selesai")
                ->get();
        // dd($coba);

        $monthlyIncome = [];
        for ($i=1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        foreach ($coba as $data) {
            $bulan = date('m', strtotime($data->tanggal_trans));
            $year = date('Y', strtotime($data->tanggal_trans));
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += ($data->total_komisi_pemilik - $data->pendapatan_website_alat) + ($data->komisi_extend - $data->pendapatan_extend);
            }
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }
        $param["monthlyIncome"] = $monthlyIncomeData;

        // Inisialisasi pendapatan tahunan untuk 5 tahun terakhir
        $currentYear = date('Y');
        $yearlyIncome = [
            $currentYear - 4 => 0,
            $currentYear - 3 => 0,
            $currentYear - 2 => 0,
            $currentYear - 1 => 0,
            $currentYear => 0
        ];

        // Menghitung pendapatan tahunan
        foreach ($coba as $data) {
            $year = date('Y', strtotime($data->tanggal_trans));
            if (isset($yearlyIncome[$year])) {
                $yearlyIncome[$year] += ($data->total_komisi_pemilik - $data->pendapatan_website_alat) + ($data->komisi_extend - $data->pendapatan_extend);
            }
        }

        // Mengkonversi $yearlyIncome ke array biasa
        $yearlyIncomeData = array_values($yearlyIncome);
        $param["yearlyIncome"] = $yearlyIncomeData;

        return view("pemilik.beranda")->with($param);
    }

    public function berandaTempat() {
        $role = Session::get("dataRole")->id_tempat;
        $trans = new htrans();
        $param["jumlahTransaksi"] = $trans->count_all_data_tempat($role);
        $minta = new requestPermintaan();
        $param["jumlahPermintaan"] = $minta->count_all_data_tempat($role);
        $tawar = new requestPenawaran();
        $param["jumlahPenawaran"] = $tawar->count_all_data_tempat($role);

        $coba = DB::table('htrans')
            ->select(
                'htrans.id_htrans',
                "htrans.kode_trans",
                DB::raw('SUM(dtrans.total_komisi_tempat) as total_komisi'),
                'htrans.subtotal_lapangan',
                'htrans.tanggal_trans',
                DB::raw('COUNT(dtrans.id_dtrans) as alat'),
                "lapangan_olahraga.nama_lapangan",
                "htrans.pendapatan_website_lapangan",
                DB::raw('SUM(extend_dtrans.total_komisi_tempat) as komisi_extend'),
                'extend_htrans.subtotal_lapangan as subtotal_ext',
                "extend_htrans.pendapatan_website_lapangan as pendapatan_ext",
                'extend_htrans.id_extend_htrans'
            )
            ->leftJoin("dtrans", "htrans.id_htrans", "=", "dtrans.fk_id_htrans")
            ->join("lapangan_olahraga", "htrans.fk_id_lapangan", "=", "lapangan_olahraga.id_lapangan")
            ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
            ->leftJoin("extend_dtrans","dtrans.id_dtrans","=","extend_dtrans.fk_id_dtrans")
            ->where("htrans.status_trans","=","Selesai")
            ->where("htrans.fk_id_tempat", "=", $role)
            ->groupBy(
                'htrans.id_htrans',
                'htrans.kode_trans',
                'htrans.subtotal_lapangan',
                'htrans.tanggal_trans',
                "lapangan_olahraga.nama_lapangan",
                "htrans.pendapatan_website_lapangan",
                'extend_htrans.subtotal_lapangan',
                "extend_htrans.pendapatan_website_lapangan",
                'extend_htrans.id_extend_htrans'
            )
            ->get();
        // dd($coba);
        $monthlyIncome = [];
        for ($i=1; $i <= 12; $i++) {
            $monthlyIncome[$i] = 0; // inisialisasi pendapatan setiap bulan dengan 0
        }

        foreach ($coba as $data) {
            // $dataDtrans = DB::table('dtrans')->where("fk_id_htrans","=",$data->id_htrans)->sum("total_komisi_tempat");
            // $dataExtendDtrans = DB::table('extend_dtrans')->where("fk_id_extend_htrans","=",$data->id_extend_htrans)->sum("total_komisi_tempat");
            // dd($data->id_extend_htrans);
            $bulan = date('m', strtotime($data->tanggal_trans));
            $year = date('Y', strtotime($data->tanggal_trans));
            if ($year == date('Y')) {
                $monthlyIncome[(int)$bulan] += ($data->subtotal_lapangan + $data->total_komisi - $data->pendapatan_website_lapangan) + ($data->subtotal_ext + $data->komisi_extend - $data->pendapatan_ext);
            }
        }

        // Mengkonversi $monthlyIncome ke array biasa
        $monthlyIncomeData = [];
        foreach ($monthlyIncome as $income) {
            $monthlyIncomeData[] = $income;
        }

        $param["monthlyIncome"] = $monthlyIncomeData;

        // Inisialisasi pendapatan tahunan untuk 5 tahun terakhir
        $currentYear = date('Y');
        $yearlyIncome = [
            $currentYear - 4 => 0,
            $currentYear - 3 => 0,
            $currentYear - 2 => 0,
            $currentYear - 1 => 0,
            $currentYear => 0
        ];

        // Menghitung pendapatan tahunan
        foreach ($coba as $data) {
            $year = date('Y', strtotime($data->tanggal_trans));
            if (isset($yearlyIncome[$year])) {
                $yearlyIncome[$year] += ($data->subtotal_lapangan + $data->total_komisi - $data->pendapatan_website_lapangan) + ($data->subtotal_ext + $data->komisi_extend - $data->pendapatan_ext);
            }
        }

        // Mengkonversi $yearlyIncome ke array biasa
        $yearlyIncomeData = array_values($yearlyIncome);
        $param["yearlyIncome"] = $yearlyIncomeData;

        return view("tempat.beranda")->with($param);
    }
}
