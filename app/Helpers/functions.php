<?php
/**
 * Created by PhpStorm.
 * User: cccRaim
 * Date: 2017/7/3
 * Time: 00:11
 */

if (!function_exists('RSM')) {
    function RSM($data, $err_code, $err_msg = '', $redirect_url = null)
    {
        return response([
            'errcode' => $err_code,
            'errmsg' => $err_msg,
            'data' => $data,
            'redirect' => $redirect_url,
        ]);
    }
}
if (!function_exists('setting')) {
    function setting($varname)
    {
        $val = \App\Models\SystemSetting::where('varname', $varname)->value('value');
        return $val;
    }
}

if (!function_exists('http_post')) {
    function http_post($url, $post_data = '', $timeout = 5){//curl
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_POST, 1);
        if($post_data != ''){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        return $file_contents;
    }
}

if (!function_exists('http_get')) {
    function http_get($url, $post_data = '', $timeout = 5){//curl
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        if($post_data != ''){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        return $file_contents;
    }
}

if (!function_exists('getCurrentTerm')) {
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
}