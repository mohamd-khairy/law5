<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('requestId');
            $table->unsignedInteger('actionId');
            $table->unsignedInteger('byUserId')->nullable();
            $table->unsignedInteger('toUserId')->nullable();
            $table->boolean('isAuto')->default(false);
            $table->string('comment')->nullable();
            $table->foreign('requestId')
                ->references('id')->on('requests');
            $table->foreign('actionId')
                ->references('id')->on('actions');
            $table->foreign('byUserId')
                ->references('id')->on('users');
            $table->foreign('toUserId')
                ->references('id')->on('users');
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
        Schema::dropIfExists('request_actions');
    }
}
