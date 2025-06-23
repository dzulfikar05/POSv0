<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('t_stok', function (Blueprint $table) {
            $table->bigIncrements('stok_id'); // Primary Key
            $table->string('item'); // Foreign Key ke m_barang
            $table->unsignedBigInteger('user_id'); // Foreign Key ke m_user
            $table->unsignedBigInteger('supplier_id'); // Foreign Key ke m_user
            $table->dateTime('stok_tanggal');
            $table->integer('stok_jumlah');
            $table->bigInteger('harga_total');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('user_id')->references('user_id')->on('m_user')->onDelete('cascade');
            $table->foreign('supplier_id')->references('supplier_id')->on('m_supplier')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_stok');
    }
};
