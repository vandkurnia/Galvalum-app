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
        Schema::create('pesanan_pembelis', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->decimal('jumlah_pembelian', 12); // 24 digit total, 2 digit di belakang koma
            $table->decimal('harga', 24, 2); // 24 digit total, 2 digit di belakang koma
            $table->decimal('diskon', 24, 2); // 24 digit total, 2 digit di belakang koma            
            $table->unsignedBigInteger('id_nota');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_jenis_pelanggan')->nullable();
            $table->unsignedBigInteger('id_diskon')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_jenis_pelanggan')->references('id_jenis_pelanggan')->on('jenis_pelanggan')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_diskon')->references('id_diskon')->on('diskon')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_nota')->references('id_nota')->on('nota_pembelis')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('barangs')->onUpdate('cascade')->onDelete('cascade');
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
