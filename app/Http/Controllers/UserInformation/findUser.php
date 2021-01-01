<?php

namespace App\Http\Controllers\UserInformation;

use Illuminate\Support\Facades\DB; // 调用数据库相关命名空间
use Illuminate\Http\Request;
use JWTAuth;
use App\Http\Controllers\Controller;

class findUser extends Controller
{
    public function findUser() {
        $id = (JWTAuth::parseToken()->authenticate())['uno']; // 获取学号信息
        $userInfo = DB::select('select * from user_information where id = ?', [$id]);
        return RJM($userInfo[0], 1, 'ok');
    }
}
