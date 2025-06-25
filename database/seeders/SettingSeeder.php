<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'label' => 'Nama Sistem',
                'value' => 'POSv0'
            ],
            [
                'label' => 'Whatsapp',
                'value' => '6281234567890'
            ],
            [
                'label' => 'Alamat Toko',
                'value' => 'Jl. Contoh No. 123, Malang'
            ],
        ];

        DB::table('setting')->insert($data);
    }
}
