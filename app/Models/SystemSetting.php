<?php
/**
 * User: cccRaim
 * Date: 2017/7/3
 * Time: 00:11
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    /**
     * 应该被转化为原生类型的属性
     *
     * @var array
     */
    protected $casts = [
        'value' => 'array',
    ];

    protected $guarded = ['id'];

    /**
     * 获取所有系统设置
     * @return array
     */
    public function getSettings()
    {
        $settings = [];
        if ($system_setting = $this->get()) {
            foreach ($system_setting as $key => $val) {
                $settings[$val->varname] = $val->value;
            }
        }
        return $settings;
    }

    /**
     * 获取特定设置项
     * @param $varname
     * @return mixed
     */
    public function getSetting($varname)
    {
        $result = $this->select('value')->where('varname', $varname)->first();
        if(isset($result['value']))
            return $result['value'];
        else
            return false;
    }

    /**
     * 增加系统设置
     * @param  string   $varname
     * @param  mixed    $value
     * @return bool
     */
    public function addSetting($varname, $value)
    {
        if(!$this->getSetting($varname))
        {
            return $this->create([
                'varname' => $varname,
                'value' => $value,
            ]);
        }
        else
        {
            return $this->setVars([
                $varname => $value,
            ]);
        }
    }

    /**
     * 设置系统
     * @param  array    $vars
     * @return bool
     */
    public function setVars($vars)
    {
        if (!is_array($vars)) {
            return false;
        }
        foreach ($vars as $key => $val) {
            $this->where('varname', $key)->update(array(
                'value' => $val
            ));
        }
        return true;
    }
}
