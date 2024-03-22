<?php

namespace App\Http\Controllers;

use App\Models\alatOlahraga;
use App\Models\filesLapanganOlahraga;
use App\Models\jamKhusus;
use App\Models\kategori;
use App\Models\lapanganOlahraga as ModelsLapanganOlahraga;
use App\Models\pihakTempat;
use App\Models\ratingLapangan;
use App\Models\requestPenawaran;
use App\Models\requestPermintaan;
use App\Models\sewaSendiri;
use App\Models\slotWaktu;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LapanganOlahraga extends Controller
{
    public function tambahLapangan(Request $request){
        $request->validate([
            "lapangan" => 'required|min:5|max:255',
            "kategori" => 'required',
            "tipe" => 'required',
            "lokasi" => 'required',
            "kota" => 'required',
            "foto" => 'required|max:5120',
            "deskripsi" => 'required|max:500',
            "panjang" => 'required|numeric|min:0',
            "lebar" => 'required|numeric|min:0',
            "harga" => 'required|numeric|min:0',
            "hari1" => 'required',
            "buka1" => 'required',
            "tutup1" => 'required'
        ],[
            "required" => ":attribute lapangan olahraga tidak boleh kosong!",
            "lapangan.required" => "nama :attribute olahraga tidak boleh kosong!",
            "lapangan.max" => "nama lapangan olahraga tidak valid!",
            "lapangan.min" => "nama lapangan olahraga tidak valid!",
            "min" => ":attribute lapangan olahraga tidak valid!",
            "foto.max" => "ukuran foto lapangan olahraga tidak boleh melebihi 5MB!",
            "foto.required" => "foto lapangan olahraga tidak boleh kosong!",
            "numeric" => ":attribute lapangan olahraga tidak valid!",
            "deskripsi.max" => "deskripsi lapangan olahraga maksimal 500 kata!",
            "hari1.required" => "hari operasional lapangan tidak boleh kosong!",
            "buka1.required" => "jam buka lapangan tidak boleh kosong!",
            "tutup1.required" => "jam tutup lapangan tidak boleh kosong!"
        ]);
        $harga = intval(str_replace(".", "", $request->harga));

        $luas = $request->panjang . "x" . $request->lebar;

        $data = [
            "nama"=>ucwords($request->lapangan),
            "kategori"=>$request->kategori,
            "tipe" =>$request->tipe,
            "lokasi"=>$request->lokasi,
            "kota"=>$request->kota,
            "deskripsi"=>$request->deskripsi,
            "luas"=>$luas,
            "harga"=>$harga,
            "status"=>$request->status,
            "pemilik"=>$request->pemilik
        ];
        $lapa = new ModelsLapanganOlahraga();
        $id = $lapa->insertLapangan($data);

        //insert foto alatnya
        $destinasi = "/upload";
        foreach ($request->foto as $key => $value) {
            $foto = uniqid().".".$value->getClientOriginalExtension();
            $value->move(public_path($destinasi),$foto);
            $data2 = [
                "nama"=>$foto,
                "fk"=>$id
            ];
            $file = new filesLapanganOlahraga();
            $file->insertFilesLapangan($data2);
        }

        $index = 1;
        $prevJamTutup = null;
        $prevHari = null;

        while ($request->has("hari$index") && $request->has("buka$index") && $request->has("tutup$index")) {
            $hari = $request->input("hari$index");
            $jamBuka = $request->input("buka$index");
            $jamTutup = $request->input("tutup$index");

            if ($prevHari === $hari && $prevJamTutup !== null) {
                // Pengecekan apakah jam buka lebih awal dari jam tutup pada entry sebelumnya
                if (strtotime($jamBuka) <= strtotime($prevJamTutup)) {
                    return redirect()->back()->with('error', 'Jam buka dari field berikutnya harus lebih dari jam tutup dari field sebelumnya!');
                }

                // Pengecekan apakah jam buka dari field saat ini sama dengan jam tutup dari field sebelumnya
                if (strtotime($jamBuka) === strtotime($prevJamTutup)) {
                    return redirect()->back()->with('error', 'Jam buka dari field berikutnya tidak boleh sama dengan jam tutup dari field sebelumnya jika hari sama!');
                }
            }

            if ($hari === $prevHari || $prevHari === null) {
                $prevJamTutup = $jamTutup;
                $prevHari = $hari;
            }

            $data3 = [
                "hari" => $request->input("hari$index"),
                "buka" => $jamBuka,
                "tutup" => $jamTutup,
                "lapangan" => $id
            ];
            $slot = new slotWaktu();
            $slot->insertSlot($data3);

            $index++; // Pergi ke set input berikutnya
        }


        return redirect()->back()->with("success", "Berhasil Menambah Lapangan Olahraga!");
    }

    public function editLapangan(Request $request){
        $request->validate([
            "lapangan" => 'required|min:5|max:255',
            "kategori" => 'required',
            "tipe" => 'required',
            "lokasi" => 'required',
            "kota" => 'required',
            "deskripsi" => 'required|max:500',
            "panjang" => 'required|numeric|min:0',
            "lebar" => 'required|numeric|min:0',
            "harga" => 'required|numeric|min:0'
        ],[
            "required" => ":attribute lapangan olahraga tidak boleh kosong!",
            "lapangan.required" => "nama :attribute olahraga tidak boleh kosong!",
            "lapangan.max" => "nama lapangan olahraga tidak valid!",
            "lapangan.min" => "nama lapangan olahraga tidak valid!",
            "min" => ":attribute lapangan olahraga tidak valid!",
            "numeric" => ":attribute lapangan olahraga tidak valid!",
            "deskripsi.max" => "deskripsi lapangan olahraga maksimal 500 kata!"
        ]);

        $harga = intval(str_replace(".", "", $request->harga));

        $luas = $request->panjang . "x" . $request->lebar;

        $data = [
            "id"=> $request->id,
            "nama"=>ucwords($request->lapangan),
            "kategori"=>$request->kategori,
            "tipe" =>$request->tipe,
            "lokasi"=>$request->lokasi,
            "kota"=>$request->kota,
            "deskripsi"=>$request->deskripsi,
            "luas"=>$luas,
            "harga"=>$harga,
            "status"=>$request->status,
            "pemilik"=>$request->pemilik
        ];
        $lapa = new ModelsLapanganOlahraga();
        $lapa->updateLapangan($data);

        // Proses unggah foto baru
        if($request->has('foto')) {
            $destinasi = "/upload";
            foreach ($request->foto as $key => $value) {
                $foto = uniqid().".".$value->getClientOriginalExtension();
                $value->move(public_path($destinasi),$foto);
                $data2 = [
                    "nama"=>$foto,
                    "fk"=>$request->id
                ];
                $file = new filesLapanganOlahraga();
                $file->insertFilesLapangan($data2);
            }
        }
    
        // Proses hapus foto
        if($request->has('delete_photos')) {
            foreach ($request->delete_photos as $photo_id) {
                $photo = filesLapanganOlahraga::find($photo_id);
                if($photo) {
                    // Hapus file dari storage
                    @unlink(public_path("/upload/" . $photo->nama));
                    // Hapus record dari database
                    $photo->delete();
                }
            }
        }
        return redirect()->back()->with("success", "Berhasil Mengubah Detail Lapangan!");
    }

    public function editJam(Request $request){
        $request->validate([
            "hari1" => 'required',
            "buka1" => 'required',
            "tutup1" => 'required'
        ],[
            "hari1.required" => "hari operasional lapangan tidak boleh kosong!",
            "buka1.required" => "jam buka lapangan tidak boleh kosong!",
            "tutup1.required" => "jam tutup lapangan tidak boleh kosong!"
        ]);

        $index = 1;
        $prevJamTutup = null;
        $prevHari = null;

        while ($request->has("hari$index") && $request->has("buka$index") && $request->has("tutup$index")) {
            $hari = $request->input("hari$index");
            $jamBuka = $request->input("buka$index");
            $jamTutup = $request->input("tutup$index");

            if ($jamBuka >= $jamTutup) {
                return redirect()->back()->with('error', 'Tanggal dan Jam tidak valid!');
            }

            if ($prevHari === $hari && $prevJamTutup !== null) {
                // Pengecekan apakah jam buka lebih awal dari jam tutup pada entry sebelumnya
                if (strtotime($jamBuka) <= strtotime($prevJamTutup)) {
                    return redirect()->back()->with('error', 'Jam buka dari field berikutnya harus lebih dari jam tutup dari field sebelumnya!');
                }

                // Pengecekan apakah jam buka dari field saat ini sama dengan jam tutup dari field sebelumnya
                if (strtotime($jamBuka) === strtotime($prevJamTutup)) {
                    return redirect()->back()->with('error', 'Jam buka dari field berikutnya tidak boleh sama dengan jam tutup dari field sebelumnya jika hari sama!');
                }
            }

            if ($hari === $prevHari || $prevHari === null) {
                $prevJamTutup = $jamTutup;
                $prevHari = $hari;
            }

            if ($request->input("id_slot$index") != "null") {
                // $slot2 = DB::table('slot_waktu')->where('id_slot',"=", $request->input("id_slot$index"))->get();
                $data3 = [
                    "id" => $request->input("id_slot$index"),
                    "hari" => $request->input("hari$index"),
                    "buka" => $jamBuka,
                    "tutup" => $jamTutup
                ];
                $slot = new slotWaktu();
                $slot->updateSlot($data3);
            }
            else {
                $data4 = [
                    "hari" => $request->input("hari$index"),
                    "buka" => $jamBuka,
                    "tutup" => $jamTutup,
                    "lapangan" => $request->id
                ];
                $slot = new slotWaktu();
                $slot->insertSlot($data4);
            }

            $index++; // Pergi ke set input berikutnya
        }
        return redirect()->back()->with("success", "Berhasil Mengubah Jam Operasional Lapangan!");
    }

    public function editJamKhusus(Request $request){
        $request->validate([
            "tanggal1" => 'required',
            "mulai1" => 'required',
            "selesai1" => 'required'
        ],[
            "tanggal1.required" => "tanggal tutup lapangan tidak boleh kosong!",
            "mulai1.required" => "jam mulai tutup lapangan tidak boleh kosong!",
            "selesai1.required" => "jam selesai tutup lapangan tidak boleh kosong!"
        ]);

        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");
        $skrgg = new DateTime($skrg);

        $index = 1;
        $prevJamTutup = null;
        $prevHari = null;

        while ($request->has("tanggal$index") && $request->has("mulai$index") && $request->has("selesai$index")) {
            $tanggal = $request->input("tanggal$index");
            $jamBuka = $request->input("mulai$index");
            $jamTutup = $request->input("selesai$index");

            $buka = new DateTime($tanggal. " " .$jamBuka);
            $tutup = new DateTime($tanggal. " " .$jamTutup);

            // dd($buka);
            if ($buka >= $tutup) {
                return redirect()->back()->with('error', 'Tanggal dan Jam tidak valid!');
            }

            //cek apakah sdh ada transaksi di jam berikut
            $cek = DB::table('htrans')
                ->select("htrans.jam_sewa", "htrans.durasi_sewa", "htrans.tanggal_sewa", "extend_htrans.jam_sewa as jam_ext","extend_htrans.durasi_extend")
                ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                ->whereDate("htrans.tanggal_sewa", $tanggal)
                ->where("fk_id_lapangan","=",$request->id)
                ->where(function($query) {
                    $query->where("htrans.status_trans", "=", "Diterima")
                          ->orWhere("htrans.status_trans", "=", "Berlangsung");
                })
                ->get();

            if (!$cek->isEmpty()) {
                $conflict = false;
                foreach ($cek as $value) {
                    $booking_jam_selesai = date('H:i', strtotime("+$value->durasi_sewa hour", strtotime($value->jam_sewa)));
                    $booking_jam_selesai_ext = date('H:i', strtotime("+$value->durasi_extend hour", strtotime($value->jam_ext)));
                    
                    if (($jamBuka >= $value->jam_sewa && $jamBuka < $booking_jam_selesai) || 
                        ($jamTutup > $value->jam_sewa && $jamTutup <= $booking_jam_selesai) ||
                        ($jamBuka <= $value->jam_sewa && $jamTutup >= $booking_jam_selesai) ||
                        ($jamBuka >= $value->jam_ext && $jamBuka < $booking_jam_selesai_ext) || 
                        ($jamTutup > $value->jam_ext && $jamTutup <= $booking_jam_selesai_ext) ||
                        ($jamBuka <= $value->jam_ext && $jamTutup >= $booking_jam_selesai_ext)) {
                        
                        $conflict = true;
                        break;
                    }
                }
    
                if ($conflict) {
                    // Ada konflik dengan booking yang ada
                    return redirect()->back()->with('error', 'Maaf, Terdapat Booking pada jam tersebut!');
                }
            }

            if ($prevHari === $tanggal && $prevJamTutup !== null) {
                // Pengecekan apakah jam mulai lebih awal dari jam selesai pada entry sebelumnya
                if (strtotime($jamBuka) <= strtotime($prevJamTutup)) {
                    return redirect()->back()->with('error', 'Jam mulai dari field berikutnya harus lebih dari jam selesai dari field sebelumnya!');
                }

                // Pengecekan apakah jam mulai dari field saat ini sama dengan jam selesai dari field sebelumnya
                if (strtotime($jamBuka) === strtotime($prevJamTutup)) {
                    return redirect()->back()->with('error', 'Jam mulai dari field berikutnya tidak boleh sama dengan jam selesai dari field sebelumnya jika tanggal sama!');
                }
            }

            if ($tanggal === $prevHari || $prevHari === null) {
                $prevJamTutup = $jamTutup;
                $prevHari = $tanggal;
            }

            if ($request->input("id_jam$index") != "null") {
                $data3 = [
                    "id" => $request->input("id_jam$index"),
                    "tanggal" => $request->input("tanggal$index"),
                    "mulai" => $jamBuka,
                    "selesai" => $jamTutup
                ];
                $jam = new jamKhusus();
                $jam->updateJam($data3);
            }
            else {
                if ($skrgg > $buka) {
                    return redirect()->back()->with('error', 'Tanggal dan Jam tidak valid!');
                }

                $data4 = [
                    "tanggal" => $request->input("tanggal$index"),
                    "mulai" => $jamBuka,
                    "selesai" => $jamTutup,
                    "lapangan" => $request->id
                ];
                $jam = new jamKhusus();
                $jam->insertJam($data4);
            }

            $index++; // Pergi ke set input berikutnya
        }
        return redirect()->back()->with("success", "Berhasil Mengatur Jam Tutup Lapangan!");
    }

    public function hapusJam($id)
    {
        // dd($id);
        date_default_timezone_set("Asia/Jakarta");
        $skrg = date("Y-m-d H:i:s");

        $data = [
            "id" => $id,
            "delete" => $skrg
        ];
        $jam = new jamKhusus();
        $jam->deleteJam($data);

        return response()->json(['success' => true, 'message' => 'Berhasil!']);
    }

    public function searchLapangan(Request $request)
    {
        // $search = $request->input("cari");
        if (Session::get("role") == "admin") {
            $query = DB::table('lapangan_olahraga')->where('lapangan_olahraga.deleted_at',"=",null);
        }
        else {
            $query = DB::table('lapangan_olahraga')->where('lapangan_olahraga.deleted_at',"=",null)->where("lapangan_olahraga.status_lapangan","=","Aktif");
        }
    
        if ($request->filled('kategori')) {
            $query->where('lapangan_olahraga.fk_id_kategori', $request->kategori);
        }

        if ($request->filled('kota')) {
            $query->where('lapangan_olahraga.kota_lapangan', $request->kota);
        }
        
        if ($request->filled('cari')) {
            // $query->where('nama_lapangan', 'like', '%' . $request->cari . '%');
            $query->where(function ($q) use ($request) {
                $q->where('lapangan_olahraga.nama_lapangan', 'like', '%' . $request->cari . '%')
                  ->orWhereExists(function ($subQuery) use ($request) {
                      $subQuery->select(DB::raw(1))
                               ->from('pihak_tempat')
                               ->whereColumn('lapangan_olahraga.pemilik_lapangan', 'pihak_tempat.id_tempat')
                               ->where('pihak_tempat.nama_tempat', 'like', '%' . $request->cari . '%');
                  });
            });
        }

        // if ($request->filled('cariPemilik')) {
        //     $query = DB::table('lapangan_olahraga')
        //     ->where('lapangan_olahraga.deleted_at',"=",null)
        //     ->join('pihak_tempat', 'lapangan_olahraga.pemilik_lapangan', '=', 'pihak_tempat.id_tempat')
        //     ->where('pihak_tempat.nama_tempat', 'like', '%' . $request->cariPemilik . '%');
        //     // dd($query->get());
        // }
        
        $hasil = $query
                ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.kota_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->get();
        // dd($hasil);
        $kat = new kategori();
        $kategori = $kat->get_all_data();

        $kot = new ModelsLapanganOlahraga();
        $kota = $kot->get_kota();

        // $files = new filesLapanganOlahraga();

        //Cek role siapa yang sedang search alat
        $role = Session::get("role");
        if ($role == "pemilik") {
            // mengirimkan data ke tampilan
            return view('pemilik.cariLapangan', ['lapangan' => $hasil, 'kategori' => $kategori, 'kota' => $kota]);
        }
        else if ($role == "admin") {
            return view('admin.produk.cariLapangan', ['lapangan' => $hasil, 'kategori' => $kategori, 'kota' => $kota]);
        }
    }

    public function searchLapangan2(Request $request)
    {
        $query = DB::table('lapangan_olahraga')->where('lapangan_olahraga.deleted_at',"=",null)->where("lapangan_olahraga.status_lapangan","=","Aktif");
    
        if ($request->filled('kategori')) {
            $query->where('lapangan_olahraga.fk_id_kategori', $request->kategori);
        }

        if ($request->filled('kota')) {
            $query->where('lapangan_olahraga.kota_lapangan', $request->kota);
        }
        
        if ($request->filled('cari')) {
            // $query->where('nama_lapangan', 'like', '%' . $request->cari . '%');
            $query->where(function ($q) use ($request) {
                $q->where('lapangan_olahraga.nama_lapangan', 'like', '%' . $request->cari . '%')
                  ->orWhereExists(function ($subQuery) use ($request) {
                      $subQuery->select(DB::raw(1))
                               ->from('pihak_tempat')
                               ->whereColumn('lapangan_olahraga.pemilik_lapangan', 'pihak_tempat.id_tempat')
                               ->where('pihak_tempat.nama_tempat', 'like', '%' . $request->cari . '%');
                  });
            });
        }
        
        $hasil = $query
                ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.kota_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->get();
        // dd($hasil);
        $kat = new kategori();
        $kategori = $kat->get_all_data();

        $kot = new ModelsLapanganOlahraga();
        $kota = $kot->get_kota();

        // $files = new filesLapanganOlahraga();
        // dd($hasil);
        return view('daftarLapangan', ['lapangan' => $hasil, 'kategori' => $kategori, 'kota' =>$kota]);
    }

    public function searchLapanganCustomer(Request $request)
    {
        $query = DB::table('lapangan_olahraga')->where('lapangan_olahraga.deleted_at',"=",null)->where("lapangan_olahraga.status_lapangan","=","Aktif");
    
        if ($request->filled('kategori')) {
            $query->where('lapangan_olahraga.fk_id_kategori',"=", $request->kategori);
        }

        if ($request->filled('kota')) {
            $query->where('lapangan_olahraga.kota_lapangan',"=", $request->kota);
        }
        // dd($query->get());
        
        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('lapangan_olahraga.nama_lapangan', 'like', '%' . $request->cari . '%')
                  ->orWhereExists(function ($subQuery) use ($request) {
                      $subQuery->select(DB::raw(1))
                               ->from('pihak_tempat')
                               ->whereColumn('lapangan_olahraga.pemilik_lapangan', 'pihak_tempat.id_tempat')
                               ->where('pihak_tempat.nama_tempat', 'like', '%' . $request->cari . '%');
                  });
            });
        }

        // if ($request->filled('cariPemilik')) {
        //     $query = DB::table('lapangan_olahraga')
        //     ->where('lapangan_olahraga.deleted_at',"=",null)
        //     ->join('pihak_tempat', 'lapangan_olahraga.pemilik_lapangan', '=', 'pihak_tempat.id_tempat')
        //     ->where('pihak_tempat.nama_tempat', 'like', '%' . $request->cariPemilik . '%');
        //     // dd($query->get());
        // }
        
        $hasil = $query
                ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.kota_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->get();
        // dd($hasil);
        $kat = new kategori();
        $kategori = $kat->get_all_data();
        $kot = new ModelsLapanganOlahraga();
        $kota = $kot->get_kota();

        // $files = new filesLapanganOlahraga();

        return view('customer.beranda', ['lapangan' => $hasil, 'kategori' => $kategori, 'kota' =>$kota]);
    }

    public function daftarLapangan() {
        $role = Session::get("dataRole")->id_tempat;

        $data = DB::table('lapangan_olahraga')
                ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.status_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("lapangan_olahraga.pemilik_lapangan", "=", $role)
                ->where("lapangan_olahraga.deleted_at","=",null)
                ->get();
        // dd($data);

        $param["lapangan"] = $data;
        return view("tempat.lapangan.daftarLapangan")->with($param);
    }

    public function cariLapangan() {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        
        $kot = new ModelsLapanganOlahraga();
        $param["kota"] = $kot->get_kota();

        $data = DB::table('lapangan_olahraga')
                ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.kota_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("lapangan_olahraga.status_lapangan", "=", "Aktif")
                ->where("lapangan_olahraga.deleted_at","=",null)
                ->get();
                // dd($data);
        $param["lapangan"] = $data;
        
        if (Session::get("role") == "pemilik") {
            return view("pemilik.cariLapangan")->with($param);
        }
        else if (Session::get("role") == "customer") {
            return view("customer.beranda")->with($param);
        }
    }

    public function cariLapangan2() {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $kot = new ModelsLapanganOlahraga();
        $param["kota"] = $kot->get_kota();

        $data = DB::table('lapangan_olahraga')
                ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.kota_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("lapangan_olahraga.status_lapangan", "=", "Aktif")
                ->where("lapangan_olahraga.deleted_at","=",null)
                ->get();
                // dd($data);
        $param["lapangan"] = $data;
        
        return view("daftarLapangan")->with($param);
    }

    public function cariLapanganAdmin() {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();

        $kot = new ModelsLapanganOlahraga();
        $param["kota"] = $kot->get_kota();

        $data = DB::table('lapangan_olahraga')
                ->select("lapangan_olahraga.id_lapangan","lapangan_olahraga.nama_lapangan", "files_lapangan.nama_file_lapangan", "lapangan_olahraga.harga_sewa_lapangan","lapangan_olahraga.kota_lapangan")
                ->joinSub(function($query) {
                    $query->select("fk_id_lapangan", "nama_file_lapangan")
                        ->from('files_lapangan')
                        ->whereRaw('id_file_lapangan = (select min(id_file_lapangan) from files_lapangan as f2 where f2.fk_id_lapangan = files_lapangan.fk_id_lapangan)');
                }, 'files_lapangan', 'lapangan_olahraga.id_lapangan', '=', 'files_lapangan.fk_id_lapangan')
                ->where("lapangan_olahraga.deleted_at","=",null)
                ->get();
        $param["lapangan"] = $data;
        
        return view("admin.produk.cariLapangan")->with($param);
    }

    public function detailLapanganUmumPemilik($id) {
        $lapa = new ModelsLapanganOlahraga();
        $param["lapangan"] = $lapa->get_all_data_by_id($id);
        $files = new filesLapanganOlahraga();
        $param["files"] = $files->get_all_data($id);
        // dd($files->get_all_data($id)->first()->nama_file_alat);
        $role = Session::get("dataRole")->id_pemilik;
        $alat = new alatOlahraga();
        $param["alat"] = $alat->get_all_data_status($role);
        $slot = new slotWaktu();
        $param["slot"] = $slot->get_all_data_by_lapangan($id);
        $jam = new jamKhusus();
        $param["jam"] = $jam->get_all_data_by_lapangan($id);

        $per = new requestPermintaan();
        $param["permintaan"] = $per->get_all_data_by_lapangan($id);
        $pen = new requestPenawaran();
        $param["penawaran"] = $pen->get_all_data_by_lapangan($id);
        $sewa = new sewaSendiri();
        $param["sewa"] = $sewa->get_all_data_by_lapangan($id);

        $rating = new ratingLapangan();
        $avg = $rating->get_avg_data($id);
        $param["averageRating"] = round($avg, 1);
        $param["totalReviews"] = $rating->get_data_count($id);
        $param["rating"] = $rating->get_data_by_id_lapangan($id);

        if (!$lapa->get_all_data_by_id($id)->isEmpty()) {
            $tempat = new pihakTempat();
            $id_tempat = $lapa->get_all_data_by_id($id)->first()->pemilik_lapangan;
            $param["dataTempat"] = $tempat->get_all_data_by_id($id_tempat)->first();

            $kategori = new kategori();
            $id_kategori = $lapa->get_all_data_by_id($id)->first()->fk_id_kategori;
            $param["kat"] = $kategori->get_all_data_by_id($id_kategori)->first()->nama_kategori;
        }

        return view("pemilik.detailLapanganUmum")->with($param);
    }

    public function detailLapanganTempat($id) {
        $lap = new ModelsLapanganOlahraga();
        $param["lapangan"] = $lap->get_all_data_by_id($id);
        $files = new filesLapanganOlahraga();
        $param["files"] = $files->get_all_data($id);
        $alat = new alatOlahraga();
        $param["alat"] = $alat->get_all_data3(Session::get("dataRole")->id_tempat);
        $slot = new slotWaktu();
        $param["slot"] = $slot->get_all_data_by_lapangan($id);
        $jam = new jamKhusus();
        $param["jam"] = $jam->get_all_data_by_lapangan($id);

        $per = new requestPermintaan();
        $param["permintaan"] = $per->get_all_data_by_lapangan($id);
        $pen = new requestPenawaran();
        $param["penawaran"] = $pen->get_all_data_by_lapangan($id);

        $sewa = new sewaSendiri();
        $param["sewa"] = $sewa->get_all_data_by_lapangan($id);

        $rating = new ratingLapangan();
        $avg = $rating->get_avg_data($id);
        $param["averageRating"] = round($avg, 1);
        $param["totalReviews"] = $rating->get_data_count($id);
        $param["rating"] = $rating->get_data_by_id_lapangan($id);
        return view("tempat.lapangan.detailLapangan")->with($param);
    }

    public function editLapanganTempat($id) {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        $lap = new ModelsLapanganOlahraga();
        $param["lapangan"] = $lap->get_all_data_by_id($id);
        $files = new filesLapanganOlahraga();
        $param["files"] = $files->get_all_data($id);
        return view("tempat.lapangan.editLapangan")->with($param);
    }

    public function jamLapangan($id) {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        $lap = new ModelsLapanganOlahraga();
        $param["lapangan"] = $lap->get_all_data_by_id($id);
        $slot = new slotWaktu();
        $param["slot"] = $slot->get_all_data_by_lapangan($id);
        $jam = new jamKhusus();
        $param["jam"] = $jam->get_all_data_by_lapangan($id);
        return view("tempat.lapangan.jamLapangan")->with($param);
    }

    public function detailLapanganCustomer($id) {
        $kat = new kategori();
        $param["kategori"] = $kat->get_all_data();
        $kot = new ModelsLapanganOlahraga();
        $param["kota"] = $kot->get_kota();
        $lapa = new ModelsLapanganOlahraga();
        $param["lapangan"] = $lapa->get_all_data_by_id($id);
        $files = new filesLapanganOlahraga();
        $param["files"] = $files->get_all_data($id);
        $slot = new slotWaktu();
        $param["slot"] = $slot->get_all_data_by_lapangan($id);
        $jam = new jamKhusus();
        $param["jam"] = $jam->get_all_data_by_lapangan($id);

        $per = new requestPermintaan();
        $param["permintaan"] = $per->get_all_data_by_lapangan($id);
        $pen = new requestPenawaran();
        $param["penawaran"] = $pen->get_all_data_by_lapangan($id);
        $sewa = new sewaSendiri();
        $param["sewa"] = $sewa->get_all_data_by_lapangan($id);

        $rating = new ratingLapangan();
        $avg = $rating->get_avg_data($id);
        $param["averageRating"] = round($avg, 1);
        $param["totalReviews"] = $rating->get_data_count($id);
        $param["rating"] = $rating->get_data_by_id_lapangan($id);

        if (!$lapa->get_all_data_by_id($id)->isEmpty()) {
            $tempat = new pihakTempat();
            $id_tempat = $lapa->get_all_data_by_id($id)->first()->pemilik_lapangan;
            $param["dataTempat"] = $tempat->get_all_data_by_id($id_tempat)->first();
            $kategori = new kategori();
            $id_kategori = $lapa->get_all_data_by_id($id)->first()->fk_id_kategori;
            $param["kat"] = $kategori->get_all_data_by_id($id_kategori)->first()->nama_kategori;
        }

        $param["dataJadwal"] = DB::table('htrans')
                            ->select("htrans.tanggal_sewa","htrans.status_trans","htrans.jam_sewa","htrans.durasi_sewa","extend_htrans.durasi_extend", "extend_htrans.status_extend")
                            ->leftJoin("extend_htrans","htrans.id_htrans","=","extend_htrans.fk_id_htrans")
                            ->where("htrans.fk_id_lapangan","=",$id)
                            ->where(function($query) {
                                $query->where("htrans.status_trans", "=", "Diterima")
                                    ->orWhere("htrans.status_trans", "=", "Berlangsung");
                            })
                            ->get();

        return view("customer.detailLapangan")->with($param);
    }

    public function detailLapanganUmumAdmin($id) {
        $lapa = new ModelsLapanganOlahraga();
        $param["lapangan"] = $lapa->get_all_data_by_id($id);
        $files = new filesLapanganOlahraga();
        $param["files"] = $files->get_all_data($id);
        $slot = new slotWaktu();
        $param["slot"] = $slot->get_all_data_by_lapangan($id);
        $jam = new jamKhusus();
        $param["jam"] = $jam->get_all_data_by_lapangan($id);

        $per = new requestPermintaan();
        $param["permintaan"] = $per->get_all_data_by_lapangan($id);
        $pen = new requestPenawaran();
        $param["penawaran"] = $pen->get_all_data_by_lapangan($id);
        $sewa = new sewaSendiri();
        $param["sewa"] = $sewa->get_all_data_by_lapangan($id);

        if (!$lapa->get_all_data_by_id($id)->isEmpty()) {
            $tempat = new pihakTempat();
            $id_tempat = $lapa->get_all_data_by_id($id)->first()->pemilik_lapangan;
            $param["dataTempat"] = $tempat->get_all_data_by_id($id_tempat)->first();
        }

        $rating = new ratingLapangan();
        $avg = $rating->get_avg_data($id);
        $param["averageRating"] = round($avg, 1);
        $param["totalReviews"] = $rating->get_data_count($id);
        $param["rating"] = $rating->get_data_by_id_lapangan($id);
        
        return view("admin.produk.detailLapanganUmum")->with($param);
    }
}
