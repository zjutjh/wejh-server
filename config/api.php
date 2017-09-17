<?php
/**
 * User: cccRaim
 * Date: 2017/7/13
 * Time: 21:25
 */
$prefix = [
    'api' => 'http://api.jh.zjut.edu.cn/student/',
    'ext' => 'http://craim.net/api/',
];

$jh = [
    'user' => [
        'api' => 'http://user.jh.zjut.edu.cn/api.php',
        'ext' => 'http://craim.net/api/api.php',
    ],
];

$ycjw = [
    'score' => 'scores.php',
    'class' => 'class.php',
    'exam' => 'examQuery.php',
    'freeroom' => 'room.php',
];

$zf = [
    'score' => 'scoresZf.php',
    'class' => 'classZf.php',
    'exam' => 'examQueryZf.php',
    'freeroom' => 'roomZf.php',
];

$library = [
    'search' => 'library_search.php',
    'book' => 'library_book.php',
    'borrow' => 'library_borrow.php',
];

$card = [
    'balance' => 'cardRecords.php'
];

return [
    'compatibleURL' => 'http://bbs.zjut.edu.cn/api/jhapi.php?url=',
    'compatible' => true,
    'isExt' => true,
    'prefix' => $prefix,
    'jh' => $jh,
    'zf' => $zf,
    'ycjw' => $ycjw,
    'library' => $library,
    'card' => $card
];