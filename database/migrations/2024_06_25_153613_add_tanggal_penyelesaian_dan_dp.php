<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use League\CommonMark\Parser\Block\BlockContinueParserWithInlinesInterface;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nota_pembelis', function (Blueprint $table) {
            $table->date('tanggal_penyelesaian')->nullable();
            $table->decimal('dp', 15, 2)->default(0); // Menggunakan tipe data decimal untuk menyimpan uang dengan dua desimal
        });
        Schema::table('barangs', function (Blueprint $table) {

            $table->date('tanggal_penyelesaian')->nullable();
            $table->decimal('dp', 15, 2)->default(0);
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

            $table->dropColumn('tanggal_penyelesaian');
            $table->dropColumn('dp');
        });

        Schema::table('nota_pembelis', function (Blueprint $table) {
            $table->dropColumn('tanggal_penyelesaian');
            $table->dropColumn('dp');
        });
    }
};
