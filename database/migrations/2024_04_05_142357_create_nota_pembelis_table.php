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
            $table->string('metode_pembayaran'); // tambahan field metode_pembayaran
            $table->enum('status_pembayaran', ['lunas', 'hutang'])->default('hutang');
            $table->string('sub_total', 45)->default(0);
            $table->decimal('nominal_terbayar', 10, 2)->default(0);
            $table->date('tenggat_bayar')->nullable();
            $table->string('diskon', 45)->default(0);
            $table->string('ongkir', 45)->default(0);
            $table->string('total', 45)->default(0);
            $table->timestamps();
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
