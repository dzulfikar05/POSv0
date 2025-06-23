<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $penjualan = [];
        for ($i = 1; $i <= 10; $i++) {
            $penjualan[] = [
                'user_id' => 1, // Sesuaikan dengan user yang ada
                'pembeli' => 'Pembeli ' . $i,
                'penjualan_kode' => 'TRX' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'penjualan_tanggal' => Carbon::now()
            ];
        }

        DB::table('t_penjualan')->insert($penjualan);
    }
}
