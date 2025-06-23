<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        $kategori = [
            ['kategori_kode' => 'ELEC', 'kategori_nama' => 'Elektronik'],
            ['kategori_kode' => 'FASH', 'kategori_nama' => 'Fashion'],
            ['kategori_kode' => 'HERB', 'kategori_nama' => 'Herbal'],
            ['kategori_kode' => 'TOYS', 'kategori_nama' => 'Mainan'],
            ['kategori_kode' => 'HOME', 'kategori_nama' => 'Perabotan Rumah']
        ];

        DB::table('m_kategori')->insert($kategori);
    }
}
