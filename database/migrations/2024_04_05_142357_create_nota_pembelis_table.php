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
            $table->string('no_nota')->unique();
            $table->unsignedBigInteger('id_pembeli');
            $table->unsignedBigInteger('id_admin');
            $table->timestamps();
            $table->string('metode_pembayaran'); // tambahan field metode_pembayaran
            $table->enum('status_pembayaran', ['lunas', 'hutang'])->default('hutang');
            $table->softDeletes();
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
