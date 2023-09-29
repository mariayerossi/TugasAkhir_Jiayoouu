<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        date_default_timezone_set("Asia/Jakarta");
        DB::table('user')->insert([
            'nama_user' => "Maria Yerossi",
            'email_user' => 'maria@gmail.com',
            'telepon_user' => "082374822343",
            'password_user' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_user' => "XQ==",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('pemilik_alat')->insert([
            'nama_pemilik' => "Andika Pratama",
            'email_pemilik' => 'andika@gmail.com',
            'telepon_pemilik' => "086768686774",
            'ktp_pemilik' => "ktp_pemilik1.jpg",
            'password_pemilik' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_pemilik' => "XQ==",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('register_tempat')->insert([
            'nama_tempat_reg' => "Herry Sport",
            'nama_pemilik_tempat_reg' => "Herry Tanoe",
            'email_tempat_reg' => 'herry12@gmail.com',
            'telepon_tempat_reg' => "0843235434528",
            'alamat_tempat_reg' => "mayjend sungkono, Surabaya",
            'ktp_tempat_reg' => "ktp_tempat1.jpg",
            'npwp_tempat_reg' => "npwp_tempat1.jpg",
            'password_tempat_reg' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_tempat_reg' => "XQ==",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('pihak_tempat')->insert([
            'nama_tempat' => "Mario Sport",
            'nama_pemilik_tempat' => "Mario Wijaya",
            'email_tempat' => 'mario12@gmail.com',
            'telepon_tempat' => "086547823741",
            'alamat_tempat' => "ngagel jaya, Surabaya",
            'ktp_tempat' => "ktp_tempat1.jpg",
            'npwp_tempat' => "npwp_tempat1.jpg",
            'password_tempat' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_tempat' => "XQ==",
            'created_at' => date("Y-m-d H:i:s"),
        ]);
        
        DB::table('kategori')->insert([
            "nama_kategori" => "Basket",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('kategori')->insert([
            "nama_kategori" => "Futsal",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Basket Molten",
            'kategori_alat' => "Basket",
            'deskripsi_alat' => "Molten Adalah Bola Basket Resmi FIBA & PERBASI
            Salah Satu Distributor RESMI Bola Original Molten Di Indonesia adalah TokoMekari
            *Bola Original Selalu ada Logo PERBASI & IBL
            
            INFO PRODUK :
            COVER MATERIAL : RUBBER (Karet)
            CONSTRUCTION : Molded
            SIZE : 6 ( B6G2010 )
            BLADDER : Butyl
            REMARK : FIBA APPROVED ( REKOMENDASI BUAT OUTDOOR ), Bola Original Hanya mendapatkan Bola",
            'berat_alat' => "20.5",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 20000,
            'ganti_rugi_alat' => 100000,
            'kota_alat' => "Jakarta",
            'status_alat' => "Aktif",
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_basket1.jpg",
            'fk_id_alat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_basket2.jpg",
            'fk_id_alat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('lapangan_olahraga')->insert([
            'nama_lapangan' => "Lapangan Basket Mario 1",
            'kategori_lapangan' => "Basket",
            'tipe_lapangan' => "Outdoor",
            'lokasi_lapangan' => "jln. ngagel tengah no.23, Surabaya",
            'kota_lapangan' => "Surabaya",
            'deskripsi_lapangan' => "Fitur Lapangan:

            - Lantai bertekstur khusus untuk cengkeraman sepatu yang optimal.
            - Ring basket yang memenuhi standar kompetisi.
            - Penerangan LED terang untuk permainan malam hari.
            - Area parkir yang luas dan mudah diakses.
            - Kursi penonton bagi yang ingin mendukung timnya.
            - Fasilitas toilet dan kamar ganti bersih.",
            'luas_lapangan' => "28x15",
            'harga_sewa_lapangan' => 100000,
            'status_lapangan' => "Aktif",
            'pemilik_lapangan' => 1,
            'created_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('files_lapangan')->insert([
            'nama_file_lapangan' => "lapangan_basket1.jpg",
            'fk_id_lapangan' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_lapangan')->insert([
            'nama_file_lapangan' => "lapangan_basket2.jpg",
            'fk_id_lapangan' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_lapangan')->insert([
            'nama_file_lapangan' => "lapangan_basket3.jpg",
            'fk_id_lapangan' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '08:00',
            'jam_tutup' => '12:00',
            'fk_id_lapangan' => 1
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Selasa",
            'jam_buka' => '08:00',
            'jam_tutup' => '20:00',
            'fk_id_lapangan' => 1
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '15:00',
            'jam_tutup' => '20:00',
            'fk_id_lapangan' => 1
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Rabu",
            'jam_buka' => '08:00',
            'jam_tutup' => '20:00',
            'fk_id_lapangan' => 1
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Futsal Specs",
            'kategori_alat' => "Futsal",
            'deskripsi_alat' => "BRAND : SPECSSS
            MODEL : FUTSAL
            MATERIAL : PVC, FOAM, POLYESTER FABRIC AND RUBBER
            MODE : COLD PRESS / SAWING
            APLICATION : INDOOR & OUTDOOR
            SIZE : 4 FUTSAL
            
            Product Features
            
            SPECSS base-level basketball
            A great 30 panel kick-about ball for the next generation
            Lightweight & durable perfect for indoor and outdoor",
            'berat_alat' => "25.5",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 25000,
            'ganti_rugi_alat' => 150000,
            'kota_alat' => "Surabaya",
            'status_alat' => "Aktif",
            'fk_id_pemilik' => null,
            'fk_id_tempat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_futsal1.jpg",
            'fk_id_alat' => 2,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_futsal2.jpg",
            'fk_id_alat' => 2,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_permintaan')->insert([
            'req_harga_sewa' => 50000,
            'req_lapangan' => 1,
            'req_tanggal_mulai' => "2023-9-13",
            'req_tanggal_selesai' => "2023-10-13",
            'req_id_alat' => 1,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 1,
            'tanggal_minta' => date("Y-m-d H:i:s"),
            'status_permintaan' => "Diterima",
            'kode_mulai' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('negosiasi')->insert([
            'isi_negosiasi' => "halo kak! untuk harga sewa tidak bisa dipertimbangkan lagi?",
            'waktu_negosiasi' => date("Y-m-d H:i:s"),
            'fk_id_permintaan' => 1,
            'fk_id_penawaran' => null,
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_penawaran')->insert([
            'req_harga_sewa' => 40000,
            'req_lapangan' => 1,
            'req_tanggal_mulai' => "2023-09-13",
            'req_tanggal_selesai' => "2023-11-13",
            'req_id_alat' => 1,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 1,
            'tanggal_tawar' => date("Y-m-d H:i:s"),
            'status_penawaran' => "Diterima",
            'status_tempat' => "Setuju",
            'status_pemilik' => "Setuju",
            'kode_mulai' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('negosiasi')->insert([
            'isi_negosiasi' => "hai! saya mau menawarkan alat olahraga, monggo mungkin tertarik untuk menyewakannya",
            'waktu_negosiasi' => date("Y-m-d H:i:s"),
            'fk_id_permintaan' => null,
            'fk_id_penawaran' => 1,
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('sewa_sendiri')->insert([
            'req_lapangan' => 1,
            'req_id_alat' => 2,
            'fk_id_tempat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('htrans')->insert([
            'kode_trans' => "H".date("dmy")."0001",
            'fk_id_lapangan' => 1,
            'subtotal_lapangan' => 200000,
            'subtotal_alat' => 150000,
            'tanggal_trans' => date("Y-m-d H:i:s"),
            'tanggal_sewa'=> '2023-09-20',
            'jam_sewa' => '16:00',
            'durasi_sewa' => 2,
            'total_trans' => 350000,
            'fk_id_user' => 1,
            'fk_id_tempat' => 1,
            'pendapatan_website_lapangan' => 18000,
            'status_trans' => "Menunggu",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('dtrans')->insert([
            'fk_id_htrans' => 1,
            'fk_id_alat' => 1,
            'harga_sewa_alat' => 50000,
            'subtotal_alat' => 100000,
            'total_komisi_pemilik' => 40000,
            'total_komisi_tempat' => 60000,
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'pendapatan_website_alat' => 4400,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('dtrans')->insert([
            'fk_id_htrans' => 1,
            'fk_id_alat' => 2,
            'harga_sewa_alat' => 25000,
            'subtotal_alat' => 50000,
            'total_komisi_pemilik' => null,
            'total_komisi_tempat' => 50000,
            'fk_id_pemilik' => null,
            'fk_id_tempat' => 1,
            'pendapatan_website_alat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('komplain_request')->insert([
            'jenis_komplain' => "Alat tidak sesuai",
            'keterangan_komplain' => "alat olahraga yang dikirim dan yang dijelaskan di detail beda jauh",
            'fk_id_permintaan' => null,
            'fk_id_penawaran' => 1,
            'waktu_komplain' => date("Y-m-d H:i:s"),
            'status_komplain' => "Menunggu",
            'penanganan_komplain' => null,
            'fk_id_pemilik' => null,
            'fk_id_tempat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_komplain_req')->insert([
            'nama_file_komplain' => "bola_jelek.jpg",
            'fk_id_komplain_req' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('htrans')->insert([
            'kode_trans' => "H".date("dmy")."0002",
            'fk_id_lapangan' => 1,
            'subtotal_lapangan' => 200000,
            'subtotal_alat' => 150000,
            'tanggal_trans' => date("Y-m-d H:i:s"),
            'tanggal_sewa'=> '2023-10-20',
            'jam_sewa' => '16:00',
            'durasi_sewa' => 2,
            'total_trans' => 350000,
            'fk_id_user' => 1,
            'fk_id_tempat' => 1,
            'pendapatan_website_lapangan' => 18000,
            'status_trans' => "Menunggu",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('dtrans')->insert([
            'fk_id_htrans' => 2,
            'fk_id_alat' => 1,
            'harga_sewa_alat' => 50000,
            'subtotal_alat' => 100000,
            'total_komisi_pemilik' => 40000,
            'total_komisi_tempat' => 60000,
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'pendapatan_website_alat' => 4400,
            'created_at' => date("Y-m-d H:i:s"),
        ]);
    }
}
