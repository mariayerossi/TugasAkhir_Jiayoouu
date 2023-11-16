<?php

use App\Http\Controllers\AlatOlahraga;
use App\Http\Controllers\KategoriOlahraga;
use App\Http\Controllers\KerusakanAlat;
use App\Http\Controllers\KomplainRequest;
use App\Http\Controllers\KomplainTrans;
use App\Http\Controllers\LapanganOlahraga;
use App\Http\Controllers\Laporan;
use App\Http\Controllers\LoginRegister;
use App\Http\Controllers\Negosiasi;
use App\Http\Controllers\NotifikasiEmail;
use App\Http\Controllers\Rating;
use App\Http\Controllers\RequestPenawaran;
use App\Http\Controllers\RequestPermintaan;
use App\Http\Controllers\Saldo;
use App\Http\Controllers\SewaSendiri;
use App\Http\Controllers\Transaksi;
use App\Http\Middleware\CekAdmin;
use App\Http\Middleware\CekCustomer;
use App\Http\Middleware\CekPemilik;
use App\Http\Middleware\CekTempat;
use App\Http\Middleware\Guest;
use App\Models\alatOlahraga as ModelsAlatOlahraga;
use App\Models\htrans as ModelsHtrans;
use App\Models\dtrans as ModelsDtrans;
use App\Models\slotWaktu as ModelsSlotWaktu;
use App\Models\customer;
use App\Models\filesAlatOlahraga;
use App\Models\filesLapanganOlahraga;
use App\Models\kategori;
use App\Models\lapanganOlahraga as ModelsLapanganOlahraga;
use App\Models\negosiasi as ModelsNegosiasi;
use App\Models\komplainRequest as ModelsKomplainRequest;
use App\Models\filesKomplainReq as ModelsFilesKomplainReq;
use App\Models\komplainTrans as ModelsKomplainTrans;
use App\Models\filesKomplainTrans as ModelsFilesKomplainTrans;
use App\Models\pemilikAlat;
use App\Models\pihakTempat;
use App\Models\registerTempat;
use App\Models\requestPenawaran as ModelsRequestPenawaran;
use App\Models\requestPermintaan as ModelsRequestPermintaan;
use App\Models\sewaSendiri as ModelsSewaSendiri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});
Route::get("/daftarLapangan", [LapanganOlahraga::class, "cariLapangan2"]);
Route::get("/detailLapangan/{id}", function ($id) {
    $kat = new kategori();
    $param["kategori"] = $kat->get_all_data();
    $lapa = new ModelsLapanganOlahraga();
    $param["lapangan"] = $lapa->get_all_data_by_id($id);
    $files = new filesLapanganOlahraga();
    $param["files"] = $files->get_all_data($id);
    $slot = new ModelsSlotWaktu();
    $param["slot"] = $slot->get_all_data_by_lapangan($id);

    $per = new ModelsRequestPermintaan();
    $param["permintaan"] = $per->get_all_data_by_lapangan($id);
    $pen = new ModelsRequestPenawaran();
    $param["penawaran"] = $pen->get_all_data_by_lapangan($id);
    $sewa = new ModelsSewaSendiri();
    $param["sewa"] = $sewa->get_all_data_by_lapangan($id);
    return view("detailLapangan")->with($param);
});
Route::get("/searchLapangan", [LapanganOlahraga::class, "searchLapangan2"]);

// -------------------------------
// TAMPILAN LOGIN REGISTER
// -------------------------------
Route::view("/register", "register")->middleware([Guest::class]);
Route::view("/login", "login")->middleware([Guest::class]);
Route::view("/registerTempat", "tempat.registerTempat")->middleware([Guest::class]);
Route::view("/registerPemilik", "pemilik.registerPemilik")->middleware([Guest::class]);

//verifikasi
Route::get("/verifikasiUser/{id}", [LoginRegister::class, "verifikasiUser"]);
Route::get("/verifikasiPemilik/{id}", [LoginRegister::class, "verifikasiPemilik"]);
Route::get("/verifikasiTempat/{id}", [LoginRegister::class, "verifikasiTempat"]);

// -------------------------------
// PROSES LOGIN REGISTER
// -------------------------------
Route::post("/registerUser", [LoginRegister::class, "registerUser"]);
Route::post("/registerPemilik", [LoginRegister::class, "registerPemilik"]);
Route::post("/registerTempat", [LoginRegister::class, "registerTempat"]);
Route::post("/login", [LoginRegister::class, "login"]);
Route::get("/logout", [LoginRegister::class, "logout"]);

