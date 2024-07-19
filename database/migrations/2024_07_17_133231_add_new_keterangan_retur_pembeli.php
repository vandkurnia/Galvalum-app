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

    //  Memberikan keterangan pada Retur Pembeli
    public function up()
    {
        Schema::table('retur_pembeli', function (Blueprint $table){
           
            $table->string('keterangan_retur_pembeli')->nullable();
        });

        Schema::table('retur_pemasok', function (Blueprint $table) {
            $table->string('keterangan_retur_pemasok')->nullable();

        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retur_pemasok', function (Blueprint $table) {
            $table->dropColumn('keterangan_retur_pemasok');

        });
        Schema::table('retur_pembeli', function (Blueprint $table){

            $table->dropColumn('keterangan_retur_pembeli');
        });

        
    }
};
