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
        Schema::create('returs', function (Blueprint $table) {
            $table->id('id_retur');
            $table->dateTime('tanggal_retur');
            $table->string('bukti');
            $table->enum('jenis_retur', ['Rusak', 'Tidak Rusak']);
            $table->string('keterangan');
            $table->unsignedBigInteger('id_pesanan');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan_pembelis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('returs');
    }
};
