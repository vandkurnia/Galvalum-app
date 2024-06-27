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
        Schema::create('custom_notifications', function (Blueprint $table) {
            $table->id('id_notifikasi');
            // $table->unsignedBigInteger('user_id');
            $table->string('type'); // 'piutang' or 'hutang'
            $table->unsignedBigInteger('id_data'); // The ID of the related data (NotaPembelian or Barang)
            $table->string('icon'); // Icon class for the notification
            $table->string('message'); // The notification message
            $table->timestamps();

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_notifications');
    }
};
