<?php

namespace App\Http\Controllers\Ycjw;

use Illuminate\Support\Facades\Auth;
use App\Models\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function bind(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $password = $request->get('password');
        list($check, $errmsg) = $this->getCheck($user->uno, $password, setting('ycjw_port'), true);
        if($check == false) {
            return RJM(null, -1, $errmsg);
        }
        $user->setExt('passwords.yc_password', encrypt($password));

        return RJM($user, 1, '绑定原创账号成功');
    }

    public function bindZf(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $password = $request->get('password');
        $api = new Api();
        $check = $api->getUEASData('score', $user->uno, [
            'zf' => $password
        ], '2017/2018(1)', null, true);
        if($check == false) {
            $error = $api->getError();
            if ($error === '用户名或密码错误') {
                $error = "用户名或密码错误\nps:正方密码是你选课的密码";
            }
            return RJM(null, -1, $error);
        }
        $user->setExt('passwords.zf_password', encrypt($password));

        return RJM($user, 1, '绑定正方账号成功');
    }

    /**
     * 循环获取
     * @param $username
     * @param $password
     * @param null $port
     * @param bool $retry
     * @param int $timeout
     * @return array
     */
    public function getCheck($username, $password, $port = null, $retry = false, $timeout = 800) {
        $api = new Api;
        $check = $api->checkYcLogin($username, $password, $port, $timeout);
        $firstError = $api->getError();
        $api->resetError();
        if($firstError === '原创服务器错误') {
            addYcjwPortError($port);
        }
        if(!$check && !$retry) {
            resetCurrentYcjwPort();
            if($firstError == '原创服务器错误') {
                return [false, '原创教务系统炸了'];
            }
            return [false, $api->getError()];
        }
        $error = '';
        if(!$check && $retry) {
            for ($i = 83; $i <= 86; $i++) {
                $check = $api->checkYcLogin($username, $password, $i, $timeout);
                $error = $api->getError();
                if($check) {
                    break;
                } else {
                    $api->resetError();
                    if($error === '原创服务器错误') {
                        addYcjwPortError($i);
                    }
                }
            }
            resetCurrentYcjwPort();
            if(!$check) {
                return [false, $error];
            }
        }
        return [$check, $error];
    }

    public function view(Request $request)
    {
        $url = $request->get('url');
        $params = $request->except('url');
        $urlObj = parse_url($url);
        $url = $urlObj['scheme'] . '://' . $urlObj['host'] . (isset($urlObj['path']) ? $urlObj['path'] : '');
        if (isset($urlObj['query'])) {
            $queryStr = explode('&', $urlObj['query']);
            $query = [];
            foreach ($queryStr as $key => $value) {
                $singleQuery = explode('=', $value);
                $query[$singleQuery[0]] = $singleQuery[1];
            }
            $params = array_merge($params, $query);
        }
        @$content = http_get($url . '?' . http_build_query($params));
        if (!preg_match('/www.zjut.edu.cn/', $url)) {
            return redirect($url . '?' . http_build_query($params));
        }
        $content = $content ? $content : '';
        $content = iconv('GBK//IGNORE', 'UTF-8//IGNORE', $content);
        $content = preg_replace('/"([^".]+).gif"/is', '"' . 'http://www.zjut.edu.cn/' . ('$1') . '.gif"', $content);
        $content = preg_replace('/"([^".]+).jpg"/is', '"' . 'http://www.zjut.edu.cn/' . ('$1') . '.jpg"', $content);
        $content = preg_replace('/\'([^".]+).jpg\'/is', '\'' . 'http://www.zjut.edu.cn/' . ('$1') . '.jpg\'', $content);
        $content = preg_replace('/"([^".]+).png"/is', '"' . 'http://www.zjut.edu.cn/' . ('$1') . '.png"', $content);
        // $content = preg_replace('/href="([^".]+).css"/is', 'href="' . 'http://www.zjut.edu.cn/' . ('$1') . '.css"', $content);
        // $content = preg_replace('/"([^"]+).js"/is', '"' . 'http://www.zjut.edu.cn/' . ('$1') . '.js"', $content);
        $content = preg_replace('/"([^"\/\/]+).jsp([^"]+)"/is', '"' . 'http://www.zjut.edu.cn/' . ('$1') . '.jsp$2"', $content);
        $content = preg_replace('/\'([^"\/\/]+).jsp([^"]+)\'/is', '"' . 'http://www.zjut.edu.cn/' . ('$1') . '.jsp$2"', $content);
        $content = preg_replace('/<a href="([^"]+)\/\/([^"]+)"/is', '<a href="' . url('zjut/view') . '?url=' . ('$1') . '//$2"', $content);
        $content = preg_replace('/none;\' href="([^"]+)\/\/([^"]+)" target="_blank"/is', 'none;\' href="' . url('zjut/view') . '?url=' . ('$1') . '//$2" target="_blank"', $content);
        $content = preg_replace('/\(\.\.\/([^\)]+).png\)/is', '(' . 'http://www.zjut.edu.cn/' . ('$1') . '.png)', $content);

        return $content;
    }

    public function image($path, $file)
    {
        // $url = $request->get('url');
        if (preg_match('/gif$/', $file)) {
            $type = 'image/gif';
        } else if (preg_match('/png/', $file)) {
            $type = 'image/png';
        } else {
            $type = 'image/jpeg';
        }
        @$content = file_get_contents('http://www.zjut.edu.cn/image/' . $path . '/' . $file);
        return response($content)->header('Content-Type', $type);
    }

    public function js($file)
    {
        // $url = $request->get('url');
        @$content = file_get_contents('http://www.zjut.edu.cn/js/' . '/' . $file);
        return response($content)->header('Content-Type', 'application/x-javascript');
    }

    public function css($file)
    {
        // $url = $request->get('url');
        @$content = file_get_contents('http://www.zjut.edu.cn/css/' . '/' . $file);
        // $content = iconv('GBK//IGNORE', 'UTF-8', $content);
        $content = preg_replace('/\(\.\.\/([^\)]+).jpg\)/is', '(' . 'http://www.zjut.edu.cn/' . ('$1') . '.jpg)', $content ? $content : '');
        return response($content)->header('Content-Type', 'text/css');
    }
}
