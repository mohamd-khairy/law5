<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('facilityName')->nullable(); 
            $table->string('legalEntityIdentifier')->nullable();
            $table->string('managerName')->nullable(); 
            $table->unsignedInteger('sectorId')->nullable();
            $table->unsignedInteger('userId'); 
            $table->boolean('isInsideIndusterialArea')->nullable();
            $table->unsignedInteger('governorateId')->nullable(); 
            $table->unsignedInteger('cityId')->nullable(); 
            $table->string('indusrialAreaName')->nullable(); 
            $table->string('blockNumber')->nullable();
            $table->string('areaNumber')->nullable();
            $table->string('areaOrDistrict')->nullable();
            $table->string('buildingNumber')->nullable();  
            $table->string('telephone')->nullable();
            $table->string('fax')->nullable();
            $table->string('authorityAcceptanceNumber')->nullable();
            $table->string('commercialRecord')->nullable();
            $table->string('licenseNumber')->nullable();
            $table->float('factoryArea')->nullable();
            $table->float('investmentCostAtConstructionTime')->nullable();
            $table->float('currentInvestmentCosts')->nullable();
            $table->string('taxCard')->nullable();
            $table->float('extentOfEnvironmentalConditionsImplementationAndTrashDisposal')->nullable();
            $table->float('extentOfDangerousMaterialsUsage')->nullable();
            $table->unsignedInteger('experienceTypeId')->nullable();
            $table->float('annualInsuranceValue')->nullable();
            $table->string('certificatesAndExtentOfSpecificationsMatching')->nullable();
            $table->unsignedInteger('insuredWorkersCount')->nullable();
            $table->string('entryNumberInIndusterialRecord')->nullable();
            $table->unsignedInteger('yearOfFirstCertificate')->nullable();
            $table->date('expirationDate')->nullable();
            $table->timestamps();
            $table->softDeletes(); 


            $table->foreign('sectorId')
                  ->references('id')->on('sectors');

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
        Schema::dropIfExists('applicants');
    }
}
