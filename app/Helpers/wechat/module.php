<?php

function wechatModule($module_name, $message) {
    $file = app_path('WechatModule/' . $module_name . '.php');
    if (!file_exists($file)) {
        return array();
    }
    return include $file;
}

function respText($message) {
    $header = '';
    $footer = "\n
如有问题，请<a href='https://jq.qq.com/?_wv=1027&k=41l0WTP'>点击反馈</a>
\n
\n
小提示：点击右上角然后“置顶公众号”，带来更方便的体验";
    $message = $header . $message . $footer;
    return $message;
}