// -------------------------------
// HALAMAN ADMIN
// -------------------------------
Route::prefix("/admin")->group(function(){
    // Route::get("/beranda", function () {
    //     $req = new ModelsKomplainRequest();
    //     $param["jumlahKomplainReq"] = $req->count_all_data_admin();
    //     $komtrans = new ModelsKomplainTrans();
    //     $param["jumlahKomplainTrans"] = $komtrans->count_all_data_admin();
    //     $trans = new ModelsHtrans();
    //     $param["jumlahTransaksi"] = $trans->count_all_data_admin();
    //     return view("admin.beranda")->with($param);
    // })->middleware([CekAdmin::class]);
    Route::get("/beranda", [Laporan::class, "berandaAdmin"])->middleware([CekAdmin::class]);
    Route::get("/registrasi_tempat", function () {
        $reg = new registerTempat();
        $param["register"] = $reg->get_all_data();
        return view("admin.registrasi_tempat")->with($param);
    })->middleware([CekAdmin::class]);
    Route::get("/konfirmasiTempat/{id}", [LoginRegister::class, "konfirmasiTempat"]);
    Route::get("/tolakKonfirmasiTempat/{id}", [LoginRegister::class, "tolakKonfirmasiTempat"]);
    Route::get("/masterKategori", function () {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        return view("admin.masterKategori")->with($param);
    })->middleware([CekAdmin::class]);
    Route::post("/tambahKategori", [KategoriOlahraga::class, "tambahKategori"]);
    Route::get("/hapusKategori/{id}", [KategoriOlahraga::class, "hapusKategori"]);
    Route::get("/daftarCustomer", function () {
        $cust = new customer();
        $param["customer"] = $cust->get_all_data();
        return view("admin.users.daftarCustomer")->with($param);
    })->middleware([CekAdmin::class]);
    Route::get("/daftarPemilik", function () {
        $pemilik = new pemilikAlat();
        $param["pemilik"] = $pemilik->get_all_data();
        return view("admin.users.daftarPemilik")->with($param);
    })->middleware([CekAdmin::class]);
    Route::get("/daftarTempat", function () {
        $tempat = new pihakTempat();
        $param["tempat"] = $tempat->get_all_data();
        return view("admin.users.daftarTempat")->with($param);
    })->middleware([CekAdmin::class]);

    //Bagian Alat Olahraga
    Route::prefix("/alat")->group(function(){
        // Route::get("/cariAlat", function () {
        //     $kat = new kategori();
        //     $param["kategori"] = $kat->get_all_data();
        //     $alat = new ModelsAlatOlahraga();
        //     $param["alat"] = $alat->get_all_data_for_admin();
        //     $files = new filesAlatOlahraga();
        //     $param["files"] = $files;
        //     return view("admin.produk.cariAlat")->with($param);
        // })->middleware([CekAdmin::class]);
        Route::get("/cariAlat", [AlatOlahraga::class, "cariAlatAdmin"])->middleware([CekAdmin::class]);
        Route::get("/detailAlatUmum/{id}", function ($id) {//melihat detail alat olahraga milik org lain
            $alat = new ModelsAlatOlahraga();
            $param["alat"] = $alat->get_all_data_by_id($id);
            $files = new filesAlatOlahraga();
            $param["files"] = $files->get_all_data($id);
            return view("admin.produk.detailAlatUmum")->with($param);
        })->middleware([CekAdmin::class]);
        Route::get("/searchAlat", [AlatOlahraga::class, "searchAlat"]);
    });

    //Bagian Lapangan Olahraga
    Route::prefix("/lapangan")->group(function(){
        // Route::get("/cariLapangan", function () {
        //     $kat = new kategori();
        //     $param["kategori"] = $kat->get_all_data();
        //     $lapa = new ModelsLapanganOlahraga();
        //     $param["lapangan"] = $lapa->get_all_data_for_admin();
        //     $files = new filesLapanganOlahraga();
        //     $param["files"] = $files;
        //     return view("admin.produk.cariLapangan")->with($param);
        // })->middleware([CekAdmin::class]);
        Route::get("/cariLapangan", [LapanganOlahraga::class, "cariLapanganAdmin"])->middleware([CekAdmin::class]);
        Route::get("/searchLapangan", [LapanganOlahraga::class, "searchLapangan"]);
        Route::get("/detailLapanganUmum/{id}", function ($id) {//melihat detail lapangan olahraga milik org lain
            $lapa = new ModelsLapanganOlahraga();
            $param["lapangan"] = $lapa->get_all_data_by_id($id);
            $files = new filesLapanganOlahraga();
            $param["files"] = $files->get_all_data($id);
            $slot = new ModelsSlotWaktu();
            $param["slot"] = $slot->get_all_data_by_lapangan($id);

            $per = new ModelsRequestPermintaan();
            $param["permintaan"] = $per->get_all_data_by_lapangan($id);
            $pen = new ModelsRequestPenawaran();
            $param["penawaran"] = $pen->get_all_data_by_lapangan($id);
            $sewa = new ModelsSewaSendiri();
            $param["sewa"] = $sewa->get_all_data_by_lapangan($id);
            return view("admin.produk.detailLapanganUmum")->with($param);
        })->middleware([CekAdmin::class]);
    });

    Route::prefix("/transaksi")->group(function(){
        Route::get("/daftarTransaksi", function () {
            $trans = new ModelsHtrans();
            $param["trans"] = $trans->get_all_data_by_admin();
            return view("admin.transaksi.daftarTransaksi")->with($param);
        })->middleware([CekAdmin::class]);
        Route::get("/daftarTransaksi", [Transaksi::class, "daftarTransaksiAdmin"])->middleware([CekAdmin::class]);
        Route::get("/detailTransaksi/{id}", function ($id) {
            $htrans = new ModelsHtrans();
            $param["htrans"] = $htrans->get_all_data_by_id($id);
            $dtrans = new ModelsDtrans();
            $param["dtrans"] = $dtrans->get_all_data_by_id_htrans($id);
            return view("admin.transaksi.detailTransaksi")->with($param);
        })->middleware([CekAdmin::class]);
        Route::get("/daftarKerusakan", [KerusakanAlat::class, "daftarKerusakan"]);
    });

    Route::prefix("/komplain")->group(function(){
        Route::prefix("/request")->group(function(){
            Route::get("/daftarKomplain", function () {
                $komp = new ModelsKomplainRequest();
                $param["baru"] = $komp->get_all_data_by_admin_baru();
                $param["diterima"] = $komp->get_all_data_by_admin_diterima();
                $param["ditolak"] = $komp->get_all_data_by_admin_ditolak();
                return view("admin.komplain.request.daftarKomplain")->with($param);
            })->middleware([CekAdmin::class]);
            Route::get("/detailKomplain/{id}", function ($id) {
                $komp = new ModelsKomplainRequest();
                $param["komplain"] = $komp->get_all_data_by_id($id);
                $files = new ModelsFilesKomplainReq();
                $param["files"] = $files->get_all_data($id);
                return view("admin.komplain.request.detailKomplain")->with($param);
            })->middleware([CekAdmin::class]);
            Route::post("/terimaKomplain", [KomplainRequest::class, "terimaKomplain"]);
            Route::get("/tolakKomplain/{id}", [KomplainRequest::class, "tolakKomplain"]);
        });

        Route::prefix("/trans")->group(function(){
            Route::get("/daftarKomplain", function () {
                $komp = new ModelsKomplainTrans();
                $param["baru"] = $komp->get_all_data_by_admin_baru();
                $param["diterima"] = $komp->get_all_data_by_admin_diterima();
                $param["ditolak"] = $komp->get_all_data_by_admin_ditolak();
                return view("admin.komplain.trans.daftarKomplain")->with($param);
            })->middleware([CekAdmin::class]);
            Route::get("/detailKomplain/{id}", function ($id) {
                $komp = new ModelsKomplainTrans();
                $param["komplain"] = $komp->get_all_data_by_id($id);
                $files = new ModelsFilesKomplainTrans();
                $param["files"] = $files->get_all_data($id);
                return view("admin.komplain.trans.detailKomplain")->with($param);
            })->middleware([CekAdmin::class]);
            Route::post("/terimaKomplain", [KomplainTrans::class, "terimaKomplain"]);
            Route::get("/tolakKomplain/{id}", [KomplainTrans::class, "tolakKomplain"]);
        });
    });

    Route::prefix("/request")->group(function(){
        Route::get("/detailRequest/{jenis}/{id}", function ($jenis, $id) {
            if ($jenis == "Permintaan") {
                $req = new ModelsRequestPermintaan();
            }
            else {
                $req = new ModelsRequestPenawaran();
            }
            $param["request"] = $req->get_all_data_by_id($id);
            $param["jenis"] = $jenis;
            return view("admin.request.detailRequest")->with($param);
        })->middleware([CekAdmin::class]);
    });

    Route::prefix("/laporan")->group(function(){
        Route::prefix("/pendapatan")->group(function(){
            Route::get("/laporanPendapatan", [Laporan::class, "laporanPendapatanAdmin"])->middleware([CekAdmin::class]);
            Route::get("/fiturPendapatan", [Laporan::class, "fiturPendapatanAdmin"]);
            Route::get("/CetakPDF", [Laporan::class, "pendapatanAdminCetakPDF"]);
            Route::get("/CetakPDF/{mulai}/{selesai}", [Laporan::class, "pendapatanAdminCetakPDF2"]);
        });

        Route::prefix("/alat")->group(function(){
            Route::get("/laporanAlat", [Laporan::class, "laporanAlatAdmin"])->middleware([CekAdmin::class]);
            Route::get("/CetakPDF", [Laporan::class, "alatAdminCetakPDF"]);
        });

        Route::prefix("/tempat")->group(function(){
            Route::get("/laporanTempat", [Laporan::class, "laporanTempatAdmin"])->middleware([CekAdmin::class]);
            Route::get("/CetakPDF", [Laporan::class, "tempatAdminCetakPDF"]);
        });
    });
});

