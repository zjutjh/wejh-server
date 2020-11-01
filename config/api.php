<?php
/**
 * User: cccRaim
 * Date: 2017/7/13
 * Time: 21:25
 */
$query = '?ip=163/jwglxt';

$prefix = [
    // 'api' => 'http://api.jh.zjut.edu.cn/student/',
    'api' => 'http://api.imcr.me:8080/student/',
    'ext' => 'http://craim.net/api/',
];

$jh = [
    'user' => [
        // 'api' => 'http://user.jh.zjut.edu.cn/api.php',
        'api' => 'http://user.zjut.com/api.php',
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
    'score' => 'scoresZf.php' . $query,
    'class' => 'classZf.php' . $query,
    'exam' => 'examQueryZf.php' . $query,
    'freeroom' => 'roomZf.php' . $query,
    'scoreDetail' => 'scoresDetailZf.php' . $query
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
    'isExt' => false,
    'prefix' => $prefix,
    'jh' => $jh,
    'zf' => $zf,
    'ycjw' => $ycjw,
    'library' => $library,
    'card' => $card
];