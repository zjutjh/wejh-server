<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class User extends Moloquent
{
    // mongodb的易扩展特性导致某些限制只能在PHP里做出配置，所以务必在所有Model里做出一些相关配置

    // 此处定义create时的默认值，请务必配置，以便保证字段完整性，因为返回给前端时是不走访问器的，所以需要设置字段减少前端工作
    protected $attributes = array(
        // 'password' => '',
        'avatar' => null, // 头像url
        'nickname' => null,// 微信昵称 or else
        'subscribe' => 0, // 是否关注公众号
        'name' => null, // 真实姓名
        'gender' => 0, //性别
        'grade' => null, // 年级
        'college' => null, // 学院
        'major' => null, // 专业
        'class' => null, // 班级
        'user_group' => 0, // 用户组, 默认普通用户
        'remember_token' => null, // 登录token标识
        // 接下来是一些用户配置
        'score_term' => '2017/2018(1)', // 成绩学期
        'class_term' => '2017/2018(1)', // 课表学期
        'exam_term' => '2017/2018(1)', // 排考学期
        'jh_password' => null, // 精弘通行证密码
        'yc_password' => null, // 原创教务密码
        'card_password' => null, // 一卡通密码
        'lib_password' => null, // 图书馆密码

    );

    protected $primaryKey = '_id';
    public $incrementing = false;

    /**
     * 不能被批量赋值的属性
     *
     * @var array
     */
    protected $guarded = ['_id'];

    /**
     * 获取用户的名字
     *
     * @param  string  $value
     * @return string
     */
    public function getNameAttribute($value)
    {
        return $value ? $value : '';
    }
}
