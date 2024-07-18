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
        // Keterangan
        Schema::table('stok_barang_history', function (Blueprint $table) {
            $table->string('keterangan_stok_history')->nullable(); // Description or reason for stock change

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Keterangan
        Schema::table('stok_barang_history', function (Blueprint $table) {
            $table->dropColumn('keterangan_stok_history'); // Description or reason for stock change

        });
    }
};
