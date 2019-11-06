<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCoulmnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('step2token')->default(null)->nullable();
            $table->string('step2code' , 4)->default(null)->nullable();
            $table->dateTime('codeCreationDate')->default(null)->nullable();
            $table->boolean('checkStep2Token')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('step2token');
            $table->dropColumn('step2code');
            $table->dropColumn('codeCreationDate');
            $table->dropColumn('checkStep2Token');
        });
    }
}
