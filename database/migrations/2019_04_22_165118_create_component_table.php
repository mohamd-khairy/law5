<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComponentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('components', function (Blueprint $table) {
            $table->increments('id');
           $table->bigInteger('assessmentId')->unsigned()->index();
            $table->string('componentName');
            $table->string('unit');
            $table->double('quantity', 20, 2);
            $table->double('unitPrice', 20, 2);
            $table->string('supplier')->nullable();
            $table->double('rate', 20, 2)->nullable();
            $table->double('CIF', 20, 2)->nullable();
            $table->boolean('isPackaging')->default(false);
            $table->boolean('isImported')->default(false);

            $table->foreign('assessmentId')
                    ->references('id')->on('assessments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('components');
    }
}
