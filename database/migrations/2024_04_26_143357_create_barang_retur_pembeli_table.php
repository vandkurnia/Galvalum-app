<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retur_pesanan_pembeli', function (Blueprint $table) {
            $table->id('id_retur_pesanan');
            $table->unsignedBigInteger('id_pesanan_pembeli');
            $table->decimal('harga', 20, 2);
            $table->decimal('total', 20, 2);
            $table->decimal('qty', 20, 2);
            $table->timestamps();
            $table->foreign('id_pesanan_pembeli')->references('id_pesanan')->on('pesanan_pembelis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barang_retur_pembeli');
    }
};