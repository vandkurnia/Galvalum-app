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
        Schema::table('retur_pemasok', function (Blueprint $table) {
            // Add the id_hutang_stok column, which is nullable
            $table->unsignedBigInteger('id_hutang_stok');

            // Add foreign key constraint
            $table->foreign('id_hutang_stok')->references('id_hutang_stok')->on('lacak_stok')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retur_pemasok', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['id_hutang_stok']);

            // Drop the id_hutang_stok column
            $table->dropColumn('id_hutang_stok');
        });
    }
};
