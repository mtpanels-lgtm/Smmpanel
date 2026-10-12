<?php
error_reporting(0);
header('Content-Type: application/json');

$API_URL = 'https://bigpanel.in/api/v2'; // <--- CHANGE THIS
$API_KEY = '384eb36991cc80c0a66194298fdac38a'; // <--- CHANGE THIS

$postData = [ 'key'=>$API_KEY, 'action'=>'status', 'order'=>$_POST['order_id'] ];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $API_URL);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$res = json_decode(curl_exec($ch), true);
curl_close($ch);

if(isset($res['status'])) echo json_encode(['status'=>$res['status']]);
else echo json_encode(['status'=>false]);
?>