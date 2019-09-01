<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nameEn');
            $table->string('nameAr');
            $table->unsignedInteger('chamberId');
            $table->boolean('isDeleted')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('chamberId')
                  ->references('id')->on('chambers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections');
    }
}
