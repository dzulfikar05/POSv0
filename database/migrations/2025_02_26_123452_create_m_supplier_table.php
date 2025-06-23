<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('m_supplier', function (Blueprint $table) {
            $table->bigIncrements('supplier_id'); // Primary Key
            $table->string('supplier_kode', 10)->unique();
            $table->string('supplier_nama', 100);
            $table->string('supplier_wa', 20)->nullable();
            $table->text('supplier_alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_supplier');
    }
};
