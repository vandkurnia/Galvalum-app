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
            $table->string('kode_barang')->unique(); // Tambahkan kolom kode_barang
            $table->string('nama_barang');
            $table->decimal('harga_barang', 25, 2);
            $table->decimal('harga_barang_pemasok', 25, 2);
            // $table->integer('stok');
            $table->string('ukuran');
            // $table->enum('status_pembayaran', ['lunas', 'hutang'])->default('hutang');
            $table->decimal('total', 25, 2)->default(0);
            $table->decimal('nominal_terbayar', 10, 2)->default(0);
            $table->date('tenggat_bayar')->nullable();
            $table->unsignedBigInteger('id_pemasok')->nullable();
            $table->unsignedBigInteger('id_tipe_barang');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_pemasok')->references('id_pemasok')->on('pemasok_barangs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_tipe_barang')->references('id_tipe_barang')->on('tipe_barangs')->onUpdate('cascade')->onDelete('cascade');
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
