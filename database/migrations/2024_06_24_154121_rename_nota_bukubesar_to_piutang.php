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
        Schema::rename('nota_bukubesar', 'riwayat_piutang');
        Schema::table('riwayat_piutang', function (Blueprint $table) {

            $table->unsignedBigInteger('id_bukubesar')->nullable()->change();
            $table->decimal('nominal_dibayar', 25, 2)->after('id_bukubesar');
            $table->string('type_cicilan', 10)->default('cicilan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert the changes made to the 'riwayat_piutang' table
        Schema::table('riwayat_piutang', function (Blueprint $table) {
            // Drop the 'nominal_dibayar' column
            $table->dropColumn('type_riwayat');
            $table->dropColumn('nominal_dibayar');
            // Change 'id_bukubesar' to be non-nullable
            $table->unsignedBigInteger('id_bukubesar')->nullable(false)->change();
        });

        // Rename the table back to 'nota_bukubesar'
        Schema::rename('riwayat_piutang', 'nota_bukubesar');
    }
};
