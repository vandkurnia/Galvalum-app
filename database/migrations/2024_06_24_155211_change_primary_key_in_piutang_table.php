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
            $table->renameColumn('id_notabukubesar', 'id_piutang');
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

            $table->renameColumn('id_piutang', 'id_notabukubesar');
        });
    }
};
