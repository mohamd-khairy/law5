<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatAssessmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('applicantId')->unsigned()->index();
            // Constraints declaration
           $table->integer('chamberId')->unsigned()->index();
            $table->string('productName');
            $table->boolean('manufactoringByOthers');
            $table->string('manufactoringCompanyName');
            $table->string('manufactoringCompanyIndustrialRegistry');
            $table->boolean('isTotals');
            $table->double('annualProductionCapacity', 20, 2)->nullable();
            $table->double('powerResources', 20, 2)->nullable();
            $table->double('localSpareParts', 20, 2)->nullable();
            $table->double('importedSpareParts', 20, 2)->nullable();
            $table->double('researchAndDevelopment', 20, 2)->nullable();
            $table->double('wages', 20, 2)->nullable();
            $table->double('annualDepreciation', 20, 2)->nullable();
            $table->double('administrativeExpenses', 20, 2)->nullable();
            $table->double('marketingExpenses', 20, 2)->nullable();
            $table->double('otherExpenses', 20, 2)->nullable();

            $table->double('localComponentsTotals', 20, 2)->nullable();
            $table->double('localPackagingComponentsTotals', 20, 2)->nullable();
            $table->double('importedComponentsTotals', 20, 2)->nullable();
            $table->double('importedPackagingComponentsTotals', 20, 2)->nullable();
            
            $table->foreign('chamberId')
                    ->references('id')->on('chambers');

            $table->foreign('applicantId')
                   ->references('id')->on('applicants');
            
            $table->boolean('isDeleted')->default(false);
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessments');
    }
}
