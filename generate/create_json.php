<?php
$data = array(
    array(
        'name'  =>  'ベッキー',
        'image' =>  'img/becky.png'
    ),
    array(
        'name'  =>  '篠崎愛',
        'image' =>  'img/shinozaki.png'
    ),
    array(
        'name'  =>  '広瀬すず',
        'image' =>  'img/hirose.png'
    ),
    array(
        'name'  =>  '狩野英孝',
        'image' =>  'img/kano.png'
    ),
    array(
        'name'  =>  '矢口真里',
        'image' =>  'img/yaguchi.png'
    ),
    array(
        'name'  =>  '小保方',
        'image' =>  'img/obokata.png'
    ),
    array(
        'name'  =>  '佐村河内',
        'image' =>  'img/samura.png'
    ),
    array(
        'name'  =>  '宮崎議員',
        'image' =>  'img/miyazaki.png'
    ),
    array(
        'name'  =>  'おのののか',
        'image' =>  'img/ono.png'
    ),
    array(
        'name'  =>  '椎木里佳',
        'image' =>  'img/shiki.png'
    ),
    array(
        'name'  =>  '川谷',
        'image' =>  'img/kawatani.png'
    ),
    array(
        'name'  =>  '清原',
        'image' =>  'img/kiyo2.png'
    )
);

$json = json_encode($data);
$ret = file_put_contents('/tmp/people.json', $json);
var_dump($ret);
