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
            'id_user'=> "1",
            'nama_user' => "Maria Yerossi",
            'email_user' => 'maria@gmail.com',
            'telepon_user' => "082374822343",
            'password_user' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_user' => "XQ==",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('pemilik_alat')->insert([
            'id_pemilik'=> "1",
            'nama_pemilik' => "Andika Pratama",
            'email_pemilik' => 'andika@gmail.com',
            'telepon_pemilik' => "086768686774",
            'ktp_pemilik' => "64db1c79dc9d5.jpg",
            'password_pemilik' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_pemilik' => "XQ==",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('pihak_tempat')->insert([
            'id_tempat'=> "1",
            'nama_tempat' => "Mario Sport",
            'nama_pemilik_tempat' => "Mario Wijaya",
            'email_tempat' => 'mario12@gmail.com',
            'telepon_tempat' => "086547823741",
            'alamat_tempat' => "ngagel jaya, Surabaya",
            'ktp_tempat' => "64e5a2e7a68d8.jpg",
            'npwp_tempat' => "64e5a2e7a68d8.jpg",
            'password_tempat' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_tempat' => "XQ==",
            'created_at' => date("Y-m-d H:i:s"),
        ]);
        
        DB::table('kategori')->insert([
            'id_kategori' => "1",
            "nama_kategori" => "Basket",
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('alat_olahraga')->insert([
            'id_alat' => "1",
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
            'stok_alat' => 15,
            'komisi_alat' => 20000,
            'ganti_rugi_alat' => 100000,
            'status_alat' => "Aktif",
            'pemilik_alat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'id_file_alat' => "1",
            'nama_file_alat' => "64eb19b1e779c.jpg",
            'fk_id_alat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('files_alat')->insert([
            'id_file_alat' => "2",
            'nama_file_alat' => "64eb19b1ea0df.jpg",
            'fk_id_alat' => 1,
            'created_at' => date("Y-m-d H:i:s"),
        ]);
    }
}
