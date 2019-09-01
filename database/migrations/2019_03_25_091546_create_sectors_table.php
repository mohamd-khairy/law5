<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectorsTable extends Migration
{

    public function up()
    {
        Schema::create('sectors', function(Blueprint $table) {
            $table->increments('id');
            $table->string('nameEn');
            $table->string('nameAr');
            $table->boolean('isDeleted')->default(false);
            // Constraints declaration

        });
    }

    public function down()
    {
        Schema::drop('sectors');
    }
}
