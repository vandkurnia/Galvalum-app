<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesanan_pembelis', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->integer('jumlah_pembelian');
            $table->unsignedBigInteger('id_nota');
            $table->unsignedBigInteger('id_barang');
            $table->timestamps();

            $table->foreign('id_nota')->references('id_nota')->on('nota_pembelis');
            $table->foreign('id_barang')->references('id_barang')->on('barangs');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pesanan_pembelis');
    }
};
