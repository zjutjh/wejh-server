<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsernameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() // 增加新数据表
    {
        if (!Schema::hasTable('user_information')) 
        {
            Schema::create('user_information', function (Blueprint $table) {
                if (!Schema::hasColumn('user_information', 'name'))  // 用户姓名
                    $table->string('name');
                if (!Schema::hasColumn('user_information', 'id')) // 用户学号
                    $table->string('id')->unique();  // 设置用户学号为唯一索引
                if (!Schema::hasColumn('user_information', 'zone')) // 用户校区 
                    $table->string('zone');  
                if (!Schema::hasColumn('user_information', 'graduateTime')) // 用户毕业年份
                    $table->date('graduateTime');  
            });
        }    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 清空数据表
        Schema::dropIfExists('user_information');
    }
}
