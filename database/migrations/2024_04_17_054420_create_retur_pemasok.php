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
        Schema::create('retur_pemasok', function (Blueprint $table) {
            $table->id('id_retur_pemasok');
            $table->uuid('hash_id_retur_pemasok')->unique();
            $table->string('no_retur_pemasok');
            $table->string('faktur_retur_pemasok');
            $table->string('tanggal_retur');
            $table->binary('bukti_retur_pemasok');
            $table->enum('jenis_retur', ['Rusak', 'Tidak Rusak']);
            $table->decimal('total_nilai_retur', 10, 2);
            $table->string('pengembalian_data');
            $table->string('kekurangan');
            $table->enum('status', ['Belum Selesai', 'Selesai']);
            $table->unsignedBigInteger('id_pemasok');
            $table->foreign('id_pemasok')->references('id_pemasok')->on('pemasok_barangs')->onUpdate('CASCADE')->onDelete('cascade');
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
        Schema::dropIfExists('retur_pemasok');
    }
};
