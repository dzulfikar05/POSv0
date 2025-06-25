<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'supplier_kode' => 'SUP001',
                'supplier_nama' => 'PT Sumber Pangan Sejahtera',
                'supplier_wa' => '6281234567890',
                'supplier_alamat' => 'Jl. Raya Bogor KM 26, Jakarta Timur',
            ],
            [
                'supplier_kode' => 'SUP002',
                'supplier_nama' => 'UD Sayur Segar',
                'supplier_wa' => '6281298765432',
                'supplier_alamat' => 'Pasar Induk Kramat Jati, Jakarta',
            ],
            [
                'supplier_kode' => 'SUP003',
                'supplier_nama' => 'CV Aneka Minuman',
                'supplier_wa' => '6285212345678',
                'supplier_alamat' => 'Jl. Sukajadi No. 12, Bandung',
            ],
        ];

        DB::table('m_supplier')->insert($data);
    }
}
