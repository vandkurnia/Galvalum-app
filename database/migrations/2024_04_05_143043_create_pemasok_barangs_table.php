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
        Schema::create('pemasok_barangs', function (Blueprint $table) {
            $table->id('id_pemasok');
            $table->string('nama_pemasok');
            $table->string('no_telp_pemasok');
            $table->string('alamat_pemasok');
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
        Schema::dropIfExists('pemasok_barangs');
    }
};
