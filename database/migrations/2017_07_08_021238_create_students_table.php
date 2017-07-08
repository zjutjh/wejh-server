<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uno', 100)->unique()->comment("学号");
            $table->string('iid')->comment("身份证号");
            $table->string('name')->comment("姓名");
            $table->string('sex')->comment("性别");
            $table->string('grade')->comment("年级");
            $table->string('college')->comment("学院");
            $table->string('major')->comment("专业");
            $table->string('class')->comment("班级");
            $table->string('students')->comment("生源地");
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
        Schema::dropIfExists('students');
    }
}
