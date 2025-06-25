<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        $kategori = [
            ['kategori_kode' => 'APP', 'kategori_nama' => 'Appetizer'],
            ['kategori_kode' => 'MKN', 'kategori_nama' => 'Makanan Utama'],
            ['kategori_kode' => 'SNK', 'kategori_nama' => 'Cemilan / Snack'],
            ['kategori_kode' => 'DSR', 'kategori_nama' => 'Makanan Penutup'],
            ['kategori_kode' => 'PDL', 'kategori_nama' => 'Makanan Pedas'],
            ['kategori_kode' => 'MNS', 'kategori_nama' => 'Makanan Manis'],
            ['kategori_kode' => 'TRD', 'kategori_nama' => 'Masakan Tradisional'],
            ['kategori_kode' => 'ASN', 'kategori_nama' => 'Masakan Asia'],
            ['kategori_kode' => 'WST', 'kategori_nama' => 'Masakan Western'],
            ['kategori_kode' => 'MNM', 'kategori_nama' => 'Minuman'],
            ['kategori_kode' => 'HTB', 'kategori_nama' => 'Minuman Hangat'],
            ['kategori_kode' => 'CLD', 'kategori_nama' => 'Minuman Dingin'],
            ['kategori_kode' => 'SPC', 'kategori_nama' => 'Menu Spesial'],
        ];

        DB::table('m_kategori')->insert($kategori);
    }
}
