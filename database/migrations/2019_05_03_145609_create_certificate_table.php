<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertificateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('certificateTypeId');
            $table->unsignedInteger('requestId');
            $table->integer('certificateNumber')->default(null);
            $table->dateTime('issueDate');
            $table->dateTime('managerApproveDate');
            $table->boolean('isWinnedTender')->default(false);
            $table->boolean('isDeleted')->default(false);
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('certificateTypeId')
                ->references('id')->on('certificate_types');
            $table->foreign('requestId')
                ->references('id')->on('requests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificates');
    }
}
