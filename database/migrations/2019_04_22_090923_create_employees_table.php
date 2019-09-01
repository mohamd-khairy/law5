<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mobile');
            $table->unsignedInteger('chamberId')->nullable();
            $table->unsignedInteger('userId');
            $table->boolean('isDeleted')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('chamberId')
                  ->references('id')->on('chambers');

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
        Schema::dropIfExists('employees');
    }
}
