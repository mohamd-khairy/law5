<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeSectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_sectors', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employeeId')->default(null)->nullable();
            $table->unsignedInteger('sectorId')->default(null)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('employeeId')
                  ->references('id')->on('employees');
            $table->foreign('sectorId')
                  ->references('id')->on('sectors');       
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_sectors');
    }
}
