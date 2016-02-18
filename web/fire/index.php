<?php

// 基本0
$number = 0;

$url = "http://210.140.101.90/demo.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$json = curl_exec($ch);
curl_close($ch);
$data = json_decode($json,true);

switch ($data[$number]['level']) {
    case "0":
        $level = '炎上レベル 0';
        $class = 'fire1';
        break;
    case "1":
        $level = '炎上レベル 1';
        $class = 'fire2';
        break;
    case "2":
        $level = '炎上レベル 2';
        $class = 'fire3';
        break;
    case "3":
        $level = '炎上レベル MAX';
        $class = 'fire4';
        break;
    default:
        $level = '炎上レベル 0';
        $class = 'fire1';
        break;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
    <title>Fire</title>
    <link rel="stylesheet" href="style.css" />
    <SCRIPT LANGUAGE="JavaScript">
    <!--
        setTimeout("location.reload()",60000);
    //-->
    </SCRIPT>
</head>
<body>
    <div class="wrap">
    <section class="screen">
        <p class="<?php echo $class; ?>"><img src="<?php echo $data[$number]['image']; ?>" width="100%"  alt=""></p>
        <p class="level"><?php echo $level; ?></p>
        <p class="name"><?php echo $data[$number]['name']; ?></p>
    </section>
    <section class="sidebar">
        <div>
            <a class="twitter-timeline"  href="https://twitter.com/search?q=%E3%83%99%E3%83%83%E3%82%AD%E3%83%BC%20%E7%82%8E%E4%B8%8A" data-widget-id="698472364502167552">ベッキー 炎上に関するツイート</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        </div>
    </section>
</div><!-- .wrap -->
</body>
</html>
