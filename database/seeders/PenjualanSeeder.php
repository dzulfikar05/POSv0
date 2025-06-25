<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PenjualanSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1;
        $customerId = 2;
        $barangList = DB::table('m_barang')->get();

        for ($bulan = 1; $bulan <= 5; $bulan++) {
            for ($i = 0; $i < 30; $i++) {
                $penjualanTanggal = Carbon::create(2025, $bulan, rand(1, 28), rand(8, 17), rand(0, 59), rand(0, 59));
                $penjualanKode = 'PJ' . $penjualanTanggal->format('YmdHis') . rand(10, 99);

                $penjualanId = DB::table('t_penjualan')->insertGetId([
                    'user_id' => $userId,
                    'customer_id' => $customerId,
                    'penjualan_kode' => $penjualanKode,
                    'penjualan_tanggal' => $penjualanTanggal,
                    'status' => 'completed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalPenjualan = 0;

                while ($totalPenjualan < rand(40000, 70000)) {
                    $barang = $barangList->random();
                    $jumlah = rand(1, 3);
                    $harga = $barang->harga;
                    $subTotal = $harga * $jumlah;

                    DB::table('t_penjualan_detail')->insert([
                        'penjualan_id' => $penjualanId,
                        'barang_id' => $barang->barang_id,
                        'harga' => $harga,
                        'jumlah' => $jumlah,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $totalPenjualan += $subTotal;

                    if ($totalPenjualan > 100000) break; // batasi 1 transaksi < 100rb
                }
            }
        }
    }
}
