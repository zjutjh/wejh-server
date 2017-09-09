<?php
/**
 * Created by PhpStorm.
 * User: cccRaim
 * Date: 2017/7/3
 * Time: 00:11
 */
use App\Models\SystemSetting;

/**
 * 响应json数据
 * @param  mixed    $data
 * @param  integer  $err_code
 * @param  string   $err_msg
 * @param  string   $redirect_url
 * @return \Symfony\Component\HttpFoundation\Response
 */
function RJM($data, $err_code, $err_msg = '', $redirect_url = null)
{
    return response([
        'errcode' => $err_code,
        'errmsg' => $err_msg,
        'data' => $data,
        'redirect' => $redirect_url,
    ]);
}

/**
 * 获取对应的系统设置
 * @param  string   $varname
 * @return mixed
 */
function setting($varname)
{
    $val = \App\Models\SystemSetting::where('varname', $varname)->value('value');
    return $val;
}

/**
 * 使用CURL的POST请求资源
 * @param  string   $url        资源路径
 * @param  array    $post_data  请求参数
 * @param  int      $timeout    超时时间，毫秒级
 * @return mixed
 */
function http_post($url, $post_data = null, $timeout = 500){//curl
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_POST, 1);
    if($post_data){
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt ($ch, CURLOPT_TIMEOUT_MS, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}

/**
 * 使用CURL的GET请求资源
 * @param  string   $url        资源路径
 * @param  array    $post_data  请求参数
 * @param  int      $timeout    超时时间，毫秒级
 * @return mixed
 */
function http_get($url, $data, $timeout = 1000){//curl
    $ch = curl_init();
    if($data){
        if(strpos($url, '?') == false) {
            $url .= '?';
        } else {
            $url .= '&';
        }
        $url .= http_build_query($data);
    }
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt ($ch, CURLOPT_TIMEOUT_MS, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}

function api($key, $isExt)
{
    $configs = config('api');
    if ($isExt === null) {
        $isExt = $configs['isExt'];
    }
    $route = array_get($configs, $key);
    if(!$route) {
        return false;
    }
    if(is_array($route)) {
        if ($configs['compatible'] === true) {
            return $configs['compatibleURL'] . urlencode($route['api']);
        }
        return $isExt ? $route['ext'] : $route['api'];
    }
    $url = '';
    if($isExt) {
        $url = $configs['prefix']['ext'] . $route;
    } else {
        $url = $configs['prefix']['api'] . $route;
    }
    if ($configs['compatible'] === true) {
        $url = $configs['compatibleURL'] . urlencode($configs['prefix']['api'] . $route);
    }
    return $url;

}

function getCurrentTerm()
{
    $year = intval(date('Y'));
    $month = intval(date('m'));
    if($month <= 2) {
        $term = (($year - 1) . '/' . $year . '(1)');
    } else if ($month >= 6 && $month < 10) {
        $term = (($year - 1) . '/' . $year . '(2)');
    } else {
        $term = ($year . '/' . ($year + 1) . '(1)');
    }
    return $term;
}

function addYcjwPortError($port)
{
    return \App\Models\Log::addLog('YCJW_PORT_ERROR', $port, "原创教务服务器错误或无响应");
}

function resetCurrentYcjwPort()
{
    $log = new \App\Models\Log;
    $start_time = date('Y-m-d H:00:00', time());
    $min_log_count = count($log->getLogsByAction('YCJW_PORT_ERROR', 0, $start_time));
    $min_log_port = 0;
    for ($i = 83; $i < 87; $i++)
    {
        $log_count = count($log->getLogsByAction('YCJW_PORT_ERROR', $i, $start_time));
        if($min_log_count > $log_count)
        {
            $min_log_count = $log_count;
            $min_log_port = $i;
        }
    }
    (new SystemSetting)->setVars([
        'ycjw_port' => $min_log_port,
    ]);
    return $min_log_port;
}