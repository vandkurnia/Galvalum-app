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
            $table->id('id_bukubesar');
            $table->uuid('hash_id_bukubesar')->unique();
            $table->unsignedBigInteger('id_akunbayar')->nullable();
            // $table->unsignedBigInteger('id_kategori'); // Menambahkan kolom id_kategori
            $table->date('tanggal');
            $table->string('kategori', 255);
            $table->string('keterangan', 255);
            $table->double('debit', 14, 0)->default(0);
            $table->double('kredit', 14, 0)->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('id_akunbayar')->references('id_akunbayar')->on('akun_bayar')->onDelete('CASCADE');
            // $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->onDelete('CASCADE'); // Menambahkan constraint foreign key
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
