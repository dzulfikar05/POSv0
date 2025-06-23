<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('t_penjualan', function (Blueprint $table) {
            $table->bigIncrements('penjualan_id'); // Primary Key
            $table->unsignedBigInteger('user_id'); // Foreign Key ke m_user
            $table->unsignedBigInteger('customer_id');
            $table->string('penjualan_kode', 20)->unique();
            $table->dateTime('penjualan_tanggal');
            $table->string('status', 20);
            $table->timestamps();

            $table->foreign('customer_id')->references('user_id')->on('m_user')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('m_user')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_penjualan_detail');
        Schema::dropIfExists('t_penjualan');
    }
};
