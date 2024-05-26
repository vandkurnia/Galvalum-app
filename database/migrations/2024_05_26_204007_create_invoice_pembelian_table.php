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
        Schema::create('invoice_pembelian', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('users');
            $table->foreign('users')->references('id_admin')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('id_nota');
            $table->foreign('id_nota')->references('id_nota')->on('nota_pembelis')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_pembelian');
    }
};