// -------------------------------
// HALAMAN PEMILIK ALAT
// -------------------------------
Route::prefix("/pemilik")->group(function(){
    // Route::get("/beranda", function () {
    //     $id = Session::get("dataRole")->id_pemilik;
    //     $alat = new ModelsAlatOlahraga();
    //     $param["jumlahAlat"] = $alat->count_all_data($id);
    //     $minta = new ModelsRequestPermintaan();
    //     $param["jumlahPermintaan"] = $minta->count_all_data_pemilik($id);
    //     $tawar = new ModelsRequestPenawaran();
    //     $param["jumlahPenawaran"] = $tawar->count_all_data_pemilik($id);
    //     return view("pemilik.beranda")->with($param);
    // })->middleware([CekPemilik::class]);
    Route::get("/beranda", [Laporan::class, "berandaPemilik"])->middleware([CekPemilik::class]);
    Route::get("/masterAlat", function () {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        return view("pemilik.alat.masterAlat")->with($param);
    })->middleware([CekPemilik::class]);
    Route::post("/tambahAlat", [AlatOlahraga::class, "tambahAlat"]);
    // Route::get("/daftarAlat", function () {
    //     $id = Session::get("dataRole")->id_pemilik;
    //     $files = new filesAlatOlahraga();
    //     $alat = new ModelsAlatOlahraga();
    //     $param["alat"] = $alat->get_all_data($id, "Pemilik");
    //     $param["files"] = $files;
    //     return view("pemilik.alat.daftarAlat")->with($param);
    // })->middleware([CekPemilik::class]);
    Route::get("/daftarAlat", [AlatOlahraga::class, "daftarAlatPemilik"])->middleware([CekPemilik::class]);
    Route::get("/lihatDetail/{id}", function ($id) {//melihat detail alat olahraga miliknya
        $alat = new ModelsAlatOlahraga();
        $param["alat"] = $alat->get_all_data_by_id($id);
        $files = new filesAlatOlahraga();
        $param["files"] = $files->get_all_data($id);
        return view("pemilik.alat.detailAlat")->with($param);
        // echo $id;
    })->middleware([CekPemilik::class]);
    Route::get("/editAlat/{id}", function ($id) {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        $alat = new ModelsAlatOlahraga();
        $param["alat"] = $alat->get_all_data_by_id($id);
        $files = new filesAlatOlahraga();
        $param["files"] = $files->get_all_data($id);
        return view("pemilik.alat.editAlat")->with($param);
        // echo $id;
    })->middleware([CekPemilik::class]);
    Route::post("/editAlat", [AlatOlahraga::class, "editAlat"]);
    // Route::get("/cariLapangan", function () {
    //     $kat = new kategori();
    //     $param["kategori"] = $kat->get_all_data();
    //     $lapa = new ModelsLapanganOlahraga();
    //     $param["lapangan"] = $lapa->get_all_data2();
    //     $files = new filesLapanganOlahraga();
    //     $param["files"] = $files;
    //     return view("pemilik.cariLapangan")->with($param);
    // })->middleware([CekPemilik::class]);
    Route::get("/cariLapangan", [LapanganOlahraga::class, "cariLapangan"])->middleware([CekPemilik::class]);
    Route::get("/searchLapangan", [LapanganOlahraga::class, "searchLapangan"]);
    Route::get("/detailLapanganUmum/{id}", function ($id) {//melihat detail lapangan olahraga milik org lain
        $lapa = new ModelsLapanganOlahraga();
        $param["lapangan"] = $lapa->get_all_data_by_id($id);
        $files = new filesLapanganOlahraga();
        $param["files"] = $files->get_all_data($id);
        $role = Session::get("dataRole")->id_pemilik;
        $alat = new ModelsAlatOlahraga();
        $param["alat"] = $alat->get_all_data_status($role);
        $slot = new ModelsSlotWaktu();
        $param["slot"] = $slot->get_all_data_by_lapangan($id);

        $per = new ModelsRequestPermintaan();
        $param["permintaan"] = $per->get_all_data_by_lapangan($id);
        $pen = new ModelsRequestPenawaran();
        $param["penawaran"] = $pen->get_all_data_by_lapangan($id);
        $sewa = new ModelsSewaSendiri();
        $param["sewa"] = $sewa->get_all_data_by_lapangan($id);
        return view("pemilik.detailLapanganUmum")->with($param);
    })->middleware([CekPemilik::class]);
    Route::post('editHargaKomisi',[AlatOlahraga::class, "editHargaKomisi"]);

    //Request permintaan
    Route::prefix("/permintaan")->group(function(){
        // Route::get("/daftarPermintaan", function () {
        //     $role = Session::get("dataRole")->id_pemilik;
        //     $req = new ModelsRequestPermintaan();
        //     $param["baru"] = $req->get_all_data_by_pemilik_baru($role);
        //     $param["diterima"] = $req->get_all_data_by_pemilik_diterima($role);
        //     $param["disewakan"] = $req->get_all_data_by_pemilik_disewakan($role);
        //     $param["ditolak"] = $req->get_all_data_by_pemilik_ditolak($role);
        //     $param["selesai"] = $req->get_all_data_by_pemilik_selesai($role);
        //     $param["dibatalkan"] = $req->get_all_data_by_pemilik_dibatalkan($role);
        //     $param["dikomplain"] = $req->get_all_data_by_pemilik_dikomplain($role);
        //     return view("pemilik.permintaan.daftarPermintaan")->with($param);
        // })->middleware([CekPemilik::class]);
        Route::get("/daftarPermintaan", [RequestPermintaan::class, "daftarPermintaanPemilik"])->middleware([CekPemilik::class]);
        Route::get("/detailPermintaanNego/{id}", function ($id) {
            $role = Session::get("dataRole")->id_pemilik;
            $req = new ModelsRequestPermintaan();
            $param["permintaan"] = $req->get_all_data_by_id($id);
            $nego = new ModelsNegosiasi();
            $param["nego"] = $nego->get_all_data_by_id_permintaan($id);
            $komplain = new ModelsKomplainRequest();
            $param["komplain"] = $komplain->get_all_data_by_id_req_pemilik_permintaan($id, $role);
            return view("pemilik.permintaan.detailPermintaanNego")->with($param);
        })->middleware([CekPemilik::class]);
        Route::post("/terimaPermintaan", [RequestPermintaan::class, "terimaPermintaan"]);
        Route::post("/tolakPermintaan", [RequestPermintaan::class, "tolakPermintaan"]);
        Route::post("/confirmKodeMulai", [RequestPermintaan::class, "confirmKodeMulai"]);
        Route::post("/confirmKodeSelesai", [RequestPermintaan::class, "confirmKodeSelesai"]);

        //Bagian negosiasi
        Route::prefix("/negosiasi")->group(function(){
            Route::post("tambahNego", [Negosiasi::class, "tambahNegoPermintaan"]);
        });
    });

    //Request penawaran
    Route::prefix("/penawaran")->group(function(){
        Route::post("/requestPenawaranAlat", [RequestPenawaran::class, "ajukanPenawaran"]);
        // Route::get("/daftarPenawaran", function () {
        //     $role = Session::get("dataRole")->id_pemilik;
        //     $req = new ModelsRequestPenawaran();
        //     $param["baru"] = $req->get_all_data_by_pemilik_baru($role);
        //     $param["diterima"] = $req->get_all_data_by_pemilik_diterima($role);
        //     $param["ditolak"] = $req->get_all_data_by_pemilik_ditolak($role);
        //     $param["disewakan"] = $req->get_all_data_by_pemilik_disewakan($role);
        //     $param["selesai"] = $req->get_all_data_by_pemilik_selesai($role);
        //     $param["dibatalkan"] = $req->get_all_data_by_pemilik_dibatalkan($role);
        //     $param["dikomplain"] = $req->get_all_data_by_pemilik_dikomplain($role);
        //     return view("pemilik.penawaran.daftarPenawaran")->with($param);
        // })->middleware([CekPemilik::class]);
        Route::get("/daftarPenawaran", [RequestPenawaran::class, "daftarPenawaranPemilik"])->middleware([CekPemilik::class]);
        Route::get("/detailPenawaranNego/{id}", function ($id) {
            $role = Session::get("dataRole")->id_pemilik;
            $req = new ModelsRequestPenawaran();
            $param["penawaran"] = $req->get_all_data_by_id($id);
            $nego = new ModelsNegosiasi();
            $param["nego"] = $nego->get_all_data_by_id_penawaran($id);
            $komplain = new ModelsKomplainRequest();
            $param["komplain"] = $komplain->get_all_data_by_id_req_pemilik_penawaran($id, $role);
            return view("pemilik.penawaran.detailPenawaranNego")->with($param);
        })->middleware([CekPemilik::class]);
        Route::post("/batalPenawaran", [RequestPenawaran::class, "batalPenawaran"]);
        Route::post("/konfirmasiPenawaran", [RequestPenawaran::class, "konfirmasiPenawaran"]);
        Route::post("/confirmKodeMulai", [RequestPenawaran::class, "confirmKodeMulai"]);
        Route::post("/confirmKodeSelesai", [RequestPenawaran::class, "confirmKodeSelesai"]);

        Route::post("/tawarLagi", [RequestPenawaran::class, "tawarLagi"]);

        //Bagian negosiasi
        Route::prefix("/negosiasi")->group(function(){
            Route::post("tambahNego", [Negosiasi::class, "tambahNegoPenawaran"]);
        });
    });

    Route::prefix("/komplain")->group(function(){
        Route::post("tambahKomplain", [KomplainRequest::class, "tambahKomplain"]);
    });

    //Bagian transaksi
    Route::prefix("/disewakan")->group(function(){
        // Route::get("/daftarDisewakan", function () {
        //     $role = Session::get("dataRole")->id_pemilik;
        //     $trans = new ModelsDtrans();
        //     $param["disewakan"] = $trans->get_all_data_by_pemilik($role);
        //     return view("pemilik.alat.daftarDisewakan")->with($param);
        // })->middleware([CekPemilik::class]);
        Route::get("/daftarDisewakan", [AlatOlahraga::class, "daftarDisewakan"])->middleware([CekPemilik::class]);
    });

    Route::prefix("/laporan")->group(function(){
        Route::prefix("/pendapatan")->group(function(){
            Route::get("/laporanPendapatan", [Laporan::class, "laporanPendapatanPemilik"])->middleware([CekPemilik::class]);
            Route::get("/fiturPendapatan", [Laporan::class, "fiturPendapatanPemilik"]);
            Route::get('/CetakPDF/{mulai}/{selesai}', [Laporan::class, "pendapatanPemilikCetakPDF2"]);
            Route::get('/CetakPDF', [Laporan::class, "pendapatanPemilikCetakPDF"]);
        });

        Route::prefix("/stok")->group(function(){
            Route::get("/laporanStok", [Laporan::class, "laporanStokPemilik"])->middleware([CekPemilik::class]);
            Route::get('/CetakPDF', [Laporan::class, "stokPemilikCetakPDF"]);
        });

        Route::prefix("/disewakan")->group(function(){
            Route::get("/laporanDisewakan", [Laporan::class, "laporanDisewakanPemilik"])->middleware([CekPemilik::class]);
            Route::get('/CetakPDF', [Laporan::class, "disewakanPemilikCetakPDF"]);
        });

        Route::prefix("/tempat")->group(function(){
            Route::get("/laporanTempat", [Laporan::class, "laporanTempatPemilik"])->middleware([CekPemilik::class]);
            Route::get('/CetakPDF', [Laporan::class, "tempatPemilikCetakPDF"]);
        });
    });

    Route::prefix("/saldo")->group(function(){
        Route::get("/tarikSaldo", function () {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            return view("pemilik.tarikSaldo")->with($param);
        })->middleware([CekPemilik::class]);
        Route::post("/tarikDana", [Saldo::class, "tarikSaldo"]);
    });
});

