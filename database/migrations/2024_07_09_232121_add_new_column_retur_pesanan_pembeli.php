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
        Schema::table('retur_pesanan_pembeli', function (Blueprint $table) {
            $table->string('jenis_pembelian_sebelumnya');
            $table->decimal('harga_potongan_sebelumnya', 25, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retur_pesanan_pembeli', function (Blueprint $table) {
            $table->dropColumn('jenis_pembelian_sebelumnya');
            $table->dropColumn('harga_potongan_sebelumnya');
        });
    }
};
