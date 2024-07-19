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
        // Tambah Stok Seluruh dan dp
        Schema::table('barangs', function (Blueprint $table) {
            $table->decimal('stok_seluruh', 25, 2)->after('stok')->default(0)->comment('Total stok dari awal hingga akhir');
            $table->decimal('dp_barang', 25, 2)->after('stok_seluruh')->default(0);
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('dp_barang');
            $table->dropColumn('stok_seluruh');
        });
    }
};
