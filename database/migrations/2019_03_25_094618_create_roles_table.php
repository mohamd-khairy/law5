<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{

    public function up()
    {
        Schema::create('roles', function(Blueprint $table) {
            $table->increments('id');
            $table->string('nameEn');
            $table->string('nameAr');
            $table->string('key');
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('roles');
    }
}
