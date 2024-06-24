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
        //
        Schema::table('retur_pembeli', function (Blueprint $table) {

            $table->enum('hidden', ['yes', 'no'])->default('no')->after('id_pembeli');
        });
        Schema::table('retur_pemasok', function (Blueprint $table) {
            $table->enum('hidden', ['yes', 'no'])->default('no')->after('id_barang');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retur_pembeli', function (Blueprint $table) {
            $table->dropColumn('hidden');
        });

        Schema::table('retur_pemasok', function (Blueprint $table) {
            $table->dropColumn('hidden');
        });
    }
};
