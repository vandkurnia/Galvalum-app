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
        Schema::table('riwayat_piutang', function (Blueprint $table) {
            $table->softDeletes(); // Menambahkan kolom 'deleted_at' ke tabel 'riwayat_piutang'
        });

        Schema::table('riwayat_hutang', function (Blueprint $table) {
            $table->softDeletes(); // Menambahkan kolom 'deleted_at' ke tabel 'riwayat_hutang'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('riwayat_piutang', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Menghapus kolom 'deleted_at' dari tabel 'riwayat_piutang'
        });

        Schema::table('riwayat_hutang', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Menghapus kolom 'deleted_at' dari tabel 'riwayat_hutang'
        });
    }
};
