<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class User extends Moloquent
{
    // mongodb的易扩展特性导致某些限制只能在PHP里做出配置，所以务必在所有Model里做出一些相关配置

    // 此处定义默认值，要保证每个值都有对应的访问器
    protected $defaults = array(
        'email' => null,
        'phone' => null,
        'password' => null,
        'avatar' => null, // 头像url
        'nickname' => null,// 微信昵称 or else
        'subscribe' => false, // 是否关注公众号
        'name' => null, // 真实姓名
        'gender' => 0, //性别
        'grade' => null, // 年级
        'college' => null, // 学院
        'major' => null, // 专业
        'class' => null, // 班级
        'user_type' => 1, // 用户类型，默认学生(1。学生，2.教师，3.研究生）
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

    protected $hidden = [
        'password'
    ];

    // 后期另外增加的字段,不需定义，只需在上面默认值处增加字段
    protected $appends;

    protected $primaryKey = '_id';
    public $incrementing = false;

    function __construct()
    {
        $this->appends = array_keys($this->defaults);
        parent::__construct(...func_get_args());
    }

    /**
     * 不能被批量赋值的属性
     *
     * @var array
     */
    protected $guarded = ['_id'];

    /**
     * 返回collection的值，如不存在，则返回默认值
     * @param  string   $field
     * @return mixed
     */
    public function getValue($field) {
        if (isset($this->attributes[$field])) {
            return $this->attributes[$field];
        } else {
            return $this->defaults[$field];
        }
    }

    /**
     * 学号or教职工号
     * @param  string  $value
     * @return string
     */
    public function getUnoAttribute($value)
    {
        return $value;
    }

    /**
     * 用户邮箱
     * @param  string  $value
     * @return string
     */
    public function getEmailAttribute($value)
    {
        return $this->getValue('email');
    }

    /**
     * 用户手机号
     * @param  string  $value
     * @return string
     */
    public function getPhoneAttribute($value)
    {
        return $this->getValue('phone');
    }

    /**
     * 用户密码
     * @param  string  $value
     * @return string
     */
    public function getPasswordAttribute($value)
    {
        return $this->getValue('password');
    }

    /**
     * 用户头像
     * @param  string  $value
     * @return string
     */
    public function getAvatarAttribute($value)
    {
        return $this->getValue('avatar');
    }

    /**
     * 用户昵称, 视情况以后要不要转换emoji
     * @param  string  $value
     * @return string
     */
    public function getNicknameAttribute($value)
    {
        return $this->getValue('nickname');
    }

    /**
     * 用户是否关注服务号
     * @param  integer  $value
     * @return integer
     */
    public function getSubscribeAttribute($value)
    {
        return $this->getValue('subscribe');
    }

    /**
     * 用户真实姓名
     * @param  string  $value
     * @return string
     */
    public function getNameAttribute($value)
    {
        return $this->getValue('name');
    }

    /**
     * 用户性别，0为女，1为男
     * @param  integer  $value
     * @return integer
     */
    public function getGenderAttribute($value)
    {
        return $this->getValue('gender');
    }

    /**
     * 年级，如2013，2017
     * @param  integer  $value
     * @return integer
     */
    public function getGradeAttribute($value)
    {
        return $this->getValue('grade');
    }

    /**
     * 学院名称
     * @param  string   $value
     * @return string
     */
    public function getCollegeAttribute($value)
    {
        return $this->getValue('college');
    }

    /**
     * 专业
     * @param  string   $value
     * @return string
     */
    public function getMajorAttribute($value)
    {
        return $this->getValue('major');
    }

    /**
     * 班级名称
     * @param  string   $value
     * @return string
     */
    public function getClassAttribute($value)
    {
        return $this->getValue('class');
    }

    /**
     * 用户类型，如学生，教师(1。学生，2.教师，3.研究生）
     * @param  integer $value
     * @return integer
     */
    public function getUserTypeAttribute($value)
    {
        return $this->getValue('user_type');
    }

    /**
     * 用户组，如管理员，普通用户等，但必须按照user_groups表中的字段来
     * @param  integer  $value
     * @return integer
     */
    public function getUserGroupAttribute($value)
    {
        return $this->getValue('user_group');
    }

    /**
     * 用户的登录token
     * @param  string   $value
     * @return string
     */
    public function getRememberTokenAttribute($value)
    {
        return $this->getValue('remember_token');
    }

    /**
     * 用户查成绩选择的学期
     * @param  string   $value
     * @return string
     */
    public function getScoreTermAttribute($value)
    {
        return $this->getValue('score_term');
    }

    /**
     * 用户查课表选择的学期
     * @param  string   $value
     * @return string
     */
    public function getClassTermAttribute($value)
    {
        return $this->getValue('class_term');
    }

    /**
     * 用户查排考选择的学期
     * @param  string   $value
     * @return string
     */
    public function getExamTermAttribute($value)
    {
        return $this->getValue('exam_term');
    }

    /**
     * 用户的精弘通行证密码
     * @param  string   $value
     * @return string
     */
    public function getJhPasswordAttribute($value)
    {
        return $this->getValue('jh_password');
    }

    /**
     * 用户的原创教务系统密码
     * @param  string   $value
     * @return string
     */
    public function getYcPasswordAttribute($value)
    {
        return $this->getValue('yc_password');
    }

    /**
     * 用户的校园一卡通密码
     * @param  string   $value
     * @return string
     */
    public function getCardPasswordAttribute($value)
    {
        return $this->getValue('card_password');
    }

    /**
     * 用户的图书馆密码
     * @param  string   $value
     * @return string
     */
    public function getLibPasswordAttribute($value)
    {
        return $this->getValue('lib_password');
    }
}
