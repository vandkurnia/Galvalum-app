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
        Schema::create('akun_bayar', function (Blueprint $table) {
            $table->id('id_akunbayar');
            $table->uuid('hash_id_akunbayar')->unique();
            $table->string('no_akun', 30)->unique();
            $table->string('nama_akun', 255);
            $table->string('tipe_akun', 50);
            $table->decimal('saldo', 18, 0)->default(0);
            $table->decimal('saldo_akhir', 18, 0)->default(0);
            $table->decimal('saldo_anak', 18, 0)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('akun_bayar');
    }
};
