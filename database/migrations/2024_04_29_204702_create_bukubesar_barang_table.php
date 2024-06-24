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
        Schema::create('bukubesar_barang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bukubesar');
            $table->unsignedBigInteger('id_barang');
            $table->timestamps();

            $table->foreign('id_bukubesar')->references('id_bukubesar')->on('bukubesar')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('bukubesar_barang');
    }
};
