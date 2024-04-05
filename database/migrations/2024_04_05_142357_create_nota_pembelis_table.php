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
        Schema::create('nota_pembelis', function (Blueprint $table) {
            $table->id('id_nota');
            $table->string('jenis_pembelian');
            $table->string('status_pembelian');
            $table->unsignedBigInteger('id_pembeli');
            $table->unsignedBigInteger('id_admin');
            $table->timestamps();

            $table->foreign('id_pembeli')->references('id_pembeli')->on('pembelis');
            $table->foreign('id_admin')->references('id_admin')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nota_pembelis');
    }
};
