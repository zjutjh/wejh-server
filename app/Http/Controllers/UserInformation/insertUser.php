<?php

namespace App\Http\Controllers\UserInformation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class insertUser extends Controller
{
    public function insertUser()
    {
        $user = JWTAuth::getToken();
        echo $user;
    }
}
