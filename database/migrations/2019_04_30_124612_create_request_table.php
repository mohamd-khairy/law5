<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('statusId')->nullable();
            $table->unsignedInteger('applicantId')->nullable();
            $table->unsignedInteger('assessmentId')->nullable();
            $table->unsignedInteger('employeeId')->nullable();
            $table->unsignedInteger('sectorId')->nullable();
            $table->unsignedInteger('sectionId')->nullable();
            $table->integer('openedByEmployeeId')->nullable();
            $table->string('chamberMemberNumber');
            $table->string('representativeTelephone');
            $table->string('representativeMobile');
            $table->string('representativeFax');
            $table->string('representativeEmail');
 
            $table->string('representativeName');
            $table->string('representativeNationalId');
            $table->integer('representativeTypeId');
            $table->string('representativeDelegationNumber');
            $table->string('representativeDelegationIssuedBy');
            $table->string('representativeMailingAddress');
            $table->string('industrialRegistry');
            $table->boolean('isOriginalsReceived')->default(false);
            $table->boolean('isChamberMember')->default(false);
            $table->boolean('isIDAFeesPaid')->default(false);
            $table->boolean('isFEIFeesPaid')->default(false);
            $table->boolean('isSubscriptionFeesPaid')->default(false);
            $table->boolean('isRenewal')->default(false);
            $table->integer('originalRequestId')->nullable();
            $table->boolean('isDeleted')->default(false);
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('statusId')
                ->references('id')->on('requeststatus');
            $table->foreign('applicantId')
                ->references('id')->on('applicants');
            $table->foreign('assessmentId')
                ->references('id')->on('assessments');
            $table->foreign('employeeId')
                ->references('id')->on('employees');
            $table->foreign('sectorId')
                ->references('id')->on('sectors');
            $table->foreign('sectionId')
                ->references('id')->on('sections');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
  