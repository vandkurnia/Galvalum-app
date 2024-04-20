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
        Schema::create('jenis_pelanggan', function (Blueprint $table) {
            $table->id('id_jenis_pelanggan');
            $table->uuid('hash_id_jenis_pelanggan')->unique();
            $table->string('nama_jenis_pelanggan');
            $table->bigInteger('nominal_jenis'); // Allow null for cases where migration fee doesn't apply
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
        Schema::dropIfExists('jenis_pelanggan');
    }
};
