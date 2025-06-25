<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StokSeeder extends Seeder
{
    public function run(): void
    {
        $items = ['Bawang', 'Merica', 'Terasi', 'Cabe', 'Garam', 'Kunyit', 'Jahe', 'Lengkuas'];
        $userId = 1;

        $allStok = [];

        for ($bulan = 1; $bulan <= 6; $bulan++) {
            $stokPerBulan = [];
            $totalHargaBulan = 0;

            while ($totalHargaBulan < 240000 || count($stokPerBulan) < 3) {
                $item = $items[array_rand($items)];
                $jumlah = rand(1, 5);
                $hargaSatuan = rand(10000, 70000);
                $hargaTotal = $jumlah * $hargaSatuan;

                $stokPerBulan[] = [
                    'item' => $item,
                    'user_id' => $userId,
                    'supplier_id' => rand(1, 3),
                    'stok_tanggal' => Carbon::create(2025, $bulan, rand(1, 28)),
                    'stok_jumlah' => $jumlah,
                    'harga_total' => $hargaTotal,
                    'keterangan' => 'kiloan',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $totalHargaBulan += $hargaTotal;
            }

            $allStok = array_merge($allStok, $stokPerBulan);
        }

        DB::table('t_stok')->insert($allStok);
    }
}
