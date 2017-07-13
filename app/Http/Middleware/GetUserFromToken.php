<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class GetUserFromToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return RJM(null, -404, '找不到用户');
            }
        } catch (TokenExpiredException $e) {
            return RJM(null, -401, '登录已过期');
        } catch (TokenInvalidException $e) {
            return RJM(null, -403, '登录凭证不合法');
        } catch (JWTException $e) {
            return RJM(null, -402, '缺少登录凭证');
        }

        Auth::login($user);

        return $next($request);
    }
}
