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
        Schema::create('bukubesar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_akunbayar')->nullable();
            $table->date('tanggal');
            $table->string('kategori', 255);
            $table->string('keterangan', 255);
            $table->double('debit', 14, 0)->default(0);
            $table->double('kredit', 14, 0)->default(0);
            $table->unsignedBigInteger('id_transaksi')->nullable();
            $table->timestamps();

            $table->foreign('id_akunbayar')->references('id')->on('akun_bayar')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bukubesar');
    }
};
