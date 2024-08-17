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
        Schema::table('riwayat_hutang', function (Blueprint $table) {
            $table->unsignedBigInteger('hutang_dan_stok_id')->after('id_barang');

            $table->foreign('hutang_dan_stok_id')
                  ->references('id_hutang_stok')
                  ->on('hutang_dan_stok')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('riwayat_hutang', function (Blueprint $table) {
            $table->dropForeign(['hutang_dan_stok_id']);
            $table->dropColumn('hutang_dan_stok_id');
        });
    }
};
