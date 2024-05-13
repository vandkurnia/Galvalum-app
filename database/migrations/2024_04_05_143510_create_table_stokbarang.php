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
        Schema::create('stok_barang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_barang');
            $table->foreign('id_barang')->references('id_barang')->on('barangs')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('id_bukubesar')->nullable(); // Delete Soon Nullablenya
            $table->foreign('id_bukubesar')->references('id_bukubesar')->on('bukubesar')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('stok_masuk')->default(0);
            $table->integer('stok_keluar')->default(0);
            $table->timestamps();
            $table->softDeletes(); // Add this line for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stok_barang');
    }
};