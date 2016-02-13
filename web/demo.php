<?php

// DB情報
define('DB_HOST', 'localhost');
define('DB_NAME', 'fire');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// 文字化け対策
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8'");

// PHPのエラーを表示するように設定
error_reporting(E_ALL & ~E_NOTICE);

// DB接続
try {
    $dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD, $options);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}

// DBデータ取得
$sql = 'select * from fire_info order by score desc';
$stmt = $dbh->prepare($sql);
$stmt->execute($bind);
$data = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = array(
        'name'  =>  $row['name'],
        'image' =>  $row['image'],
        'fire'  =>  $row['score'],
        'level' =>  getFireLevel(intval($row['score'])),
        'date'  =>  $row['update_at']
    );
}

$json = json_encode($data);

header("Content-Type: text/javascript; charset=utf-8");
echo $json;

function getFireLevel($score) {
    if (0 <= $score && $score < 25) {
        return "0";
    } elseif (25 <= $score && $score < 50) {
        return "1";
    } elseif (50 <= $score && $score < 75) {
        return "2";
    } elseif (75 <= $score && $score <= 100) {
        return "3";
    }

    return "0";
}
