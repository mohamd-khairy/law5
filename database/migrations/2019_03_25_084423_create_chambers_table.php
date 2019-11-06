<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChambersTable extends Migration
{

    public function up()
    {
        Schema::create('chambers', function(Blueprint $table) {
            $table->increments('id');
            $table->string('nameEn');
            $table->string('nameAr');
            $table->integer('assessmentMethod')->unsigned()->index();
            $table->boolean('isDeleted')->default(false);
            // Constraints declaration
            $table->foreign('assessmentMethod')
                ->references('id')
                ->on('assessment_methods');
        });
    }

    public function down()
    {
        Schema::drop('chambers');
    }
}
