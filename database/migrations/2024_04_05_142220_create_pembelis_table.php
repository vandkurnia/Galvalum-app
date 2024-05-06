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
        Schema::create('pembelis', function (Blueprint $table) {
            $table->id('id_pembeli');
            $table->uuid('hash_id_pembeli')->unique();
            $table->string('nama_pembeli');
            $table->string('alamat_pembeli');
            $table->enum('jenis_pembeli', ['harga_normal', 'applicator', 'potongan'])->default('harga_normal');
            $table->string('no_hp_pembeli');
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
        Schema::dropIfExists('pembelis');
    }
};
