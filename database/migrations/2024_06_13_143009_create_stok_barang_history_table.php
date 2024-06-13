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
        Schema::create('stok_barang_history', function (Blueprint $table) {
            $table->id('id_stok');
            $table->unsignedBigInteger('id_barang');
            $table->foreign('id_barang')->references('id_barang')->on('barangs')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('stok_masuk', 25, 2)->default(0);
            $table->decimal('stok_keluar', 25, 2)->default(0);
            $table->decimal('stok_terkini', 25, 2);
            $table->timestamps();
        });
        Schema::table('log_stok_barang', function (Blueprint $table) {
            $table->unsignedBigInteger('id_stok_barang_history')->nullable();
            $table->foreign('id_stok_barang_history')->references('id_stok')->on('stok_barang_history')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign key dan kolom pada tabel log_stok_barang terlebih dahulu
        Schema::table('log_stok_barang', function (Blueprint $table) {
            $table->dropForeign(['id_stok_barang_history']);
            $table->dropColumn('id_stok_barang_history');
        });

        // Drop tabel stok_barang_history
        Schema::dropIfExists('stok_barang_history');
    }
};
