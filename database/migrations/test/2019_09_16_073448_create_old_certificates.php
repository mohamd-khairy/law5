<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOldCertificates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_certificates', function (Blueprint $table) {

            // $table->bigIncrements('integralId');
            $table->string('id', 30)->primary();
            $table->unsignedTinyInteger('certificateTypeId');
            $table->string('companyName');
            $table->string('companyActivity');
            $table->string('productName');
            $table->unsignedInteger('copy');
            $table->string('companyAddress');
            $table->string('companyCity', 50);
            $table->string('companyRegNo');
            $table->double('localPercentage');
            $table->tinyInteger('manufacturingByOthers');
            $table->text('manufacturingCompanyName');
            $table->string('manufacturingCompanyIndustrialRegistry');
            $table->string('executiveManagerName');
            $table->unsignedSmallInteger('issueYear')->nullable();
            $table->timestamp('startDate')->nullable();
            $table->timestamp('endDate')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('old_certificates');
    }
}
