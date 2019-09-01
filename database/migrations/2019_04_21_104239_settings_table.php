<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('automaticAssignDelay')->default(60);
            $table->integer('automaticIDAApproveDelay')->default(60);
            $table->integer('law5CertificatePercentage')->default(40);
            $table->integer('exportFundPercentage')->default(10);
            $table->string('executiveManagerName');

            $table->string('mailServer')->nullable();
            $table->integer('mailServerPort')->nullable();
            $table->boolean('mailEnableSSL')->nullable();
            $table->string('fromEmail')->nullable();
            $table->string('fromEmailPassword')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Settings');
    }
}
