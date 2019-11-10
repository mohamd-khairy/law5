<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('roleId');
            $table->unsignedInteger('sectorId')->nullable()->default(null);
            $table->foreign('roleId')
                ->references('id')->on('roles');
            $table->foreign('sectorId')
                ->references('id')->on('sectors');
            $table->string('name');
            $table->string('telephone');
            $table->string('email');
            $table->text('password');
            $table->text('token')->nullable()->default(null);
            $table->boolean('isEmailVerified')->default(false);
            $table->boolean('isActive');
            $table->boolean('isDeleted')->default(false);
            $table->text('resetPasswordCode')->nullable()->default(null);
            $table->dateTime('resetPasswordCodeCreationdate')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
