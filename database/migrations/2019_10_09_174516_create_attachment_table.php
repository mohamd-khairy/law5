<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('requestId')->default(null);
            $table->unsignedInteger('applicantId')->default(null);                  
            $table->boolean('isRepresentativeProof')->default(false);
            $table->string('relativePath');
            $table->string('originalName');
            $table->boolean('isDeleted')->default(false);
            $table->foreign('requestId')
                  ->references('id')->on('requests');
            $table->foreign('applicantId')
                  ->references('id')->on('applicants');
            $table->softDeletes();
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
        Schema::dropIfExists('attachments');
    }
}
