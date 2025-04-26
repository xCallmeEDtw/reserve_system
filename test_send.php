<?php
// 測試寄信用
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://localhost/demo/api/send_email.php');  // 注意這邊要是你的網址
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'to_email' => 'edward3a18@gmail.com',  // ❗換成你自己的收信信箱
    'to_name'  => '測試收件人',
    'subject'  => '這是一封測試信',
    'body'     => '如果你看到這封信，代表 SMTP 成功了！'
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

// 印出回應
if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}
?>
