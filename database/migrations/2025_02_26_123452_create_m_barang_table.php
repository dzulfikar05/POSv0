<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('m_barang', function (Blueprint $table) {
            $table->bigIncrements('barang_id'); // Primary Key
            $table->unsignedBigInteger('kategori_id'); // Foreign Key ke m_kategori
            $table->string('barang_kode', 10)->unique();
            $table->string('barang_nama', 100);
            $table->bigInteger('harga');
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('kategori_id')->references('kategori_id')->on('m_kategori')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_barang');
    }
};
