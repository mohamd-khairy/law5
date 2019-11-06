<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentMethodsTable extends Migration
{

    public function up()
    {
        Schema::create('assessment_methods', function(Blueprint $table) {
            $table->increments('id');
            $table->text('nameEn');
            $table->text('nameAr');
        });
    }

    public function down()
    {
        Schema::drop('assessment_methods');
    }
}
