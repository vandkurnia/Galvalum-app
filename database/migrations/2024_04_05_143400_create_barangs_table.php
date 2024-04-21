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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id('id_barang');
            $table->string('hash_id_barang')->unique();
            $table->string('nama_barang');
            $table->decimal('harga_barang', 15, 2);
            $table->integer('stok');
            $table->string('ukuran');
            $table->unsignedBigInteger('id_pemasok');
            $table->unsignedBigInteger('id_tipe_barang');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_pemasok')->references('id_pemasok')->on('pemasok_barangs');
            $table->foreign('id_tipe_barang')->references('id_tipe_barang')->on('tipe_barangs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barangs');
    }
};