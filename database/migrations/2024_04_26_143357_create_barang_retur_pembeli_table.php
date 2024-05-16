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
            $table->unsignedBigInteger('id_retur_pembeli');
            $table->unsignedBigInteger('id_pesanan_pembeli');
            $table->decimal('harga', 20, 2);
            $table->decimal('total', 20, 2);
            $table->decimal('qty', 20, 2);
            $table->decimal('qty_sebelum_perubahan', 20, 2)->nullable();
            $table->enum('type_retur_pesanan', ['retur_tambah_barang', 'retur_tambah_stok', 'retur_murni_rusak', 'retur_murni_tidak_rusak']);
            $table->unsignedBigInteger('id_stok_barang')->nullable();
            $table->foreign('id_stok_barang')->references('id')->on('stok_barang')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_retur_pembeli')->references('id_retur_pembeli')->on('retur_pembeli')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_pesanan_pembeli')->references('id_pesanan')->on('pesanan_pembelis')->onUpdate('cascade')->onDelete('cascade');
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
