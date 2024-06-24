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
        Schema::table('nota_pembelis', function (Blueprint $table) {
            $table->unsignedBigInteger('id_bukubesar')->after('id_admin')->nullable();
            $table->foreign('id_bukubesar')->references('id_bukubesar')->on('bukubesar')->onUpdate('cascade')->onDelete('cascade');
            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nota_pembelis', function( Blueprint $table) {
            $table->dropForeign(['id_bukubesar']);
            $table->dropColumn('id_bukubesar');

        });
    }
};
