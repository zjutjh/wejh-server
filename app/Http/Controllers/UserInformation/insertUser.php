<?php

namespace App\Http\Controllers\UserInformation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class insertUser extends Controller
{
    public function insertUser(Request $request)
    {
        $name = $request->name; // 姓名
        $zone = $request->zone; // 校区信息
        $enterTime = $request->enterTime; // 入学时间
        $graduateTime = $request->graduateTime; // 毕业时间 
        $id = (JWTAuth::parseToken()->authenticate())['uno']; // 获取学号信息
        
        
    }
}
