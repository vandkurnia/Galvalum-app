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
        Schema::create('retur_pembeli', function (Blueprint $table) {
            $table->id('id_retur_pembeli');
            $table->uuid('hash_id_retur_pembeli')->unique();
            $table->unsignedBigInteger('id_nota');
            $table->string('no_retur_pembeli');
            $table->string('faktur_retur_pembeli');
            $table->string('tanggal_retur_pembeli');
            $table->binary('bukti_retur_pembeli');
            $table->enum('jenis_retur', ['Rusak', 'Tidak Rusak']);
            $table->decimal('total_nilai_retur', 10, 2)->default(0);
            $table->decimal('pengembalian_data', 10, 2)->default(0);
            $table->decimal('kekurangan', 10, 2)->default(0);
            $table->enum('status', ['Belum Selesai', 'Selesai']);
            $table->unsignedBigInteger('id_pembeli');
            $table->foreign('id_pembeli')->references('id_pembeli')->on('pembelis')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes(); // Menambahkan soft delete
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retur_pembeli');
    }
};
