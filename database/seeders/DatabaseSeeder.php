<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use DateInterval;
use DateTime;
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

        //User
        DB::table('user')->insert([
            'nama_user' => "Maria Yerossi",
            'email_user' => 'maria@gmail.com',
            'telepon_user' => "082374822343",
            'password_user' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_user' => "X0lDVVNCVQ==",//2 jt
            'email_verified_at' => date("Y-m-d H:i:s"),
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('user')->insert([
            'nama_user' => "Lusiana Putri",
            'email_user' => 'lusi@gmail.com',
            'telepon_user' => "085436876789",
            'password_user' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_user' => "XElDVVNCVUQ=",//10 jt
            'email_verified_at' => date("Y-m-d H:i:s"),
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //Pemilik Alat
        DB::table('pemilik_alat')->insert([
            'nama_pemilik' => "Andika Pratama",
            'email_pemilik' => 'andika@gmail.com',
            'telepon_pemilik' => "086768686774",
            'kota_pemilik' => "Surabaya",
            'ktp_pemilik' => "ktp_pemilik1.jpg",
            'password_pemilik' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_pemilik' => "VUlDVVNC",//800.000
            'norek_pemilik' => 454782937193,
            'nama_rek_pemilik' => "Andika Pratama",
            'nama_bank_pemilik' => "BCA",
            'email_verified_at' => date("Y-m-d H:i:s"),
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('pemilik_alat')->insert([
            'nama_pemilik' => "Budi Santoso",
            'email_pemilik' => 'budi@gmail.com',
            'telepon_pemilik' => "084552673167",
            'kota_pemilik' => "Jakarta",
            'ktp_pemilik' => "ktp_pemilik2.jpeg",
            'password_pemilik' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_pemilik' => "VUlDVVNC",//800.000
            'norek_pemilik' => 887391238712,
            'nama_rek_pemilik' => "Budi Santoso",
            'nama_bank_pemilik' => "BCA",
            'email_verified_at' => date("Y-m-d H:i:s"),
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //Register Tempat
        DB::table('register_tempat')->insert([
            'nama_tempat_reg' => "Herry Sport",
            'nama_pemilik_tempat_reg' => "Herry Tanoe",
            'email_tempat_reg' => 'herry12@gmail.com',
            'telepon_tempat_reg' => "0843235434528",
            'alamat_tempat_reg' => "mayjend sungkono, Surabaya",
            'kota_tempat_reg' => "Surabaya",
            'ktp_tempat_reg' => "ktp_tempat1.jpg",
            'npwp_tempat_reg' => "npwp_tempat1.jpg",
            'password_tempat_reg' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_tempat_reg' => "VUlDVVNC",//800.000
            'email_verified_at' => date("Y-m-d H:i:s"),
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //Pihak Tempat
        DB::table('pihak_tempat')->insert([
            'nama_tempat' => "Mario Sport",
            'nama_pemilik_tempat' => "Mario Wijaya",
            'email_tempat' => 'mario12@gmail.com',
            'telepon_tempat' => "086547823741",
            'alamat_tempat' => "ngagel jaya, Surabaya",
            'kota_tempat' => "Surabaya",
            'ktp_tempat' => "ktp_tempat1.jpg",
            'npwp_tempat' => "npwp_tempat1.jpg",
            'password_tempat' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_tempat' => "VUlDVVNC",//800.000
            'norek_tempat' => 763817136363,
            'nama_rek_tempat' => "Mario Wijaya",
            'nama_bank_tempat' => "BCA",
            'email_verified_at' => date("Y-m-d H:i:s"),
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('pihak_tempat')->insert([
            'nama_tempat' => "Daniel Sport",
            'nama_pemilik_tempat' => "Daniel Santosa",
            'email_tempat' => 'daniel@gmail.com',
            'telepon_tempat' => "086565721091",
            'alamat_tempat' => "darmo indah utara, Jakarta",
            'kota_tempat' => "Jakarta",
            'ktp_tempat' => "ktp_tempat2.jpeg",
            'npwp_tempat' => "npwp_tempat2.jpg",
            'password_tempat' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_tempat' => "VUlDVVNC",//800.000
            'norek_tempat' => 4677673123918,
            'nama_rek_tempat' => "Daniel Santosa",
            'nama_bank_tempat' => "BCA",
            'email_verified_at' => date("Y-m-d H:i:s"),
            'created_at' => date("Y-m-d H:i:s"),
        ]);
        
        //Kategori
        DB::table('kategori')->insert([
            "nama_kategori" => "Basket",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('kategori')->insert([
            "nama_kategori" => "Futsal",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('kategori')->insert([
            "nama_kategori" => "Voli",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('kategori')->insert([
            "nama_kategori" => "Tenis",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('kategori')->insert([
            "nama_kategori" => "Badminton",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //Alat olahraga
        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Basket Molten",
            'fk_id_kategori' => 1,
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
            'status_alat' => "Non Aktif",
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_basket3.jpg",
            'fk_id_alat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_basket2.jpg",
            'fk_id_alat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Basket Spalding",
            'fk_id_kategori' => 1,
            'deskripsi_alat' => "Bola futsal specs BOLT motif terbaru qualitas terbaik di kelasnya, produk sudah sesuai standart jadi di jamin produk berkualitas, Apabila produk tidak memuaskan silahkan di RETUR!!

            Bola Futsal specs BOLT ukuran 4, Menggunakan bahan pilihan terbaik yang di recomendasikan untuk bola futsal supaya menghasilkan produk terbaik.
            Bola ini cocok di gunakan indoor atau pun outdoor.",
            'berat_alat' => "20.5",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 20000,
            'ganti_rugi_alat' => 100000,
            'status_alat' => "Aktif",
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_basket6.jpg",
            'fk_id_alat' => 2,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Futsal Ortuseight",
            'fk_id_kategori' => 2,
            'deskripsi_alat' => "BRAND : ORTUSEIGHT
            MODEL : LIGHTNING
            MATERIAL : 60 RUBBER ,15%POLYURETHANE ,13% POLYESTER ,12% EVA
            MODE : PRES
            APLICATION : FUTSAL
            ORIGIN : CHINA
            SIZE : 4",
            'berat_alat' => "20.5",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 10000,
            'ganti_rugi_alat' => 100000,
            'status_alat' => "Aktif",
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_futsal5.jpg",
            'fk_id_alat' => 3,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Voli Molten",
            'fk_id_kategori' => 3,
            'deskripsi_alat' => "Bola Voli Volly MOLTEN V5M 5000 Grade Ori Gratis Pentil dan Jaring Bola

            berbahan lembut mirip seperti ori
            kuat dan tahan lama
            warna 100 % mirip ori
            pantulan 90% ori
            tidak panas, lemut dan empuk ditangan
            barang sesuai di gambar.",
            'berat_alat' => "20.5",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 15000,
            'ganti_rugi_alat' => 100000,
            'status_alat' => "Aktif",
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_voli2.jpg",
            'fk_id_alat' => 4,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Raket Tenis VCORE PRO 100",
            'fk_id_kategori' => 4,
            'deskripsi_alat' => '"VCORE PRO 100
            For intermediate to advanced players looking for a flexible racquet with precision and feel
            
            More Information:
            Head Size: 100 sq.in.
            Weight: 300g / 10.6oz
            Grip Size: 1 - 5
            Length: 27 in.
            Width Range: 23 mm - 23 mm - 23 mm
            Balance Point: 320 mm',
            'berat_alat' => "300",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 20000,
            'ganti_rugi_alat' => 100000,
            'status_alat' => "Aktif",
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "raket_tenis1.jpg",
            'fk_id_alat' => 5,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Tenis Frasser 812",
            'fk_id_kategori' => 4,
            'deskripsi_alat' => "bola tenis
            bola kasti
            harga per biji
            kwalitas premium
            full imports
            Melayani dropshipper, grosir besar dan pemesanan khusus seluruh Indonesia
            Untuk pembelian grosir dan barang yang volume kubikasi besar atau berat bisa request pengiriman yang lebih murah seperti jne trucking, dakota, indah cargo, baraka dan lain lain nya.
            Untuk request pengiriman bisa chat kami melalui diskusi / pesan.",
            'berat_alat' => "270",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 20000,
            'ganti_rugi_alat' => 150000,
            'status_alat' => "Aktif",
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_tenis1.jpg",
            'fk_id_alat' => 6,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Raket Badminton Warna Warni",
            'fk_id_kategori' => 5,
            'deskripsi_alat' => "KETERANGAN PRODUK :
            ► 100% Original (Signature Product)
            ► Untuk Raket Badminton / Raket Tennis.
            ► Panjang Grip Sekitar 110-115 CM.
            ► Bahan Terbuat Dari Karet Berkualitas Tinggi (Anti Slip / Tidak Licin).
            ► Terdapat Foam Yang Membentuk Tulang Pada Karet.",
            'berat_alat' => "270",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 20000,
            'ganti_rugi_alat' => 150000,
            'status_alat' => "Aktif",
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "raket_badminton1.jpg",
            'fk_id_alat' => 7,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Basket Proteam Rubber Royale Edition",
            'fk_id_kategori' => 1,
            'deskripsi_alat' => "Bola Basket Proteam Rubber Royale Edition (DBL Licensed)

            Proteam Royale Orange Bola Basket adalah bola basket yang terbuat dari bahan Rubber berkualitas sehingga cocok untuk digunakan saat latihan maupun pertandingan. Bola basket ini memiliki bahan lembut dan empuk sehingga menghasilkan performa yang bagus. Memenuhi standar FIBA dengan desain menggunakan 8 panel & terbuat dari bahan material PU membuat bola terasa lembut dan kuat saat digunakan.",
            'berat_alat' => "300",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 20000,
            'ganti_rugi_alat' => 150000,
            'status_alat' => "Aktif",
            'fk_id_pemilik' => 2,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_basket5.jpg",
            'fk_id_alat' => 8,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Raket Tenis Wilson Ultra Power 105",
            'fk_id_kategori' => 4,
            'deskripsi_alat' => 'Wilson Ultra Power 105 menekankan pada kekuatan ringan untuk pemain tenis menengah. Dengan ukuran kepala 105 inci persegi, raket ini cocok dengan kategori "midplus", yang dirancang untuk memberikan keseimbangan kekuatan dan kontrol. Untuk beberapa kekuatan tambahan dan pengampunan, itu dilengkapi dengan sweet spot yang diperbesar untuk, membantu pemain untuk memukul tembakan yang lebih konsisten.',
            'berat_alat' => "300",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 10000,
            'ganti_rugi_alat' => 120000,
            'status_alat' => "Aktif",
            'fk_id_pemilik' => 2,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "raket_tenis2.jpg",
            'fk_id_alat' => 9,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Voli Mikasa FIV3",
            'fk_id_kategori' => 4,
            'deskripsi_alat' => "Spesifikasi Produk :
                ✅Bola Voli/Volly Press Pabrikan Kwalitas Import
                ✅Bahan PU Lembut dan Empuk saat dipakai.
                ✅Ukuran Size 5.
                ✅Berat Bola saat digunakan berkisar 260-280gram.
                ✅Keliling lingkaran bola 65-66cm.
                ✅Diameter bola 20,7 - 21 cm.
                ✅Dilengkapi jaring dan adapter pentil.
                ✅Direkomendasikan untuk latihan usia remaja dan dewasa.",
            'berat_alat' => "270",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 17000,
            'ganti_rugi_alat' => 130000,
            'status_alat' => "Aktif",
            'fk_id_pemilik' => 2,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_voli1.jpg",
            'fk_id_alat' => 10,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Futsal Adidas",
            'fk_id_kategori' => 4,
            'deskripsi_alat' => "Bahan: PU Leather Glossy
            Berat: 400-440 Gram
            Keliling: 62-64 Cm
            Size: 4
            Blader: Latex
            Sistem Pembuatan: Jahit Mesin",
            'berat_alat' => "270",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 15000,
            'ganti_rugi_alat' => 150000,
            'status_alat' => "Non Aktif",
            'fk_id_pemilik' => 2,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_futsal7.jpg",
            'fk_id_alat' => 11,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Basket Guard",
            'fk_id_kategori' => 1,
            'deskripsi_alat' => "Guard tosca basketball

            Spesifikasi :
            -Bahan leather/kulit berkualitas (persis bahan mo*ten)
            -pantulan enak
            -awet
            -bisa untuk indoor / oudoor
            -ukuran 6 (bisa untuk anak2 ataupun wanita)",
            'berat_alat' => "270",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 15000,
            'ganti_rugi_alat' => 120000,
            'status_alat' => "Non Aktif",
            'fk_id_pemilik' => 2,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_basket7.jpg",
            'fk_id_alat' => 12,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Basket Adidas",
            'fk_id_kategori' => 1,
            'deskripsi_alat' => "Bola Basket Adidas All Court X35859

            X35859",
            'berat_alat' => "270",
            'ukuran_alat' => "10x10x10",
            'komisi_alat' => 20000,
            'ganti_rugi_alat' => 200000,
            'status_alat' => "Non Aktif",
            'fk_id_pemilik' => 2,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_basket8.jpg",
            'fk_id_alat' => 13,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //Lapangan Olahraga
        DB::table('lapangan_olahraga')->insert([
            'nama_lapangan' => "Lapangan Basket Mario 1",
            'fk_id_kategori' => 1,
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

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '08:00',
            'jam_tutup' => '12:00',
            'fk_id_lapangan' => 1
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Selasa",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 1
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '15:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 1
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Rabu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 1
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Kamis",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 1
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Jumat",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 1
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Sabtu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 1
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Minggu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 1
        ]);

        DB::table('lapangan_olahraga')->insert([
            'nama_lapangan' => "Lapangan Basket Mario 2",
            'fk_id_kategori' => 1,
            'tipe_lapangan' => "Outdoor",
            'lokasi_lapangan' => "jln. kupang jaya no.35, Surabaya",
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
            'nama_file_lapangan' => "lapangan_basket2.jpg",
            'fk_id_lapangan' => 2,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '08:00',
            'jam_tutup' => '12:00',
            'fk_id_lapangan' => 2
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Selasa",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 2
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '15:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 2
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Rabu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 2
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Kamis",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 2
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Jumat",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 2
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Sabtu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 2
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Minggu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 2
        ]);

        DB::table('lapangan_olahraga')->insert([
            'nama_lapangan' => "Lapangan Basket YukSport",
            'fk_id_kategori' => 1,
            'tipe_lapangan' => "Outdoor",
            'lokasi_lapangan' => "jln. mayjend sungkono no.103, Surabaya",
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
            'nama_file_lapangan' => "lapangan_basket3.jpg",
            'fk_id_lapangan' => 3,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '08:00',
            'jam_tutup' => '12:00',
            'fk_id_lapangan' => 3
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Selasa",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 3
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '15:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 3
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Rabu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 3
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Kamis",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 3
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Jumat",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 3
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Sabtu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 3
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Minggu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 3
        ]);

        DB::table('lapangan_olahraga')->insert([
            'nama_lapangan' => "Lapangan Futsal City Arena",
            'fk_id_kategori' => 2,
            'tipe_lapangan' => "Outdoor",
            'lokasi_lapangan' => "jln. darmo indah no.48, Surabaya",
            'kota_lapangan' => "Surabaya",
            'deskripsi_lapangan' => "Fitur Lapangan:

            - Lantai bertekstur khusus untuk cengkeraman sepatu yang optimal.
            - Ring basket yang memenuhi standar kompetisi.
            - Penerangan LED terang untuk permainan malam hari.
            - Area parkir yang luas dan mudah diakses.
            - Kursi penonton bagi yang ingin mendukung timnya.
            - Fasilitas toilet dan kamar ganti bersih.",
            'luas_lapangan' => "28x15",
            'harga_sewa_lapangan' => 200000,
            'status_lapangan' => "Aktif",
            'pemilik_lapangan' => 1,
            'created_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('files_lapangan')->insert([
            'nama_file_lapangan' => "lapangan_futsal1.jpg",
            'fk_id_lapangan' => 4,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '08:00',
            'jam_tutup' => '12:00',
            'fk_id_lapangan' => 4
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Selasa",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 4
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '15:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 4
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Rabu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 4
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Kamis",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 4
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Jumat",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 4
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Sabtu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 4
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Minggu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 4
        ]);

        DB::table('lapangan_olahraga')->insert([
            'nama_lapangan' => "Lapangan Goal Zone Futsal",
            'fk_id_kategori' => 2,
            'tipe_lapangan' => "Outdoor",
            'lokasi_lapangan' => "jln. citraland no.95, Surabaya",
            'kota_lapangan' => "Surabaya",
            'deskripsi_lapangan' => "Fitur Lapangan:

            - Lantai bertekstur khusus untuk cengkeraman sepatu yang optimal.
            - Ring basket yang memenuhi standar kompetisi.
            - Penerangan LED terang untuk permainan malam hari.
            - Area parkir yang luas dan mudah diakses.
            - Kursi penonton bagi yang ingin mendukung timnya.
            - Fasilitas toilet dan kamar ganti bersih.",
            'luas_lapangan' => "28x15",
            'harga_sewa_lapangan' => 200000,
            'status_lapangan' => "Aktif",
            'pemilik_lapangan' => 1,
            'created_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('files_lapangan')->insert([
            'nama_file_lapangan' => "lapangan_futsal2.jpg",
            'fk_id_lapangan' => 5,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '08:00',
            'jam_tutup' => '12:00',
            'fk_id_lapangan' => 5
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Selasa",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 5
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '15:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 5
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Rabu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 5
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Kamis",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 5
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Jumat",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 5
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Sabtu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 5
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Minggu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 5
        ]);

        DB::table('lapangan_olahraga')->insert([
            'nama_lapangan' => "Lapangan Badminton Binas",
            'fk_id_kategori' => 5,
            'tipe_lapangan' => "Indoor",
            'lokasi_lapangan' => "jln. citraland no.95, Surabaya",
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
            'nama_file_lapangan' => "lapangan_bultang1.jpg",
            'fk_id_lapangan' => 6,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '08:00',
            'jam_tutup' => '12:00',
            'fk_id_lapangan' => 6
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Selasa",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 6
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '15:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 6
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Rabu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 6
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Kamis",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 6
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Jumat",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 6
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Sabtu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 6
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Minggu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 6
        ]);

        DB::table('lapangan_olahraga')->insert([
            'nama_lapangan' => "Lapangan Futsalindo Center",
            'fk_id_kategori' => 2,
            'tipe_lapangan' => "Outdoor",
            'lokasi_lapangan' => "jln. bengawan no.19, Surabaya",
            'kota_lapangan' => "Surabaya",
            'deskripsi_lapangan' => "Fitur Lapangan:

            - Lantai bertekstur khusus untuk cengkeraman sepatu yang optimal.
            - Ring basket yang memenuhi standar kompetisi.
            - Penerangan LED terang untuk permainan malam hari.
            - Area parkir yang luas dan mudah diakses.
            - Kursi penonton bagi yang ingin mendukung timnya.
            - Fasilitas toilet dan kamar ganti bersih.",
            'luas_lapangan' => "28x15",
            'harga_sewa_lapangan' => 200000,
            'status_lapangan' => "Aktif",
            'pemilik_lapangan' => 2,
            'created_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('files_lapangan')->insert([
            'nama_file_lapangan' => "lapangan_futsal3.jpg",
            'fk_id_lapangan' => 7,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '08:00',
            'jam_tutup' => '12:00',
            'fk_id_lapangan' => 7
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Selasa",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 7
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '15:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 7
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Rabu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 7
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Kamis",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 7
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Jumat",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 7
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Sabtu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 7
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Minggu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 7
        ]);

        DB::table('lapangan_olahraga')->insert([
            'nama_lapangan' => "Lapangan Basket Nusantara Court",
            'fk_id_kategori' => 1,
            'tipe_lapangan' => "Indoor",
            'lokasi_lapangan' => "jln. kartini no.27, Surabaya",
            'kota_lapangan' => "Surabaya",
            'deskripsi_lapangan' => "Fitur Lapangan:

            - Lantai bertekstur khusus untuk cengkeraman sepatu yang optimal.
            - Ring basket yang memenuhi standar kompetisi.
            - Penerangan LED terang untuk permainan malam hari.
            - Area parkir yang luas dan mudah diakses.
            - Kursi penonton bagi yang ingin mendukung timnya.
            - Fasilitas toilet dan kamar ganti bersih.",
            'luas_lapangan' => "28x15",
            'harga_sewa_lapangan' => 200000,
            'status_lapangan' => "Aktif",
            'pemilik_lapangan' => 2,
            'created_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('files_lapangan')->insert([
            'nama_file_lapangan' => "lapangan_basket4.jpg",
            'fk_id_lapangan' => 8,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '08:00',
            'jam_tutup' => '12:00',
            'fk_id_lapangan' => 8
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Selasa",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 8
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '15:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 8
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Rabu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 8
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Kamis",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 8
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Jumat",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 8
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Sabtu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 8
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Minggu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 8
        ]);

        DB::table('lapangan_olahraga')->insert([
            'nama_lapangan' => "Lapangan LigaPlay Futsal",
            'fk_id_kategori' => 1,
            'tipe_lapangan' => "Indoor",
            'lokasi_lapangan' => "jln. mana aja deh no.27, Surabaya",
            'kota_lapangan' => "Surabaya",
            'deskripsi_lapangan' => "Fitur Lapangan:

            - Lantai bertekstur khusus untuk cengkeraman sepatu yang optimal.
            - Ring basket yang memenuhi standar kompetisi.
            - Penerangan LED terang untuk permainan malam hari.
            - Area parkir yang luas dan mudah diakses.
            - Kursi penonton bagi yang ingin mendukung timnya.
            - Fasilitas toilet dan kamar ganti bersih.",
            'luas_lapangan' => "28x15",
            'harga_sewa_lapangan' => 200000,
            'status_lapangan' => "Aktif",
            'pemilik_lapangan' => 2,
            'created_at' => date("Y-m-d H:i:s")
        ]);

        DB::table('files_lapangan')->insert([
            'nama_file_lapangan' => "lapangan_futsal4.jpg",
            'fk_id_lapangan' => 9,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '08:00',
            'jam_tutup' => '12:00',
            'fk_id_lapangan' => 9
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Selasa",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 9
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Senin",
            'jam_buka' => '15:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 9
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Rabu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 9
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Kamis",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 9
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Jumat",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 9
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Sabtu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 9
        ]);

        DB::table('slot_waktu')->insert([
            'hari' => "Minggu",
            'jam_buka' => '08:00',
            'jam_tutup' => '23:00',
            'fk_id_lapangan' => 9
        ]);

        DB::table('alat_olahraga')->insert([
            "nama_alat" => "Bola Futsal Specs",
            'fk_id_kategori' => 2,
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
            'status_alat' => "Non Aktif",
            'fk_id_pemilik' => null,
            'fk_id_tempat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_futsal1.jpg",
            'fk_id_alat' => 14,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'nama_file_alat' => "bola_futsal2.jpg",
            'fk_id_alat' => 14,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //--------------------------------------------------------------------
        $tanggal = date("Y-m-d");

        //minggu depan
        $updated_at2 = new DateTime($tanggal);
        $updated_at2->add(new DateInterval('P7D'));
        $tanggal2 = $updated_at2->format('Y-m-d');

        //minggu lalu
        $updated_at3 = new DateTime($tanggal);
        $updated_at3->sub(new DateInterval('P7D'));
        $tanggal3 = $updated_at3->format('Y-m-d');

        //bulan lalu
        $updated_at4 = new DateTime($tanggal);
        $updated_at4->sub(new DateInterval('P30D'));
        $tanggal4 = $updated_at4->format('Y-m-d');

        $format_tgl = date("Ymd");

        $waktu = date("H:i:s");

        //Request permintaan
        DB::table('request_permintaan')->insert([
            'req_harga_sewa' => 50000,
            'req_lapangan' => 1,
            'req_tanggal_mulai' => $tanggal,
            'req_tanggal_selesai' => $tanggal2,
            'req_id_alat' => 1,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 1,
            'tanggal_minta' => date("Y-m-d H:i:s"),
            'status_permintaan' => "Disewakan",
            'kode_mulai' => "REQMM".$format_tgl."1",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_permintaan')->insert([
            'req_harga_sewa' => 50000,
            'req_lapangan' => 1,
            'req_tanggal_mulai' => $tanggal,
            'req_tanggal_selesai' => $tanggal2,
            'req_id_alat' => 2,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 1,
            'tanggal_minta' => date("Y-m-d H:i:s"),
            'status_permintaan' => "Menunggu",
            'kode_mulai' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //Negosiasi permintaan
        DB::table('negosiasi')->insert([
            'isi_negosiasi' => "halo kak! untuk harga sewa tidak bisa dipertimbangkan lagi?",
            'waktu_negosiasi' => date("Y-m-d H:i:s"),
            'fk_id_permintaan' => 2,
            'fk_id_penawaran' => null,
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_permintaan')->insert([
            'req_harga_sewa' => 20000,
            'req_lapangan' => 1,
            'req_tanggal_mulai' => $tanggal,
            'req_tanggal_selesai' => $tanggal2,
            'req_id_alat' => 3,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 1,
            'tanggal_minta' => date("Y-m-d H:i:s"),
            'status_permintaan' => "Diterima",
            'kode_mulai' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_permintaan')->insert([
            'req_harga_sewa' => 30000,
            'req_lapangan' => 2,
            'req_tanggal_mulai' => $tanggal3,
            'req_tanggal_selesai' => $tanggal,
            'req_id_alat' => 4,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 1,
            'tanggal_minta' => date("Y-m-d H:i:s"),
            'status_permintaan' => "Selesai",
            'kode_mulai' => "REQMM".$format_tgl."3",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_permintaan')->insert([
            'req_harga_sewa' => 40000,
            'req_lapangan' => 3,
            'req_tanggal_mulai' => $tanggal,
            'req_tanggal_selesai' => $tanggal2,
            'req_id_alat' => 5,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 1,
            'tanggal_minta' => date("Y-m-d H:i:s"),
            'status_permintaan' => "Dikomplain",
            'kode_mulai' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //Komplain request permintaan
        DB::table('komplain_request')->insert([
            'jenis_komplain' => "Alat tidak sesuai",
            'keterangan_komplain' => "alat olahraga yang dikirim dan yang dijelaskan di detail beda jauh",
            'fk_id_permintaan' => 5,
            'fk_id_penawaran' => null,
            'waktu_komplain' => date("Y-m-d H:i:s"),
            'status_komplain' => "Menunggu",
            'penanganan_komplain' => null,
            'alasan_komplain' => null,
            'fk_id_pemilik' => null,
            'fk_id_tempat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_komplain_req')->insert([
            'nama_file_komplain' => "raket_rusak.jpg",
            'fk_id_komplain_req' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_permintaan')->insert([
            'req_harga_sewa' => 50000,
            'req_lapangan' => 2,
            'req_tanggal_mulai' => "2024-01-15",
            'req_tanggal_selesai' => "2024-10-15",
            'req_id_alat' => 12,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 2,
            'tanggal_minta' => "2024-01-10 12:30:00",
            'status_permintaan' => "Disewakan",
            'kode_mulai' => "REQMM".$format_tgl."6",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_permintaan')->insert([
            'req_harga_sewa' => 20000,
            'req_lapangan' => 4,
            'req_tanggal_mulai' => "2024-01-15",
            'req_tanggal_selesai' => "2024-10-15",
            'req_id_alat' => 11,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 2,
            'tanggal_minta' => "2024-01-10 12:30:00",
            'status_permintaan' => "Disewakan",
            'kode_mulai' => "REQMM".$format_tgl."6",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_permintaan')->insert([
            'req_harga_sewa' => 30000,
            'req_lapangan' => 3,
            'req_tanggal_mulai' => "2024-01-15",
            'req_tanggal_selesai' => "2024-10-15",
            'req_id_alat' => 13,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 2,
            'tanggal_minta' => "2024-01-10 12:30:00",
            'status_permintaan' => "Disewakan",
            'kode_mulai' => "REQMM".$format_tgl."6",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //request penawaran
        DB::table('request_penawaran')->insert([
            'req_harga_sewa' => null,
            'req_lapangan' => 1,
            'req_tanggal_mulai' => null,
            'req_tanggal_selesai' => null,
            'req_id_alat' => 6,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 1,
            'tanggal_tawar' => date("Y-m-d H:i:s"),
            'status_penawaran' => "Menunggu",
            'status_tempat' => null,
            'status_pemilik' => null,
            'kode_mulai' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //Negosiasi penawaran
        DB::table('negosiasi')->insert([
            'isi_negosiasi' => "hai!",
            'waktu_negosiasi' => date("Y-m-d H:i:s"),
            'fk_id_permintaan' => null,
            'fk_id_penawaran' => 1,
            'fk_id_pemilik' => 1,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_penawaran')->insert([
            'req_harga_sewa' => null,
            'req_lapangan' => 3,
            'req_tanggal_mulai' => null,
            'req_tanggal_selesai' => null,
            'req_id_alat' => 7,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 1,
            'tanggal_tawar' => date("Y-m-d H:i:s"),
            'status_penawaran' => "Menunggu",
            'status_tempat' => null,
            'status_pemilik' => null,
            'kode_mulai' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //Negosiasi penawaran
        DB::table('negosiasi')->insert([
            'isi_negosiasi' => "hai! saya mau menawarkan alat olahraga, monggo mungkin tertarik untuk menyewakannya",
            'waktu_negosiasi' => date("Y-m-d H:i:s"),
            'fk_id_permintaan' => null,
            'fk_id_penawaran' => 2,
            'fk_id_pemilik' => 2,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_penawaran')->insert([
            'req_harga_sewa' => 50000,
            'req_lapangan' => 3,
            'req_tanggal_mulai' => $tanggal,
            'req_tanggal_selesai' => $tanggal2,
            'req_id_alat' => 8,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 2,
            'tanggal_tawar' => date("Y-m-d H:i:s"),
            'status_penawaran' => "Menunggu",
            'status_tempat' => "Setuju",
            'status_pemilik' => null,
            'kode_mulai' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_penawaran')->insert([
            'req_harga_sewa' => 20000,
            'req_lapangan' => 4,
            'req_tanggal_mulai' => $tanggal,
            'req_tanggal_selesai' => $tanggal2,
            'req_id_alat' => 9,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 2,
            'tanggal_tawar' => date("Y-m-d H:i:s"),
            'status_penawaran' => "Diterima",
            'status_tempat' => "Setuju",
            'status_pemilik' => "Setuju",
            'kode_mulai' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('request_penawaran')->insert([
            'req_harga_sewa' => 20000,
            'req_lapangan' => 4,
            'req_tanggal_mulai' => $tanggal,
            'req_tanggal_selesai' => $tanggal2,
            'req_id_alat' => 10,
            'fk_id_tempat' => 1,
            'fk_id_pemilik' => 2,
            'tanggal_tawar' => date("Y-m-d H:i:s"),
            'status_penawaran' => "Dikomplain",
            'status_tempat' => "Setuju",
            'status_pemilik' => "Setuju",
            'kode_mulai' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //Komplain request penawaran
        DB::table('komplain_request')->insert([
            'jenis_komplain' => "Lapangan tidak sesuai",
            'keterangan_komplain' => "Lapangan olahraga sebenarnya sudah tidak layak digunakan",
            'fk_id_permintaan' => null,
            'fk_id_penawaran' => 5,
            'waktu_komplain' => date("Y-m-d H:i:s"),
            'status_komplain' => "Menunggu",
            'penanganan_komplain' => null,
            'alasan_komplain' => null,
            'fk_id_pemilik' => 2,
            'fk_id_tempat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //Sewa sendiri
        DB::table('sewa_sendiri')->insert([
            'req_lapangan' => 1,
            'req_id_alat' => 14,
            'fk_id_tempat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        //Transaksi
        DB::table('htrans')->insert([
            'kode_trans' => "H".date("dmy")."0001",
            'fk_id_lapangan' => 1,
            'subtotal_lapangan' => 200000,
            'subtotal_alat' => 150000,
            'tanggal_trans' => $tanggal4,
            'tanggal_sewa'=> $tanggal4,
            'jam_sewa' => "06:00",
            'durasi_sewa' => 2,
            'total_trans' => 350000,
            'fk_id_user' => 1,
            'fk_id_tempat' => 1,
            'pendapatan_website_lapangan' => 18000,
            'status_trans' => "Selesai",
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
            'fk_id_alat' => 5,
            'harga_sewa_alat' => 25000,
            'subtotal_alat' => 50000,
            'total_komisi_pemilik' => null,
            'total_komisi_tempat' => 50000,
            'fk_id_pemilik' => null,
            'fk_id_tempat' => 1,
            'pendapatan_website_alat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('htrans')->insert([
            'kode_trans' => "H".date("dmy")."0002",
            'fk_id_lapangan' => 1,
            'subtotal_lapangan' => 200000,
            'subtotal_alat' => 150000,
            'tanggal_trans' => date("Y-m-d H:i:s"),
            'tanggal_sewa'=> $tanggal,
            'jam_sewa' => "06:00",
            'durasi_sewa' => 2,
            'total_trans' => 350000,
            'fk_id_user' => 1,
            'fk_id_tempat' => 1,
            'pendapatan_website_lapangan' => 18000,
            'status_trans' => "Selesai",
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

        DB::table('dtrans')->insert([
            'fk_id_htrans' => 2,
            'fk_id_alat' => 5,
            'harga_sewa_alat' => 25000,
            'subtotal_alat' => 50000,
            'total_komisi_pemilik' => null,
            'total_komisi_tempat' => 50000,
            'fk_id_pemilik' => null,
            'fk_id_tempat' => 1,
            'pendapatan_website_alat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('htrans')->insert([
            'kode_trans' => "H".date("dmy")."0003",
            'fk_id_lapangan' => 2,
            'subtotal_lapangan' => 200000,
            'subtotal_alat' => 150000,
            'tanggal_trans' => date("Y-m-d H:i:s"),
            'tanggal_sewa'=> $tanggal,
            'jam_sewa' => $waktu,
            'durasi_sewa' => 2,
            'total_trans' => 350000,
            'fk_id_user' => 1,
            'fk_id_tempat' => 1,
            'pendapatan_website_lapangan' => 18000,
            'status_trans' => "Diterima",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('dtrans')->insert([
            'fk_id_htrans' => 3,
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
            'fk_id_htrans' => 3,
            'fk_id_alat' => 5,
            'harga_sewa_alat' => 25000,
            'subtotal_alat' => 50000,
            'total_komisi_pemilik' => null,
            'total_komisi_tempat' => 50000,
            'fk_id_pemilik' => null,
            'fk_id_tempat' => 1,
            'pendapatan_website_alat' => null,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        // DB::table('komplain_trans')->insert([
        //     'jenis_komplain' => "Alat tidak sesuai",
        //     'keterangan_komplain' => "alat olahraga bedaa",
        //     'fk_id_htrans' => 3,
        //     'waktu_komplain' => date("Y-m-d H:i:s"),
        //     'status_komplain' => "Menunggu",
        //     'penanganan_komplain' => null,
        //     'alasan_komplain' => null,
        //     'fk_id_user' => 1,
        //     'created_at' => date("Y-m-d H:i:s"),
        // ]);

        // DB::table('files_komplain_trans')->insert([
        //     'nama_file_komplain' => "bola_jelek.jpg",
        //     'fk_id_komplain_trans' => 1,
        //     'created_at' => date("Y-m-d H:i:s"),
        // ]);

        DB::table('htrans')->insert([
            'kode_trans' => "H".date("dmy")."0004",
            'fk_id_lapangan' => 1,
            'subtotal_lapangan' => 200000,
            'subtotal_alat' => 150000,
            'tanggal_trans' => date("Y-m-d H:i:s"),
            'tanggal_sewa'=> $tanggal,
            'jam_sewa' => $waktu,
            'durasi_sewa' => 2,
            'total_trans' => 350000,
            'fk_id_user' => 1,
            'fk_id_tempat' => 1,
            'pendapatan_website_lapangan' => 18000,
            'status_trans' => "Berlangsung",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('dtrans')->insert([
            'fk_id_htrans' => 4,
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

        // DB::table('rating_alat')->insert([
        //     'rating' => 4,
        //     'review' => "bola basketnya lumayan bagus, cuman agak kempes",
        //     'hide' => "Tidak",
        //     'fk_id_user' => 1,
        //     'fk_id_alat' => 1,
        //     'fk_id_dtrans' => 3,
        //     'created_at' => date("Y-m-d H:i:s"),
        // ]);

        // DB::table('rating_lapangan')->insert([
        //     'rating' => 5,
        //     'review' => "lapangannya bagus, bersih, nyaman dibuat main",
        //     'hide' => "Tidak",
        //     'fk_id_user' => 1,
        //     'fk_id_lapangan' => 1,
        //     'fk_id_htrans' => 2,
        //     'created_at' => date("Y-m-d H:i:s"),
        // ]);

        // DB::table('notifikasi')->insert([
        //     'keterangan_notifikasi' => "Permintaan alat olahraga Bola Basket Molten",
        //     'waktu_notifikasi' => date("Y-m-d H:i:s"),
        //     'link_notifikasi' => "/pemilik/permintaan/detailPermintaanNego/1",
        //     'fk_id_user' => null,
        //     'fk_id_pemilik' => 1,
        //     'fk_id_tempat' => null,
        //     'admin' => null,
        //     'status_notifikasi' => "Tidak",
        //     'created_at' => date("Y-m-d H:i:s"),
        // ]);
    }
}
