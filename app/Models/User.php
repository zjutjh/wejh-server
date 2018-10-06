<?php
/**
 * User: cccRaim
 * Date: 2017/7/3
 * Time: 00:11
 */

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use Notifiable;

    // 此处定义扩展字段默认值
    protected $defaults = array(
        /*'email' => null,
        'phone' => null,
        'password' => null,
        'avatar' => null, // 头像url
        'user_type' => 1, // 用户类型，默认学生(1。学生，2.教师，3.研究生）
        'user_group' => 0, // 用户组, 默认普通用户
        'remember_token' => null, // 登录token标识*/

        /**
         * 扩展字段
         */
        'wechat_info' => [
            'avatar' => null, // 头像url
            'nickname' => null,// 微信昵称 or else
            'subscribe' => false, // 是否关注公众号
        ],
        'school_info' => [
            'name' => null, // 真实姓名
            'gender' => 0, //性别
            'grade' => null, // 年级
            'college' => null, // 学院
            'major' => null, // 专业
            'class' => null, // 班级
        ],
        // 接下来是一些用户配置
        'terms' => [
            'score_term' => '2018/2019(1)', // 成绩学期
            'class_term' => '2018/2019(1)', // 课表学期
            'exam_term' => '2018/2019(1)', // 排考学期
        ],
        'passwords' => [
            'jh_password' => null, // 精弘通行证密码
            'yc_password' => null, // 原创教务密码
            'zf_password' => null, // 原创教务密码
            'card_password' => null, // 一卡通密码
            'lib_password' => null, // 图书馆密码
        ],

    );

    /**
     * 应该被转化为原生类型的属性
     *
     * @var array
     */
    protected $casts = [
        'ext' => 'array',
    ];

    protected $hidden = [
        'remember_token',
        'password'
    ];

    /**
     * 不能被批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 重写toArray方法
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = array_merge($this->attributesToArray(), $this->relationsToArray());

        // 隐藏扩展字段的密码字段
        unset($attributes['ext']['passwords']);

        return $attributes;
    }

    /**
     * 扩展字段
     * @param  string  $value
     * @return array
     */
    public function getExtAttribute($value)
    {
        $value = json_decode($value, true);

        $defaults = array_dot($this->defaults);

        foreach ($defaults as $k => $val) {
            if (!$hasItems = array_has($value ,$k)) {
                array_set($value, $k, $val);
            }
        }

        foreach ($value['passwords'] as $k => $val) {
            $value['passwords_bind'][$k] = $val ? 1 : 0;
        }

        return $value;
    }

    /**
     * 设置扩展字段
     * @param string $key
     * @param mixed
     */
    public function setExt($key, $value) {
        $ext = $this->ext;
        array_set($ext, $key, $value);
        $this->ext = $ext;
        $this->save();
    }

    /**
     * 获取扩展字段
     * @param string $key
     * @param mixed
     */
    public function getExt($key) {
        $ext = $this->ext;
        return array_get($ext, $key);
    }

    /**
     * 获取用户组数据
     * @param  integer $value
     * @return array
     */
    public function getUserGroupAttribute($value) {
        $user_group = UserGroup::where('id', $value)->first();

        return $user_group;
    }

    public static function getUserByOpenid($openid) {
        $user_link = UserLink::where('type', 'wechat')->where('openid', $openid)->first();
        if(!$user_link) {
            return null;
        }

        return User::where('id', $user_link->uid)->first();
    }

}
