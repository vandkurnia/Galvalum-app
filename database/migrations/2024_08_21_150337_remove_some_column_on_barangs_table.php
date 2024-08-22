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
        Schema::table('barangs', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['id_bukubesar']);

            // Drop the columns
            $table->dropColumn(['id_bukubesar', 'dp_barang', 'nominal_terbayar']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barangs', function (Blueprint $table) {
            // Re-add the columns
            $table->unsignedBigInteger('id_bukubesar')->nullable();
            $table->decimal('dp_barang', 8, 2)->nullable();
            $table->decimal('nominal_terbayar', 8, 2)->nullable();

            // Re-add the foreign key constraint
            $table->foreign('id_bukubesar')->references('id_bukubesar')->on('bukubesar')->onDelete('cascade');
        });
    }
};
