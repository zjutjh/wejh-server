<?php

namespace App\Http\Controllers\UserInformation;

use Illuminate\Support\Facades\DB;
use JWTAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class updateUser extends Controller
{
    public function updateUser(Request $request) {
        $name = $request->name; // 姓名
        $zone = $request->zone; // 校区信息
        $enterTime = $request->enterTime; // 入学时间
        $graduateTime = $request->graduateTime; // 毕业时间 
        $id = (JWTAuth::parseToken()->authenticate())['uno']; // 获取学号信息

        // 修改数据
        $affected = DB::update('update user_information 
                                set name = ?, zone = ?, enterTime = ?, graduateTime = ?
                                where id = ?', 
                            [$name, $zone, $enterTime, $graduateTime, $id]);
        if ($affected === 1) 
            return RJM(null, 1, 'ok');
        else 
            return RJM(null, -1, 'failed'); // 用户没有修改信息或者 token 出错
    }
}
