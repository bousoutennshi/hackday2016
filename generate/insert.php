<?php

// 日付データ取得
$date = date("Y-m-d", time());

// 炎上リスト読み込み
$file = '/tmp/people.json';
$people_list = json_decode(file_get_contents($file),true);

// Twitterデータからスコア値を取得
$fire_info = array();
foreach ($people_list as $people) {
    if (empty($people)) {
        continue;
    }

    // パラメータ設定
    $params = array(
        'q' => '炎上 '.$people['name'],                // 検索キーワード (必須)
    #    'geocode' => '35.794507,139.790788,1km' ,  // 範囲指定
    #    'lang' => 'ja' ,                       // 対象地域(言語コードで指定)
    #    'locale' => 'ja' ,                     // 検索クエリの言語コード
    #    'result_type' => 'popular' ,           // 検索クエリの言語コード
        'count' => '100',                       // 取得件数
        'until' => $date,                       // 最新日時
    #    'since_id' => '598534160928509952' ,   // 最古のツイートID
    #    'max_id' => '599056298085224449' ,     // 最新のツイートID
    #    'include_entities' => 'true' ,         // ツイートオブジェクトのエンティティを含める
    #    'callback' => 'syncerAction' ,         // コールバック関数名
    );
    $twitter = getTwitterData($params);
    $score = getScoreData($twitter);
    if (!$score) {
        $score = 0;
    }
    $fire_info[] = array(
        'name'  =>  $people['name'],
        'image' =>  $people['image'],
        'score' =>  $score
    );
}

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

// DBインサート
foreach ($fire_info as $fire) {
    $sql = 'replace into fire_info (name, image, score, update_at) values (:name, :image, :score, now())';
    $stmt = $dbh->prepare($sql);
    $bind = array(
        ':name'     =>  $fire['name'],
        ':image'    =>  $fire['image'],
        ':score'    =>  $fire['score']
    );
    $stmt->execute($bind);
}

function getTwitterData($params_a) {
    // 設定
    $api_key = '5zQIvn5ClD0cOic8pFnQg';     // APIキー
    $api_secret = 'YvFscMS8gRR62vytecYuNr5OaAYiMYB9BAue28sn3Cs';      // APIシークレット
    $access_token = '83531350-kNESrF5YvWMtWkudUdCf2LIqLAJAHLXd3k0fI5Jr3';        // アクセストークン
    $access_token_secret = 'jybvWsgukJ8LtgoWFRIao2daFdb5DcwS8bHNAr1OF4RU5' ;     // アクセストークンシークレット
    $request_url = 'https://api.twitter.com/1.1/search/tweets.json' ;       // エンドポイント
    $request_method = 'GET' ;

    // キーを作成する (URLエンコードする)
    $signature_key = rawurlencode( $api_secret ) . '&' . rawurlencode( $access_token_secret );

    // パラメータB (署名の材料用)
    $params_b = array(
        'oauth_token' => $access_token ,
        'oauth_consumer_key' => $api_key ,
        'oauth_signature_method' => 'HMAC-SHA1' ,
        'oauth_timestamp' => time() ,
        'oauth_nonce' => microtime() ,
        'oauth_version' => '1.0' ,
    );

    // パラメータAとパラメータBを合成してパラメータCを作る
    $params_c = array_merge($params_a, $params_b);
    ksort($params_c);

    // リクエスト加工
    $request_params = http_build_query($params_c,'','&');
    $request_params = str_replace( array( '+' , '%7E' ) , array( '%20' , '~' ),$request_params);
    $request_params = rawurlencode( $request_params ) ;
    $encoded_request_method = rawurlencode( $request_method ) ;
    $encoded_request_url = rawurlencode( $request_url ) ;
    $signature_data = $encoded_request_method . '&' . $encoded_request_url . '&' . $request_params ;
    $hash = hash_hmac( 'sha1' , $signature_data , $signature_key , TRUE ) ;
    $signature = base64_encode( $hash ) ;
    $params_c['oauth_signature'] = $signature ;
    $header_params = http_build_query( $params_c , '' , ',' ) ;
    $context = array(
        'http' => array(
            'method' => $request_method , // リクエストメソッド
            'header' => array(            // ヘッダー
                'Authorization: OAuth ' . $header_params ,
            ) ,
        ) ,
    );
    if ($params_a) {
        $request_url .= '?' . http_build_query( $params_a ) ;
    }

    $curl = curl_init() ;
    curl_setopt( $curl , CURLOPT_URL , $request_url ) ;
    curl_setopt( $curl , CURLOPT_HEADER, 1 ) ;
    curl_setopt( $curl , CURLOPT_CUSTOMREQUEST , $context['http']['method'] ) ;         // メソッド
    curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false ) ;                             // 証明書の検証を行わない
    curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ) ;                              // curl_execの結果を文字列で返す
    curl_setopt( $curl , CURLOPT_HTTPHEADER , $context['http']['header'] ) ;            // ヘッダー
    curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;                                        // タイムアウトの秒数
    $res1 = curl_exec( $curl ) ;
    $res2 = curl_getinfo( $curl ) ;
    curl_close( $curl ) ;

    // 取得したデータ
    $json = substr( $res1, $res2['header_size'] ) ;             // 取得したデータ(JSONなど)
    $header = substr( $res1, 0, $res2['header_size'] ) ;        // レスポンスヘッダー (検証に利用したい場合にどうぞ)

    // JSONをオブジェクトに変換
    $obj = json_decode($json, true);

    return $obj;
}

function getScoreData($twitter) {
    $score = 0;
    $term = strtotime('-2 day', time());

    if (!isset($twitter['statuses'])) {
        var_dump($twitter);
        return false;
    }

    foreach ($twitter['statuses'] as $data) {
        $current = strtotime($data['created_at']);
        if ($current > $term) {
            $score++;
        }
    }

    return $score;
}
