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
            'updated_at' => date("Y-m-d H:i:s"),
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
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('pihak_tempat')->insert([
            'id_tempat'=> "1",
            'nama_tempat' => "Mario Sport",
            'nama_pemilik_tempat' => "Mario Wijaya",
            'email_tempat' => 'mario@gmail.com',
            'telepon_tempat' => "086547823741",
            'alamat_tempat' => "ngagel jaya, Surabaya",
            'ktp_tempat' => "64e5a2e7a68d8.jpg",
            'npwp_tempat' => "64e5a2e7a68d8.jpg",
            'password_tempat' => password_hash('1234567890', PASSWORD_BCRYPT),
            'saldo_tempat' => "XQ==",
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
        
        DB::table('kategori')->insert([
            'id_kategori' => "1",
            "nama_kategori" => "Basket",
        ]);
    }
}
