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
        // 由于mongodb的特性，设置非主键的字段无意义，所以在此将一些非主键字段注释掉，反正可以瞎几把扩展
        Schema::create('users', function (Blueprint $table) {
            // $table->increments('uid')->comment("自增长用户id");
            $table->string('uno')->unique()->comment("学号");
            // $table->string('password')->nullable()->comment("密码");
            $table->string('email', 100)->unique()->nullable()->comment("邮箱");
            $table->string('phone', 100)->unique()->nullable()->comment("手机");
            // $table->string('avatar')->default('')->comment("微信头像");
            // $table->string('nickname')->default('')->comment("微信昵称");
            // $table->integer('subscribe')->default(0)->comment("是否关注");
            // $table->string('name')->default('')->comment("姓名");
            // $table->tinyInteger('sex')->default(0)->comment("性别0-女 1-男");
            // $table->string('grade')->default('')->comment("年级");
            // $table->string('college')->default('')->comment("学院");
            // $table->string('major')->default('')->comment("专业");
            // $table->string('class')->default('')->comment("班级");
            // $table->text('user_setting')->comment("用户设置");
            // $table->tinyInteger('is_admin')->default(0)->comment("是否管理员");
            // $table->rememberToken()->comment("登录token");
            // $table->timestamps();
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
