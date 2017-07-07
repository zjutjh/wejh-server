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
            $table->increments('id')->comment("自增长用户id");
            $table->string('uno')->unique()->comment("学号");
            $table->string('password')->comment("密码");
            $table->string('email', 100)->unique()->nullable()->comment("邮箱");
            $table->string('phone', 100)->unique()->nullable()->comment("手机");
            $table->tinyInteger('user_type')->default(1)->comment("用户类型，默认学生(1。学生，2.教师，3.研究生）");
            $table->integer('user_group')->default(1)->comment("用户组");

            $table->string('avatar')->default('')->comment("头像");

            //$table->json('wechat_info')->comment("微信相关信息");
            //$table->string('nickname')->default('')->comment("微信昵称");
            //$table->integer('subscribe')->default(0)->comment("是否关注");

            //学校信息
            //$table->json('school_info')->comment("学校相关信息");
            //$table->string('name')->default('')->comment("姓名");
            //$table->tinyInteger('gender')->default(0)->comment("性别0-女 1-男");
            //$table->string('grade')->default('')->comment("年级");
            //$table->string('college')->default('')->comment("学院");
            //$table->string('major')->default('')->comment("专业");
            //$table->string('class')->default('')->comment("班级");

            //所有扩展字段都放进这个字段里
            $table->json('ext')->comment("扩展字段");
            $table->rememberToken()->comment("登录token");
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
        Schema::dropIfExists('users');
    }
}
