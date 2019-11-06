<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenderWinnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_winners', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('certificateId');
            $table->string('TenderDescription');
            $table->double('TenderValue');
            $table->dateTime('TenderDate');
            $table->timestamps(); 
            
            $table->foreign('certificateId')
            ->references('id')->on('certificates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_winners');
    }
}
