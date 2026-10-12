<?php

ob_start();
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

$API_URL = 'https://bigpanel.in/api/v2';
$API_KEY = '384eb36991cc80c0a66194298fdac38a';


/*
|--------------------------------------------------------------------------
| JSON RESPONSE
|--------------------------------------------------------------------------
*/
function sendResponse($status, $message = '', $order_id = null)
{
    ob_clean();

    $response = [
        'status' => $status
    ];

    if ($message !== '') {
        $response['message'] = $message;
    }

    if ($order_id !== null) {
        $response['order_id'] = $order_id;
    }

    echo json_encode($response, JSON_UNESCAPED_SLASHES);
    exit;
}


/*
|--------------------------------------------------------------------------
| CHECK INPUT
|--------------------------------------------------------------------------
*/
$service  = trim($_POST['service'] ?? '');
$link     = trim($_POST['link'] ?? '');
$quantity = trim($_POST['quantity'] ?? '');

if ($service === '' || $link === '' || $quantity === '') {
    sendResponse('fail', 'Missing order details');
}

if (!is_numeric($quantity) || (int)$quantity < 1) {
    sendResponse('fail', 'Invalid quantity');
}

$quantity = (int)$quantity;


/*
|--------------------------------------------------------------------------
| PROVIDER REQUEST
|--------------------------------------------------------------------------
*/
$postData = [
    'key'      => $API_KEY,
    'action'   => 'add',
    'service'  => $service,
    'link'     => $link,
    'quantity' => $quantity
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $API_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

/*
 * IMPORTANT:
 * Termux certificate error ko avoid karne ke liye
 * SSL verification disabled rakha gaya hai.
 */
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);

$curlError = curl_error($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);


/*
|--------------------------------------------------------------------------
| CONNECTION ERROR
|--------------------------------------------------------------------------
*/
if ($response === false || $response === '') {

    sendResponse(
        'fail',
        'Provider connection failed' .
        ($curlError ? ': ' . $curlError : '')
    );
}


/*
|--------------------------------------------------------------------------
| API RESPONSE
|--------------------------------------------------------------------------
*/
$res = json_decode($response, true);

if (!is_array($res)) {
    sendResponse(
        'fail',
        'Invalid response from provider'
    );
}


/*
|--------------------------------------------------------------------------
| ORDER SUCCESS
|--------------------------------------------------------------------------
*/
if (isset($res['order']) && $res['order'] !== '') {

    sendResponse(
        'success',
        'Order placed successfully',
        $res['order']
    );
}


/*
|--------------------------------------------------------------------------
| API ERROR
|--------------------------------------------------------------------------
*/
$error = '';

if (isset($res['error'])) {
    $error = $res['error'];
} elseif (isset($res['message'])) {
    $error = $res['message'];
}

if ($error === '') {
    $error = 'Provider API error';
}

sendResponse('fail', $error);

?>