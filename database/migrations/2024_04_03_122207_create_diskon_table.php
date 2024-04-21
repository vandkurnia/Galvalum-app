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
        Schema::create('diskon', function (Blueprint $table) {
            $table->id('id_diskon');
            $table->uuid('hash_id_diskon')->unique();
            $table->string('kode_diskon', 10);
            $table->string('nama_diskon', 255);
            $table->enum('type', ['percentage', 'amount'])->default('percentage');
            $table->text('besaran');
            $table->enum('status', ['AKTIF', 'NONAKTIF'])->default('NONAKTIF');
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
        Schema::dropIfExists('diskon');
    }
};
