<?php
$openid = $message->FromUserName;

$app = app('wechat');
$userService = $app->user;
$user = $userService->get($openid);

return json_encode($user);