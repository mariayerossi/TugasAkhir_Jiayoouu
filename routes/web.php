<?php

use App\Http\Controllers\AlatOlahraga;
use App\Http\Controllers\Controller;
use App\Http\Controllers\KategoriOlahraga;
use App\Http\Controllers\KerusakanAlat;
use App\Http\Controllers\KomplainRequest;
use App\Http\Controllers\KomplainTrans;
use App\Http\Controllers\LapanganOlahraga;
use App\Http\Controllers\Laporan;
use App\Http\Controllers\LoginRegister;
use App\Http\Controllers\Negosiasi;
use App\Http\Controllers\Notifikasi;
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
use App\Models\extendHtrans;
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
        $param["edit"] = "";
        $param["id"] = "";
        return view("admin.masterKategori")->with($param);
    })->middleware([CekAdmin::class]);
    Route::post("/tambahKategori", [KategoriOlahraga::class, "tambahKategori"]);
    Route::get("/hapusKategori/{id}", [KategoriOlahraga::class, "hapusKategori"]);
    Route::get("/editKategori/{id}", [KategoriOlahraga::class, "edit"]);
    Route::post("/editKategori", [KategoriOlahraga::class, "editKategori"]);
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
        Route::get("/cariAlat", [AlatOlahraga::class, "cariAlatAdmin"])->middleware([CekAdmin::class]);
        Route::get("/detailAlatUmum/{id}", [AlatOlahraga::class, "detailAlatUmumAdmin"])->middleware([CekAdmin::class]);
        Route::get("/searchAlat", [AlatOlahraga::class, "searchAlat"]);
    });

    //Bagian Lapangan Olahraga
    Route::prefix("/lapangan")->group(function(){
        Route::get("/cariLapangan", [LapanganOlahraga::class, "cariLapanganAdmin"])->middleware([CekAdmin::class]);
        Route::get("/searchLapangan", [LapanganOlahraga::class, "searchLapangan"]);
        Route::get("/detailLapanganUmum/{id}", [LapanganOlahraga::class, "detailLapanganUmumAdmin"])->middleware([CekAdmin::class]);
    });

    Route::prefix("/transaksi")->group(function(){
        Route::get("/daftarTransaksi", [Transaksi::class, "daftarTransaksiAdmin"])->middleware([CekAdmin::class]);
        Route::get("/detailTransaksi/{id}", [Transaksi::class, "detailTransaksiAdmin"])->middleware([CekAdmin::class]);
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
            Route::get("/detailKomplain/{id}", [KomplainRequest::class, "detailKomplain"])->middleware([CekAdmin::class]);
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
            Route::get("/detailKomplain/{id}", [KomplainTrans::class, "detailKomplain"])->middleware([CekAdmin::class]);
            Route::post("/terimaKomplain", [KomplainTrans::class, "terimaKomplain"]);
            Route::get("/tolakKomplain/{id}/{id2}", [KomplainTrans::class, "tolakKomplain"]);
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
    Route::get("/beranda", [Laporan::class, "berandaPemilik"])->middleware([CekPemilik::class]);
    Route::get("/masterAlat", function () {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        return view("pemilik.alat.masterAlat")->with($param);
    })->middleware([CekPemilik::class]);
    Route::post("/tambahAlat", [AlatOlahraga::class, "tambahAlat"]);
    Route::get("/daftarAlat", [AlatOlahraga::class, "daftarAlatPemilik"])->middleware([CekPemilik::class]);
    Route::get("/lihatDetail/{id}", [AlatOlahraga::class, "detailAlatPemilik"])->middleware([CekPemilik::class]);
    Route::get("/detailAlatUmum/{id}", [AlatOlahraga::class, "detailAlatUmumPemilik"])->middleware([CekPemilik::class]);
    Route::get("/editAlat/{id}", [AlatOlahraga::class, "editAlatPemilik"])->middleware([CekPemilik::class]);
    Route::post("/editAlat", [AlatOlahraga::class, "editAlat"]);
    Route::get("/cariLapangan", [LapanganOlahraga::class, "cariLapangan"])->middleware([CekPemilik::class]);
    Route::get("/searchLapangan", [LapanganOlahraga::class, "seachLapangan"]);
    Route::get("/detailLapanganUmum/{id}", [LapanganOlahraga::class, "detailLapanganUmumPemilik"])->middleware([CekPemilik::class]);
    Route::post('editHargaKomisi',[AlatOlahraga::class, "editHargaKomisi"]);

    //Request permintaan
    Route::prefix("/permintaan")->group(function(){
        Route::get("/daftarPermintaan", [RequestPermintaan::class, "daftarPermintaanPemilik"])->middleware([CekPemilik::class]);
        Route::get("/detailPermintaanNego/{id}", [RequestPermintaan::class, "detailPermintaanPemilik"])->middleware([CekPemilik::class]);
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
        Route::get("/daftarPenawaran", [RequestPenawaran::class, "daftarPenawaranPemilik"])->middleware([CekPemilik::class]);
        Route::get("/detailPenawaranNego/{id}", [RequestPenawaran::class, "detailPenawaranPemilik"])->middleware([CekPemilik::class]);
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
    Route::get("/beranda", [Laporan::class, "berandaTempat"])->middleware([CekTempat::class]);
    Route::get("/cariAlat", [AlatOlahraga::class, "cariAlat"])->middleware([CekTempat::class]);
    Route::get("/searchAlat", [AlatOlahraga::class, "searchAlat"]);
    Route::get("/detailAlatUmum/{id}", [AlatOlahraga::class, "detailAlatUmumTempat"])->middleware([CekTempat::class]);
    Route::post("/requestPermintaanAlat", [RequestPermintaan::class, "ajukanPermintaan"]);

    //Bagian lapangan olahraga
    Route::prefix("/lapangan")->group(function(){
        Route::get("/masterLapangan", function () {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            return view("tempat.lapangan.masterLapangan")->with($param);
        })->middleware([CekTempat::class]);
        Route::post("/tambahLapangan", [LapanganOlahraga::class, "tambahLapangan"]);
        Route::get("/daftarLapangan", [LapanganOlahraga::class, "daftarLapangan"])->middleware([CekTempat::class]);
        Route::get("/lihatDetailLapangan/{id}", [LapanganOlahraga::class, "detailLapanganTempat"])->middleware([CekTempat::class]);
        Route::get("/editLapangan/{id}", [LapanganOlahraga::class, "editLapanganTempat"])->middleware([CekTempat::class]);
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
        Route::get("/daftarAlat", [AlatOlahraga::class, "daftarAlatTempat"])->middleware([CekTempat::class]);
        Route::get("/lihatDetail/{id}", [AlatOlahraga::class, "detailAlatTempat"])->middleware([CekTempat::class]);
        Route::get("/editAlat/{id}", [AlatOlahraga::class, "editAlatTempat"])->middleware([CekTempat::class]);
        Route::post("/editAlat", [AlatOlahraga::class, "editAlat"]);
    });

    //Request permintaan
    Route::prefix("/permintaan")->group(function(){
        Route::post("/requestPermintaanAlat", [RequestPermintaan::class, "ajukanPermintaan"]);
        Route::get("/daftarPermintaan", [RequestPermintaan::class, "daftarPermintaanTempat"])->middleware([CekTempat::class]);
        Route::get("/detailPermintaanNego/{id}", [RequestPermintaan::class, "detailPermintaanTempat"])->middleware([CekTempat::class]);
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
        Route::get("/daftarPenawaran", [RequestPenawaran::class, "daftarPenawaranTempat"])->middleware([CekTempat::class]);
        Route::get("/detailPenawaranNego/{id}", [RequestPenawaran::class, "detailPenawaranTempat"])->middleware([CekTempat::class]);
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
        Route::get("/daftarTransaksi", [Transaksi::class, "daftarTransaksiTempat"])->middleware([CekTempat::class]);
        Route::get("/detailTransaksi/{id}", [Transaksi::class, "detailTransaksiTempat"])->middleware([CekTempat::class]);
        Route::post("/terimaTransaksi", [Transaksi::class, "terimaTransaksi"]);
        Route::post("/tolakTransaksi", [Transaksi::class, "tolakTransaksi"]);
        Route::post("/konfirmasiDipakai", [Transaksi::class, "konfirmasiDipakai"]);
        Route::get("/cetakNota", [Transaksi::class, "cetakNota"])->middleware([CekTempat::class]);

        Route::get("/tampilanEditTransaksi/{id}", [Transaksi::class, "editTransaksiTempat"])->middleware([CekTempat::class]);
        
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
            Route::get('/PerAlatCetakPDF/{id}/{filter}', [Laporan::class, "laporanPerAlatCetakPDF"]);
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
        $kot = new ModelsLapanganOlahraga();
        $param["kota"] = $kot->get_kota();
        $cust = new customer();
        $cust->get_all_data_by_id(Session::get("dataRole")->id_user);
        $param["cust"] = $cust->get_all_data_by_id(Session::get("dataRole")->id_user);
        return view("customer.profile")->with($param);
    });
    Route::get("/detailLapangan/{id}", [LapanganOlahraga::class, "detailLapanganCustomer"])->middleware([CekCustomer::class]);
    Route::get("/searchLapangan", [LapanganOlahraga::class, "searchLapanganCustomer"]);
    Route::get("/detailAlat/{id}", [AlatOlahraga::class, "detailAlatCustomer"])->middleware([CekCustomer::class]);

    //bagian transaksi
    Route::prefix("/transaksi")->group(function(){
        Route::post("/tambahAlat", [Transaksi::class, "tambahAlat"]);
        Route::get("/deleteAlat/{urutan}", [Transaksi::class, "deleteAlat"]);
        Route::get("/detailTransaksi", [Transaksi::class, "detailTransaksi"])->middleware([CekCustomer::class]);
        Route::post("/tambahTransaksi", [Transaksi::class, "tambahTransaksi"]);
        Route::post("/batalBooking", [Transaksi::class, "batalBooking"]);
        Route::get("/deleteAlatDetail/{id}", [Transaksi::class, "deleteAlatDetail"]);
    });

    //bagian transaksi
    Route::prefix("/extend")->group(function(){
        Route::post("/tambahWaktu", [Transaksi::class, "tambahWaktu"]);
        Route::get("/detailTambahWaktu", [Transaksi::class, "detailTambahWaktu"])->middleware([CekCustomer::class]);
    });

    Route::post("/tambahKeranjang", [Transaksi::class, "tambahKeranjang"]);
    Route::get("/daftarKeranjang", [Transaksi::class, "daftarKeranjang"])->middleware([CekCustomer::class]);
    Route::get("/hapusKeranjang/{urutan}", [Transaksi::class, "hapusKeranjang"]);

    Route::get("/daftarRiwayat", [Transaksi::class, "daftarRiwayat"])->middleware([CekCustomer::class]);
    Route::get("/daftarKomplain", [KomplainTrans::class, "daftarKomplain"])->middleware([CekCustomer::class]);

    Route::prefix("/saldo")->group(function(){
        Route::get("/topup", [Saldo::class, "topup"]);
        Route::get("/topupSaldo", function () {
            $kat = new kategori();
            $param["kategori"] = $kat->get_all_data();
            $kot = new ModelsLapanganOlahraga();
            $param["kota"] = $kot->get_kota();
            return view("customer.topup")->with($param);
        })->middleware([CekCustomer::class]);
        Route::post("/midtrans-callback", [Saldo::class, "callback"]);
        Route::post("/after_midtrans", [Saldo::class, "afterpayment"]);
    });

    Route::prefix("/rating")->group(function(){
        Route::get("/detailRating/{id}", [Rating::class, "detailRatingCustomer"])->middleware([CekCustomer::class]);

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

Route::prefix("/notifikasi")->group(function(){
    Route::post("/editStatusDibaca/{id}", [Notifikasi::class, "editStatusDibaca"]);
    Route::prefix("/pemilik")->group(function(){
        Route::get("/lihatNotifikasi", [Notifikasi::class, "lihatNotifikasiPemilik"])->middleware([CekPemilik::class]);
    });
    Route::prefix("/tempat")->group(function(){
        Route::get("/lihatNotifikasi", [Notifikasi::class, "lihatNotifikasiTempat"])->middleware([CekTempat::class]);
    });
    Route::prefix("/admin")->group(function(){
        Route::get("/lihatNotifikasi", [Notifikasi::class, "lihatNotifikasiAdmin"])->middleware([CekAdmin::class]);
    });
    Route::prefix("/customer")->group(function(){
        Route::get("/lihatNotifikasi", [Notifikasi::class, "lihatNotifikasiCustomer"])->middleware([CekCustomer::class]);
    });
});

//contoh
Route::get("/sendEmail", [NotifikasiEmail::class, "sendEmail"]);

Route::post("/hapusMsg", [Controller::class, "hapusSessionMsg"]);