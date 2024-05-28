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
        Schema::create('log_nota', function (Blueprint $table) {
            $table->id();
        
            $table->json('json_content');
            $table->string('tipe_log', 50);
            $table->string('keterangan', 255)->nullable();
            $table->unsignedBigInteger('id_admin');
            $table->unsignedBigInteger('id_nota');
            $table->foreign('id_nota')->references('id_nota')->on('nota_pembelis')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_admin')->references('id_admin')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_nota');
    }
};
