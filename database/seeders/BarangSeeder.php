<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run()
    {
        $barang = [
            ['kategori_id' => 1, 'barang_kode' => 'B001', 'barang_nama' => 'Laptop', 'harga_beli' => 5000000, 'harga_jual' => 6000000],
            ['kategori_id' => 1, 'barang_kode' => 'B002', 'barang_nama' => 'Smartphone', 'harga_beli' => 3000000, 'harga_jual' => 3500000],
            ['kategori_id' => 2, 'barang_kode' => 'B003', 'barang_nama' => 'Kaos Polos', 'harga_beli' => 50000, 'harga_jual' => 75000],
            ['kategori_id' => 2, 'barang_kode' => 'B004', 'barang_nama' => 'Sepatu Sneakers', 'harga_beli' => 400000, 'harga_jual' => 500000],
            ['kategori_id' => 3, 'barang_kode' => 'B005', 'barang_nama' => 'Madu', 'harga_beli' => 2500, 'harga_jual' => 3000],
            ['kategori_id' => 3, 'barang_kode' => 'B006', 'barang_nama' => 'Obat Mag', 'harga_beli' => 5000, 'harga_jual' => 6000],
            ['kategori_id' => 4, 'barang_kode' => 'B007', 'barang_nama' => 'Boneka Teddy Bear', 'harga_beli' => 75000, 'harga_jual' => 100000],
            ['kategori_id' => 4, 'barang_kode' => 'B008', 'barang_nama' => 'Lego Set', 'harga_beli' => 200000, 'harga_jual' => 250000],
            ['kategori_id' => 5, 'barang_kode' => 'B009', 'barang_nama' => 'Kursi Kayu', 'harga_beli' => 500000, 'harga_jual' => 650000],
            ['kategori_id' => 5, 'barang_kode' => 'B010', 'barang_nama' => 'Meja Belajar', 'harga_beli' => 700000, 'harga_jual' => 850000]
        ];

        DB::table('m_barang')->insert($barang);
    }
}

