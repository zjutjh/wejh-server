<?php
/**
 * User: cccRaim
 * Date: 2017/7/3
 * Time: 00:11
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $hidden = [
        'id',
        'updated_at',
        'created_at'
    ];

    /**
     * 应该被转化为原生类型的属性
     *
     * @var array
     */
    protected $casts = [
        'permission' => 'array',
    ];
}