// -------------------------------
// HALAMAN PIHAK TEMPAT
// -------------------------------
Route::prefix("/tempat")->group(function(){
    // Route::get("/beranda", function () {
    //     $role = Session::get("dataRole")->id_tempat;
    //     $trans = new ModelsHtrans();
    //     $param["jumlahTransaksi"] = $trans->count_all_data_tempat($role);
    //     $minta = new ModelsRequestPermintaan();
    //     $param["jumlahPermintaan"] = $minta->count_all_data_tempat($role);
    //     $tawar = new ModelsRequestPenawaran();
    //     $param["jumlahPenawaran"] = $tawar->count_all_data_pemilik($role);
    //     return view("tempat.beranda")->with($param);
    // })->middleware([CekTempat::class]);
    Route::get("/beranda", [Laporan::class, "berandaTempat"])->middleware([CekTempat::class]);
    // Route::get("/cariAlat", function () {
    //     $kat = new kategori();
    //     $param["kategori"] = $kat->get_all_data();
    //     $alat = new ModelsAlatOlahraga();
    //     $param["alat"] = $alat->get_all_data2();
    //     $files = new filesAlatOlahraga();
    //     $param["files"] = $files;
    //     return view("tempat.cariAlat")->with($param);
    // })->middleware([CekTempat::class]);
    Route::get("/cariAlat", [AlatOlahraga::class, "cariAlat"])->middleware([CekTempat::class]);
    Route::get("/searchAlat", [AlatOlahraga::class, "searchAlat"]);
    Route::get("/detailAlatUmum/{id}", function ($id) {//melihat detail alat olahraga milik org lain
        $alat = new ModelsAlatOlahraga();
        $param["alat"] = $alat->get_all_data_by_id($id);
        $files = new filesAlatOlahraga();
        $param["files"] = $files->get_all_data($id);
        $role = Session::get("dataRole")->id_tempat;
        $lapa = new ModelsLapanganOlahraga();
        $param["lapangan"] = $lapa->get_all_data_status($role);
        return view("tempat.detailAlatUmum")->with($param);
    })->middleware([CekTempat::class]);
    Route::post("/requestPermintaanAlat", [RequestPermintaan::class, "ajukanPermintaan"]);

    //Bagian lapangan olahraga
    Route::prefix("/lapangan")->group(function(){
        Route::get("/masterLapangan", function () {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            return view("tempat.lapangan.masterLapangan")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/tambahLapangan", [LapanganOlahraga::class, "tambahLapangan"]);
        // Route::get("/daftarLapangan", function () {
        //     $role = Session::get("dataRole")->id_tempat;
        //     $files = new filesLapanganOlahraga();
        //     $lap = new ModelsLapanganOlahraga();
        //     $param["lapangan"] = $lap->get_all_data($role);
        //     $param["files"] = $files;
        //     return view("tempat.lapangan.daftarLapangan")->with($param);
        // })->middleware([CekTempat::class]);
        Route::get("/daftarLapangan", [LapanganOlahraga::class, "daftarLapangan"])->middleware([CekTempat::class]);
        Route::get("/lihatDetailLapangan/{id}", function ($id) {//melihat detail lapangan olahraga miliknya
            $lap = new ModelsLapanganOlahraga();
            $param["lapangan"] = $lap->get_all_data_by_id($id);
            $files = new filesLapanganOlahraga();
            $param["files"] = $files->get_all_data($id);
            $alat = new ModelsAlatOlahraga();
            $param["alat"] = $alat->get_all_data3(Session::get("dataRole")->id_tempat);
            $slot = new ModelsSlotWaktu();
            $param["slot"] = $slot->get_all_data_by_lapangan($id);

            $per = new ModelsRequestPermintaan();
            $param["permintaan"] = $per->get_all_data_by_lapangan($id);
            $pen = new ModelsRequestPenawaran();
            $param["penawaran"] = $pen->get_all_data_by_lapangan($id);

            $sewa = new ModelsSewaSendiri();
            $param["sewa"] = $sewa->get_all_data_by_lapangan($id);
            return view("tempat.lapangan.detailLapangan")->with($param);
        })->middleware([CekTempat::class]);
        Route::get("/editLapangan/{id}", function ($id) {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            $lap = new ModelsLapanganOlahraga();
            $param["lapangan"] = $lap->get_all_data_by_id($id);
            $files = new filesLapanganOlahraga();
            $param["files"] = $files->get_all_data($id);
            $slot = new ModelsSlotWaktu();
            $param["slot"] = $slot->get_all_data_by_lapangan($id);
            return view("tempat.lapangan.editLapangan")->with($param);
            // echo $id;
        })->middleware([CekTempat::class]);
        Route::post("/editLapangan", [LapanganOlahraga::class, "editLapangan"]);
    });

    //Bagian alat olahraga
    Route::prefix("/alat")->group(function(){
        Route::get("/masterAlat", function () {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            return view("tempat.alat.masterAlat")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/tambahAlat", [AlatOlahraga::class, "tambahAlat"]);
        // Route::get("/daftarAlat", function () {
        //     $id = Session::get("dataRole")->id_tempat;
        //     $files = new filesAlatOlahraga();
        //     $alat = new ModelsAlatOlahraga();
        //     $param["alat"] = $alat->get_all_data($id, "Tempat");
        //     $param["files"] = $files;
        //     return view("tempat.alat.daftarAlat")->with($param);
        // })->middleware([CekTempat::class]);
        Route::get("/daftarAlat", [AlatOlahraga::class, "daftarAlatTempat"])->middleware([CekTempat::class]);
        Route::get("/lihatDetail/{id}", function ($id) {//melihat detail alat olahraga miliknya
            $alat = new ModelsAlatOlahraga();
            $param["alat"] = $alat->get_all_data_by_id($id);
            $files = new filesAlatOlahraga();
            $param["files"] = $files->get_all_data($id);
            return view("tempat.alat.detailAlat")->with($param);
        })->middleware([CekTempat::class]);
        Route::get("/editAlat/{id}", function ($id) {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            $alat = new ModelsAlatOlahraga();
            $param["alat"] = $alat->get_all_data_by_id($id);
            $files = new filesAlatOlahraga();
            $param["files"] = $files->get_all_data($id);
            return view("tempat.alat.editAlat")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/editAlat", [AlatOlahraga::class, "editAlat"]);
    });

    //Request permintaan
    Route::prefix("/permintaan")->group(function(){
        Route::post("/requestPermintaanAlat", [RequestPermintaan::class, "ajukanPermintaan"]);
        // Route::get("/daftarPermintaan", function () {
        //     $role = Session::get("dataRole")->id_tempat;
        //     $req = new ModelsRequestPermintaan();
        //     $param["baru"] = $req->get_all_data_by_tempat_baru($role);
        //     $param["diterima"] = $req->get_all_data_by_tempat_diterima($role);
        //     $param["ditolak"] = $req->get_all_data_by_tempat_ditolak($role);
        //     $param["selesai"] = $req->get_all_data_by_tempat_selesai($role);
        //     $param["disewakan"] = $req->get_all_data_by_tempat_disewakan($role);
        //     $param["dibatalkan"] = $req->get_all_data_by_tempat_dibatalkan($role);
        //     $param["dikomplain"] = $req->get_all_data_by_tempat_dikomplain($role);
        //     return view("tempat.permintaan.daftarPermintaan")->with($param);
        // })->middleware([CekTempat::class]);
        Route::get("/daftarPermintaan", [RequestPermintaan::class, "daftarPermintaanTempat"])->middleware([CekTempat::class]);
        Route::get("/detailPermintaanNego/{id}", function ($id) {
            $role = Session::get("dataRole")->id_tempat;
            $req = new ModelsRequestPermintaan();
            $param["permintaan"] = $req->get_all_data_by_id($id);
            $nego = new ModelsNegosiasi();
            $param["nego"] = $nego->get_all_data_by_id_permintaan($id);
            $komplain = new ModelsKomplainRequest();
            $param["komplain"] = $komplain->get_all_data_by_id_req_tempat_permintaan($id, $role);
            return view("tempat.permintaan.detailPermintaanNego")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/batalPermintaan", [RequestPermintaan::class, "batalPermintaan"]);
        Route::post("/editHargaSewa", [RequestPermintaan::class, "editHargaSewa"]);
        Route::post("/simpanKodeMulai", [RequestPermintaan::class, "simpanKodeMulai"]);
        Route::post("/simpanKodeSelesai", [RequestPermintaan::class, "simpanKodeSelesai"]);

        //Bagian negosiasi
        Route::prefix("/negosiasi")->group(function(){
            Route::post("tambahNego", [Negosiasi::class, "tambahNegoPermintaan"]);
        });
    });

    //Request penawaran
    Route::prefix("/penawaran")->group(function(){
        // Route::get("/daftarPenawaran", function () {
        //     $role = Session::get("dataRole")->id_tempat;
        //     $req = new ModelsRequestPenawaran();
        //     $param["baru"] = $req->get_all_data_by_tempat_baru($role);
        //     $param["diterima"] = $req->get_all_data_by_tempat_diterima($role);
        //     $param["ditolak"] = $req->get_all_data_by_tempat_ditolak($role);
        //     $param["selesai"] = $req->get_all_data_by_tempat_selesai($role);
        //     $param["disewakan"] = $req->get_all_data_by_tempat_disewakan($role);
        //     $param["dibatalkan"] = $req->get_all_data_by_tempat_dibatalkan($role);
        //     $param["dikomplain"] = $req->get_all_data_by_tempat_dikomplain($role);
        //     return view("tempat.penawaran.daftarPenawaran")->with($param);
        // })->middleware([CekTempat::class]);
        Route::get("/daftarPenawaran", [RequestPenawaran::class, "daftarPenawaranTempat"])->middleware([CekTempat::class]);
        Route::get("/detailPenawaranNego/{id}", function ($id) {
            $role = Session::get("dataRole")->id_tempat;
            $req = new ModelsRequestPenawaran();
            $param["penawaran"] = $req->get_all_data_by_id($id);
            $nego = new ModelsNegosiasi();
            $param["nego"] = $nego->get_all_data_by_id_penawaran($id);
            $komplain = new ModelsKomplainRequest();
            $param["komplain"] = $komplain->get_all_data_by_id_req_tempat_penawaran($id, $role);
            return view("tempat.penawaran.detailPenawaranNego")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/terimaPenawaran", [RequestPenawaran::class, "terimaPenawaran"]);
        Route::post("/tolakPenawaran", [RequestPenawaran::class, "tolakPenawaran"]);
        Route::post("/editHargaSewa", [RequestPenawaran::class, "editHargaSewa"]);
        Route::post("/editTanggalMulai", [RequestPenawaran::class, "editTanggalMulai"]);
        Route::post("/editTanggalSelesai", [RequestPenawaran::class, "editTanggalSelesai"]);
        Route::post("/simpanKodeMulai", [RequestPenawaran::class, "simpanKodeMulai"]);
        Route::post("/simpanKodeSelesai", [RequestPenawaran::class, "simpanKodeSelesai"]);

        Route::prefix("/negosiasi")->group(function(){
            Route::post("tambahNego", [Negosiasi::class, "tambahNegoPenawaran"]);
        });
    });

    //Bagian komplain
    Route::prefix("/komplain")->group(function(){
        Route::post("tambahKomplain", [KomplainRequest::class, "tambahKomplain"]);
    });

    //Bagian sewa sendiri
    Route::prefix("/sewa")->group(function(){
        Route::post("/tambahSewaSendiri", [SewaSendiri::class, "tambahSewaSendiri"]);
        Route::post("/hapusSewaSendiri", [SewaSendiri::class, "hapusSewaSendiri"]);
    });

    //Bagian transaksi
    Route::prefix("/transaksi")->group(function(){
        // Route::get("/daftarTransaksi", function () {
        //     $role = Session::get("dataRole")->id_tempat;
        //     $trans = new ModelsHtrans();
        //     $param["baru"] = $trans->get_all_data_by_tempat_baru($role);
        //     $param["diterima"] = $trans->get_all_data_by_tempat_diterima($role);
        //     $param["berlangsung"] = $trans->get_all_data_by_tempat_berlangsung($role);
        //     $param["ditolak"] = $trans->get_all_data_by_tempat_ditolak($role);
        //     $param["selesai"] = $trans->get_all_data_by_tempat_selesai($role);
        //     $param["dikomplain"] = $trans->get_all_data_by_tempat_dikomplain($role);
        //     $param["dibatalkan"] = $trans->get_all_data_by_tempat_dibatalkan($role);
        //     return view("tempat.transaksi.daftarTransaksi")->with($param);
        // })->middleware([CekTempat::class]);
        Route::get("/daftarTransaksi", [Transaksi::class, "daftarTransaksiTempat"])->middleware([CekTempat::class]);
        Route::get("/detailTransaksi/{id}", function ($id) {
            $htrans = new ModelsHtrans();
            $param["htrans"] = $htrans->get_all_data_by_id($id);
            $dtrans = new ModelsDtrans();
            $param["dtrans"] = $dtrans->get_all_data_by_id_htrans($id);
            return view("tempat.transaksi.detailTransaksi")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/terimaTransaksi", [Transaksi::class, "terimaTransaksi"]);
        Route::post("/tolakTransaksi", [Transaksi::class, "tolakTransaksi"]);
        Route::post("/konfirmasiDipakai", [Transaksi::class, "konfirmasiDipakai"]);
        Route::get("/cetakNota", [Transaksi::class, "cetakNota"])->middleware([CekTempat::class]);

        Route::get("/tampilanEditTransaksi/{id}", function ($id) {
            $htrans = new ModelsHtrans();
            $param["htrans"] = $htrans->get_all_data_by_id($id);
            $dtrans = new ModelsDtrans();
            $param["dtrans"] = $dtrans->get_all_data_by_id_htrans($id);
            return view("tempat.transaksi.editTrans")->with($param);
        })->middleware([CekTempat::class]);
        
        Route::post("/hapusAlat", [Transaksi::class, "hapusAlat"]);
        Route::post("/batalTrans", [Transaksi::class, "batalTrans"]);
    });

    //bagian extend
    Route::prefix("/extend")->group(function(){
        Route::post("/terimaExtend", [Transaksi::class, "terimaExtend"]);
        Route::post("/tolakExtend", [Transaksi::class, "tolakExtend"]);
    });

    Route::prefix("/kerusakan")->group(function(){
        Route::get("/detailKerusakan/{id}", function ($id) {
            $dtrans = new ModelsDtrans();
            $param["dtrans"] = $dtrans->get_all_data_by_id_htrans($id);
            return view("tempat.transaksi.detailKerusakan")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/ajukanKerusakan", [KerusakanAlat::class, "ajukanKerusakan"]);
        Route::get("/daftarKerusakan", [KerusakanAlat::class, "daftarKerusakan"]);
        Route::get("/detailKerusakan2", function () {
            $htrans = new ModelsHtrans();
            $param["htrans"] = $htrans->get_all_data_by_tempat(Session::get("dataRole")->id_tempat);
            $param["dtrans"] = null;
            return view("tempat.transaksi.detailKerusakan2")->with($param);
        })->middleware([CekTempat::class]);
        Route::get("/tampilkan", [KerusakanAlat::class, "detailTrans"]);
    });

    //bagian laporan
    Route::prefix("/laporan")->group(function(){
        Route::prefix("/pendapatan")->group(function(){
            Route::get("/laporanPendapatan", [Laporan::class, "laporanPendapatanTempat"])->middleware([CekTempat::class]);
            Route::get("/fiturPendapatan", [Laporan::class, "fiturPendapatanTempat"]);
            Route::get('/CetakPDF', [Laporan::class, "pendapatanTempatCetakPDF"]);
            Route::get('/CetakPDF/{mulai}/{selesai}', [Laporan::class, "pendapatanTempatCetakPDF2"]);
        });

        Route::prefix("/stok")->group(function(){
            Route::get("/laporanStok", [Laporan::class, "laporanStokTempat"])->middleware([CekTempat::class]);
            Route::get('/CetakPDF', [Laporan::class, "stokTempatCetakPDF"]);
        });

        Route::prefix("/disewakan")->group(function(){
            Route::get("/laporanDisewakan", [Laporan::class, "laporanDisewakanTempat"])->middleware([CekTempat::class]);
            Route::get('/CetakPDF', [Laporan::class, "disewakanTempatCetakPDF"]);
            Route::get("/laporanPerAlat/{id}", [Laporan::class, "laporanPerAlatTempat"])->middleware([CekTempat::class]);
            Route::get("/fiturPerAlat/{id}", [Laporan::class, "fiturPerAlat"])->middleware([CekTempat::class]);
        });

        Route::prefix("/lapangan")->group(function(){
            Route::get("/laporanLapangan", [Laporan::class, "laporanLapangan"])->middleware([CekTempat::class]);
            Route::get('/CetakPDF', [Laporan::class, "lapanganCetakPDF"]);
        });
    });
});

// -------------------------------
// HALAMAN CUSTOMER
// -------------------------------
Route::prefix("/customer")->group(function(){
    // Route::view("/beranda", "customer.beranda");
    Route::get("/beranda", [LapanganOlahraga::class, "cariLapangan"])->middleware([CekCustomer::class]);
    Route::get("/editProfile", function () {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        $cust = new customer();
        $cust->get_all_data_by_id(Session::get("dataRole")->id_user);
        $param["cust"] = $cust->get_all_data_by_id(Session::get("dataRole")->id_user);
        return view("customer.profile")->with($param);
    });
    Route::get("/detailLapangan/{id}", function ($id) {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        $lapa = new ModelsLapanganOlahraga();
        $param["lapangan"] = $lapa->get_all_data_by_id($id);
        $files = new filesLapanganOlahraga();
        $param["files"] = $files->get_all_data($id);
        $slot = new ModelsSlotWaktu();
        $param["slot"] = $slot->get_all_data_by_lapangan($id);

        $per = new ModelsRequestPermintaan();
        $param["permintaan"] = $per->get_all_data_by_lapangan($id);
        $pen = new ModelsRequestPenawaran();
        $param["penawaran"] = $pen->get_all_data_by_lapangan($id);
        $sewa = new ModelsSewaSendiri();
        $param["sewa"] = $sewa->get_all_data_by_lapangan($id);
        return view("customer.detailLapangan")->with($param);
    })->middleware([CekCustomer::class]);
    Route::get("/searchLapangan", [LapanganOlahraga::class, "searchLapanganCustomer"]);
    Route::get("/detailAlat/{id}", function ($id) {//melihat detail alat olahraga milik org lain
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        $alat = new ModelsAlatOlahraga();
        $param["alat"] = $alat->get_all_data_by_id($id);
        $files = new filesAlatOlahraga();
        $param["files"] = $files->get_all_data($id);
        return view("customer.detailAlat")->with($param);
    })->middleware([CekCustomer::class]);

    //bagian transaksi
    Route::prefix("/transaksi")->group(function(){
        Route::post("/tambahAlat", [Transaksi::class, "tambahAlat"]);
        Route::get("/deleteAlat/{urutan}", [Transaksi::class, "deleteAlat"]);
        Route::get("/detailTransaksi", [Transaksi::class, "detailTransaksi"]);
        Route::post("/tambahTransaksi", [Transaksi::class, "tambahTransaksi"]);
        Route::post("/batalBooking", [Transaksi::class, "batalBooking"]);
        Route::get("/deleteAlatDetail/{id}", [Transaksi::class, "deleteAlatDetail"]);
    });

    //bagian transaksi
    Route::prefix("/extend")->group(function(){
        Route::post("/tambahWaktu", [Transaksi::class, "tambahWaktu"]);
        Route::get("/detailTambahWaktu", [Transaksi::class, "detailTambahWaktu"]);
    });

    Route::post("/tambahKeranjang", [Transaksi::class, "tambahKeranjang"]);
    Route::get("/daftarKeranjang", [Transaksi::class, "daftarKeranjang"])->middleware([CekCustomer::class]);
    Route::get("/hapusKeranjang/{urutan}", [Transaksi::class, "hapusKeranjang"]);

    Route::get("/daftarRiwayat", [Transaksi::class, "daftarRiwayat"])->middleware([CekCustomer::class]);
    Route::get("/daftarKomplain", [KomplainTrans::class, "daftarKomplain"])->middleware([CekCustomer::class]);

    Route::prefix("/saldo")->group(function(){
        Route::post("/topup", [Saldo::class, "topup"]);
        Route::get("/topupSaldo", function () {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            return view("customer.topup")->with($param);
        })->middleware([CekCustomer::class]);
        Route::post("/midtrans-callback", [Saldo::class, "callback"]);
        Route::post("/after_midtrans", [Saldo::class, "afterpayment"]);
    });

    Route::prefix("/rating")->group(function(){
        Route::get("/detailRating/{id}", function ($id) {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();

            $htrans = new ModelsHtrans();
            $param["htrans"] = $htrans->get_all_data_by_id($id);

            $dtrans = new ModelsDtrans();
            $param["dtrans"] = $dtrans->get_all_data_by_id_htrans($id);
            return view("customer.ulasan")->with($param);
        })->middleware([CekCustomer::class]);

        Route::prefix("/lapangan")->group(function(){
            Route::post("/tambahRating", [Rating::class, "tambahRatingLapangan"]);
        });
        
        Route::prefix("/alat")->group(function(){
            Route::post("/tambahRating", [Rating::class, "tambahRatingAlat"]);
        });
    });

    Route::prefix("/komplain")->group(function(){
        Route::post("/ajukanKomplain", [KomplainTrans::class, "ajukanKomplain"]);
    });
});

//contoh
Route::get("/sendEmail", [NotifikasiEmail::class, "sendEmail"]);