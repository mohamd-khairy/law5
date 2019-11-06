<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrustedDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trusted_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('userId')->default(null);
            $table->text('trustToken')->default(null);
            $table->timestamps();
            $table->foreign('userId')
                ->references('id')->on('users');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trusted_devices');
    }
}
