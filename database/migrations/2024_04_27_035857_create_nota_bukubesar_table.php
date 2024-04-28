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
        Schema::create('nota_bukubesar', function (Blueprint $table) {
            $table->id('id_notabukubesar');
            $table->unsignedBigInteger('id_nota');
            $table->unsignedBigInteger('id_bukubesar');
            $table->timestamps();
            $table->foreign('id_nota')->references('id_nota')->on('nota_pembelis')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('nota_bukubesar');
    }
};
