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
        Schema::create('modal_tambahan', function (Blueprint $table) {
            $table->id('id_modal_tambahan');
            $table->string('jenis_modal_tambahan');
            $table->string('deskripsi');
            $table->string('jumlah_modal');
            $table->string('tanggal');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('kas_keluar', function (Blueprint $table) {
            $table->bigIncrements('id_kas_keluar');
            $table->string('nama_pengeluaran');
            $table->string('deskripsi');
            $table->string('jumlah_pengeluaran');
            $table->date('tanggal');
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
    
        Schema::dropIfExists('kas_keluar');
        Schema::dropIfExists('modal_tambahan');
    }
};
