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
            $table->increments('uid')->comment("自增长用户id");
            $table->string('uno')->unique()->comment("学号");
            $table->string('password')->nullable()->comment("密码");
            $table->string('email', 100)->unique()->nullable()->comment("邮箱");
            $table->string('phone', 100)->unique()->nullable()->comment("手机");

            //微信信息，之后应该会改成关联查询，todo
            $table->json('wechat_info')->comment("微信相关信息");
            //$table->string('avatar')->default('')->comment("微信头像");
            //$table->string('nickname')->default('')->comment("微信昵称");
            //$table->integer('subscribe')->default(0)->comment("是否关注");

            $table->tinyInteger('user_type')->default(0)->comment("用户类型，默认学生(1。学生，2.教师，3.研究生）");
            //学校信息
            // 学校信息，之后应该会改成关联查询，todo
            $table->json('school_info')->comment("学校相关信息");
            //$table->string('name')->default('')->comment("姓名");
            //$table->tinyInteger('gender')->default(0)->comment("性别0-女 1-男");
            //$table->string('grade')->default('')->comment("年级");
            //$table->string('college')->default('')->comment("学院");
            //$table->string('major')->default('')->comment("专业");
            //$table->string('class')->default('')->comment("班级");

            //以后增加密码和学期的关联查询，todo
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
