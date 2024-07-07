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
        Schema::table('retur_pembeli', function (Blueprint $table) {
            $table->decimal('dp_before', 25, 2)->default(0);
            $table->date('tanggal_penyelesaian_before')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retur_pembeli', function (Blueprint $table) {
            $table->dropColumn('tanggal_penyelesaian_before');
            $table->dropColumn('dp_before');
            
        });
    }
};
