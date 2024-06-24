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
        Schema::table('bukubesar_barang', function (Blueprint $table) {
            $table->unsignedBigInteger('id_bukubesar')->nullable()->change();
            $table->decimal('nominal_dibayar', 25, 2)->default(0)->after('id_barang');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bukubesar_barang', function (Blueprint $table) {
            $table->unsignedBigInteger('id_bukubesar')->nullable(false)->change();
            $table->dropColumn('nominal_dibayar');
        });
    }
};
