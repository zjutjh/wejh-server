<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_links', function (Blueprint $table) {
            $table->increments('id')->comment("自增长id");
            $table->string('uno')->comment("关联用户学号or教职工号");
            $table->string('type')->comment("第三方平台类型");
            $table->string('openid')->comment("第三方平台用户标识");
            $table->string('access_token')->comment("第三方平台用户授权凭证");
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
        Schema::dropIfExists('user_links');
    }
}
