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
        Schema::create('hutang_dan_stok', function (Blueprint $table) {
            $table->id('id_hutang_stok');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('total', 15, 2);
            $table->decimal('dp', 15, 2);
            $table->decimal('nominal_terbayar', 15, 2);
            $table->date('tenggat_waktu');  // Kolom tenggat_waktu ditambahkan di sini
            $table->unsignedBigInteger('id_bukubesar');
            $table->unsignedBigInteger('id_barang');
            $table->integer('stok');
            $table->timestamps();
            $table->softDeletes();



            $table->foreign('id_bukubesar')->references('id_bukubesar')->on('bukubesar')->onDelete('cascade');
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
        Schema::dropIfExists('hutang_dan_stok');
    }
};
