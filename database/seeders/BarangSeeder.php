<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BarangSeeder extends Seeder
{
    public function run()
    {
        $items = [
            // Appetizer (kategori_id = 1)
            ['kategori_id' => 1, 'barang_kode' => 'B001', 'barang_nama' => 'Salad Buah', 'harga' => 15000],
            ['kategori_id' => 1, 'barang_kode' => 'B002', 'barang_nama' => 'Soup Jagung', 'harga' => 18000],
            // Makanan Utama (kategori_id = 2)
            ['kategori_id' => 2, 'barang_kode' => 'B003', 'barang_nama' => 'Nasi Goreng Spesial', 'harga' => 25000],
            ['kategori_id' => 2, 'barang_kode' => 'B004', 'barang_nama' => 'Ayam Bakar Madu', 'harga' => 28000],
            ['kategori_id' => 2, 'barang_kode' => 'B005', 'barang_nama' => 'Ikan Bumbu Kuning', 'harga' => 30000],
            // Cemilan / Snack (kategori_id = 3)
            ['kategori_id' => 3, 'barang_kode' => 'B006', 'barang_nama' => 'Kentang Goreng', 'harga' => 12000],
            ['kategori_id' => 3, 'barang_kode' => 'B007', 'barang_nama' => 'Pisang Goreng Coklat', 'harga' => 13000],
            // Dessert (kategori_id = 4)
            ['kategori_id' => 4, 'barang_kode' => 'B008', 'barang_nama' => 'Puding Coklat', 'harga' => 10000],
            ['kategori_id' => 4, 'barang_kode' => 'B009', 'barang_nama' => 'Es Krim Vanilla', 'harga' => 12000],
            // Pedas (kategori_id = 5)
            ['kategori_id' => 5, 'barang_kode' => 'B010', 'barang_nama' => 'Mie Goreng Sambal Matah', 'harga' => 22000],
            ['kategori_id' => 5, 'barang_kode' => 'B011', 'barang_nama' => 'Ayam Geprek', 'harga' => 24000],
            // Manis (kategori_id = 6)
            ['kategori_id' => 6, 'barang_kode' => 'B012', 'barang_nama' => 'Martabak Coklat Keju', 'harga' => 18000],
            ['kategori_id' => 6, 'barang_kode' => 'B013', 'barang_nama' => 'Roti Bakar Susu', 'harga' => 15000],
            // Tradisional (kategori_id = 7)
            ['kategori_id' => 7, 'barang_kode' => 'B014', 'barang_nama' => 'Soto Ayam Lamongan', 'harga' => 25000],
            ['kategori_id' => 7, 'barang_kode' => 'B015', 'barang_nama' => 'Pecel Lele', 'harga' => 23000],
            // Asia (kategori_id = 8)
            ['kategori_id' => 8, 'barang_kode' => 'B016', 'barang_nama' => 'Ramen Jepang', 'harga' => 35000],
            ['kategori_id' => 8, 'barang_kode' => 'B017', 'barang_nama' => 'Bibimbap Korea', 'harga' => 37000],
            // Western (kategori_id = 9)
            ['kategori_id' => 9, 'barang_kode' => 'B018', 'barang_nama' => 'Chicken Steak', 'harga' => 40000],
            ['kategori_id' => 9, 'barang_kode' => 'B019', 'barang_nama' => 'Spaghetti Carbonara', 'harga' => 38000],
            // Minuman Umum (kategori_id = 10)
            ['kategori_id' => 10, 'barang_kode' => 'B020', 'barang_nama' => 'Es Teh Manis', 'harga' => 5000],
            ['kategori_id' => 10, 'barang_kode' => 'B021', 'barang_nama' => 'Jus Alpukat', 'harga' => 12000],
            // Minuman Hangat (kategori_id = 11)
            ['kategori_id' => 11, 'barang_kode' => 'B022', 'barang_nama' => 'Teh Hangat', 'harga' => 4000],
            ['kategori_id' => 11, 'barang_kode' => 'B023', 'barang_nama' => 'Kopi Tubruk', 'harga' => 7000],
            // Minuman Dingin (kategori_id = 12)
            ['kategori_id' => 12, 'barang_kode' => 'B024', 'barang_nama' => 'Es Kopi Susu', 'harga' => 15000],
            ['kategori_id' => 12, 'barang_kode' => 'B025', 'barang_nama' => 'Es Jeruk', 'harga' => 8000],
            // Menu Spesial (kategori_id = 13)
            ['kategori_id' => 13, 'barang_kode' => 'B026', 'barang_nama' => 'Platter Komplit', 'harga' => 50000],
            ['kategori_id' => 13, 'barang_kode' => 'B027', 'barang_nama' => 'Chefs Special Ribs', 'harga' => 75000],
        ];

        $barang = collect($items)->map(function ($item) {
            $slug = Str::of($item['barang_nama'])->lower()->slug('_');
            $sourcePath = public_path("img-seeder/{$slug}.jpg");

            if (file_exists($sourcePath)) {
                $filename = time() . '_' . uniqid() . '.jpg';
                $destination = storage_path("app/public/uploads/product/{$filename}");

                // Salin file dari public/img-seeder ke storage/app/public/uploads/product
                copy($sourcePath, $destination);

                $item['image'] = $filename; // disimpan hanya nama file seperti hasil storeAs
            } else {
                $item['image'] = null; // Atau default.jpg jika kamu sediakan fallback
            }

            return $item;
        })->toArray();

        DB::table('m_barang')->insert($barang);
    }
}
