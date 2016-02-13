<?php
$url = "http://210.140.101.90/demo.php";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$json = curl_exec($ch);
curl_close($ch);
$data = json_decode($json,true);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />
    <title>Fire</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="name_wrap">
    <p class="talent-name"><?php echo $data[0]['name']; ?></p>
</div><!-- .wrap -->
</body>
</html>
