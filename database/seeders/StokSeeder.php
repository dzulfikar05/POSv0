<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StokSeeder extends Seeder
{
    public function run()
    {
        $stok = [];
        for ($i = 1; $i <= 10; $i++) {
            $stok[] = [
                'barang_id' => $i,
                'user_id' => 1, // Sesuaikan dengan user yang ada
                'stok_tanggal' => Carbon::now(),
                'stok_jumlah' => rand(10, 100)
            ];
        }

        DB::table('t_stok')->insert($stok);
    }
}

