<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertificateCopyRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificateCopyRequest', function (Blueprint $table) {
           
            $table->bigIncrements('id');
            $table->unsignedInteger('applicantId');
            $table->bigInteger('certificateId');
            $table->unsignedInteger('count');   
            $table->tinyInteger('isChamberMember')->default('0');
            $table->tinyInteger('isSubscriptionFeesPaid')->default(0);
            $table->tinyInteger('isIDAFeesPaid')->default(0);
            $table->tinyInteger('isFEIFeesPaid')->default(0);
            $table->tinyInteger('isIssued')->default(0);
            $table->tinyInteger('isDeleted')->default(0);
            $table->softDeletes()->nullable();
            $table->timestamps();
            $table->timestamp('issueDate')->nullable();
            
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificateCopyRequest');
    }
}
