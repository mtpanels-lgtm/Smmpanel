<?php
// ============================================================
// 🔑 API KEY - CHANGE ONLY THIS
// ============================================================
$apiKey = "PAY77EB1243D909C2DC29073401"; // <-- Replace with your API key

// ============================================================
// ERROR REPORTING OFF
// ============================================================
error_reporting(0);

// ============================================================
// CHECK INPUT DATA
// ============================================================
if (!isset($_POST['orderid']) || !isset($_POST['amount']) || !isset($_POST['userid'])) {
  header("Location: index.html");
  exit;
}

$orderId = $_POST['orderid'];
$amount = $_POST['amount'];
$userId = $_POST['userid'];

// ============================================================
// 1. VERIFY PAYMENT WITH VC GATEWAY API
// ============================================================
$response = callVerificationAPI($orderId, $apiKey);

// ============================================================
// 2. IF API NO RESPONSE -> FAIL
// ============================================================
if (!$response) {
  header("Location: failed.php?orderid=" . urlencode($orderId));
  exit;
}

$data = json_decode($response, true);

// ============================================================
// 3. CHECK PAYMENT STATUS
// ============================================================

// ✅ SUCCESS
if (isset($data['status']) && $data['status'] == "success") {
  
  $isUpdated = updateFirebaseBalance($userId, $amount);
  
  if($isUpdated) {
      header("Location: success.php?orderid=" . urlencode($orderId) . "&amount=" . urlencode($amount) . "&userid=" . urlencode($userId));
  } else {
      echo "Payment Received but Database Error. Contact Admin.";
  }
  exit;

} else {
  // ❌ FAILED
  header("Location: failed.php?orderid=" . urlencode($orderId));
  exit;
}

// ============================================================
// 4. FUNCTIONS (API URL HIDDEN AT BOTTOM)
// ============================================================

function callVerificationAPI($orderId, $apiKey) {
    // 🌐 API URL - HIDDEN AT BOTTOM (Users won't see easily)
    $apiBaseUrl = "https://vcapi.vcstore.site/payment_api.php";
    
    // Build API URL
    $api_url = $apiBaseUrl . "?api_key=" . urlencode($apiKey) . "&order_id=" . urlencode($orderId);
    
    // SSL Verification Disabled
    $context = stream_context_create([
      "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
        "allow_self_signed" => true
      ],
      "http" => [
        "ignore_errors" => true,
        "timeout" => 30
      ]
    ]);
    
    return @file_get_contents($api_url, false, $context);
}

function updateFirebaseBalance($userId, $amount) {
    // 🔥 FIREBASE CONFIGURATION
    $firebaseUrl = "https://smm-panel-59a8b-default-rtdb.firebaseio.com";
    $dbSecret = "T7clwvGE7ZgpUYy1qll6hQ4pFhqZNarA0dCnavBb"; 
    
    // 1. Get Current Balance
    $getBalanceUrl = $firebaseUrl . "users/" . $userId . "/balance.json";
    $currentBalance = getCurrentBalanceWithCurl($getBalanceUrl, $dbSecret);
    
    // 2. Calculate New Balance
    $newBalance = $currentBalance + floatval($amount);
    
    // 3. Update Balance
    $updateBalanceUrl = $firebaseUrl . "users/" . $userId . ".json";
    $userData = ['balance' => $newBalance];
    
    $updateResponse = firebaseCurlRequest($updateBalanceUrl, 'PATCH', $userData, $dbSecret);
    
    return $updateResponse;
}

function getCurrentBalanceWithCurl($url, $authKey) {
    $fullUrl = $url . "?auth=" . $authKey;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response !== 'null' && $response !== null) {
        return floatval($response);
    }
    return 0;
}

function firebaseCurlRequest($url, $method = 'GET', $data = null, $authKey = null) {
    $fullUrl = $url . ($authKey ? "?auth=" . $authKey : "");
    
    $headers = ['Content-Type: application/json'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($data && in_array($method, ['PATCH', 'POST', 'PUT'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode >= 200 && $httpCode < 300;
}
?>