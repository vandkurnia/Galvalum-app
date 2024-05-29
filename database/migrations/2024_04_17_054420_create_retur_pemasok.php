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
        Schema::create('retur_pemasok', function (Blueprint $table) {
            $table->id('id_retur_pemasok');
            $table->uuid('hash_id_retur_pemasok')->unique();
            $table->string('no_retur_pemasok');
            $table->string('tanggal_retur');
            $table->binary('bukti_retur_pemasok');
            $table->enum('jenis_retur', ['Rusak', 'Tidak Rusak']);
            // $table->string('faktur_retur_pemasok');
            $table->decimal('total_nilai_retur', 10, 2)->default(0);
            $table->string('pengembalian_data')->default('0');
            $table->string('kekurangan')->default('0');
            $table->decimal('harga', 20, 2);
            $table->decimal('total', 20, 2);
            $table->decimal('qty', 20, 2);
            $table->decimal('qty_sebelum_perubahan', 20, 2)->nullable();
            $table->enum('type_retur_pesanan', ['retur_tambah_barang', 'retur_tambah_stok', 'retur_murni_rusak', 'retur_murni_tidak_rusak']);
            $table->enum('status', ['Belum Selesai', 'Selesai'])->default('Belum Selesai');
            $table->unsignedBigInteger('id_pemasok')->nullable();
            $table->foreign('id_pemasok')->references('id_pemasok')->on('pemasok_barangs')->onUpdate('CASCADE')->onDelete('cascade');
            $table->unsignedBigInteger('id_barang');
            $table->foreign('id_barang')->references('id_barang')->on('barangs')->onUpdate('CASCADE')->onDelete('cascade');
            $table->unsignedBigInteger('id_stok_barang');
            $table->foreign('id_stok_barang')->references('id')->on('stok_barang')->onUpdate('CASCADE')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retur_pemasok');
    }
};
