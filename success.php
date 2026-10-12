<?php
// --- 1. DATA COLLECTION ---
$orderId = $_REQUEST['orderid'] ?? 'N/A';
$amount = $_REQUEST['amount'] ?? '0.00';
$userId = $_REQUEST['userid'] ?? 'N/A';
$userEmail = $_REQUEST['email'] ?? 'Not Provided';

// Set Indian Time Zone
date_default_timezone_set('Asia/Kolkata');
$currentDate = date('d M, Y h:i A');

// --- LOG FILE ---
$logFile = 'telegram_log.txt';

// --- 2. TELEGRAM NOTIFICATION SYSTEM ---
if ($amount > 0) {
    
    $botToken = "8804203869:AAHt3ZzBcTbEzgAa05THrl9UmVR_HT0sgio";
    $chatId = "8907916745";

    // --- UPGRADED MESSAGE DESIGN ---
    $message = "💎 *PAYMENT SUCCESSFUL* 💎\n\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "💰 *Amount:* ₹" . number_format($amount, 2) . "\n";
    $message .= "🆔 *Transaction ID:* `" . $orderId . "`\n";
    $message .= "👤 *User ID:* `" . $userId . "`\n";
    if($userEmail != 'Not Provided') {
        $message .= "📧 *Email:* " . $userEmail . "\n";
    }
    $message .= "📅 *Date:* " . $currentDate . "\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "✅ *Status:* SUCCESSFUL\n";
    $message .= "💵 *Payment Received & Added to Wallet*\n";
    $message .= "━━━━━━━━━━━━━━━━━━━━\n";
    $message .= "🎯 *VC Gateway* | India's Best Payment Gateway";

    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];

    // --- SMART SENDER FUNCTION ---
    $endpoints = [
        "https://api.telegram.org/bot$botToken/sendMessage",
        "https://149.154.167.220/bot$botToken/sendMessage",
        "https://91.108.56.113/bot$botToken/sendMessage"
    ];

    $sent = false;
    $responseLog = [];

    foreach ($endpoints as $index => $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Host: api.telegram.org"]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // --- LOG EACH ATTEMPT ---
        $logEntry = "[" . date('Y-m-d H:i:s') . "] ";
        $logEntry .= "Attempt " . ($index + 1) . " | ";
        $logEntry .= "URL: " . parse_url($url, PHP_URL_HOST) . " | ";
        $logEntry .= "HTTP Code: $httpCode | ";
        
        if ($httpCode == 200 && $result) {
            $response = json_decode($result, true);
            if (isset($response['ok']) && $response['ok'] === true) {
                $logEntry .= "✅ SUCCESS! Message sent.\n";
                $sent = true;
                $responseLog[] = $logEntry;
                break;
            } else {
                $logEntry .= "❌ API Error: " . ($response['description'] ?? 'Unknown error') . "\n";
                $responseLog[] = $logEntry;
            }
        } else {
            $logEntry .= "❌ FAILED | ";
            if ($curlError) {
                $logEntry .= "CURL Error: $curlError\n";
            } else {
                $logEntry .= "Response: " . substr($result, 0, 200) . "\n";
            }
            $responseLog[] = $logEntry;
        }
    }

    // --- FINAL LOG ---
    if ($sent) {
        $log = "[" . date('Y-m-d H:i:s') . "] ✅ SUCCESS: Payment notification sent to Telegram!\n";
        $log .= "Order ID: $orderId | Amount: ₹$amount | User: $userId\n";
        $log .= "------------------------------------------------\n";
    } else {
        $log = "[" . date('Y-m-d H:i:s') . "] ❌ FAILED: Could not send Telegram notification!\n";
        $log .= "Order ID: $orderId | Amount: ₹$amount | User: $userId\n";
        $log .= "All attempts failed.\n";
        $log .= "------------------------------------------------\n";
    }
    
    // Write to log file
    file_put_contents($logFile, $log, FILE_APPEND);
    
    // Also write detailed attempts
    if (!empty($responseLog)) {
        file_put_contents($logFile, implode("", $responseLog), FILE_APPEND);
        file_put_contents($logFile, "------------------------------------------------\n", FILE_APPEND);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Payment Success - VC Gateway</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background: #f5f7fb;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .success-card {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            border-radius: 24px;
            padding: 32px 24px 28px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.06);
            text-align: center;
            position: relative;
            border: 1px solid #f0f2f5;
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #34d399);
            border-radius: 0 0 10px 10px;
        }

        .icon-wrapper {
            width: 90px;
            height: 90px;
            margin: 8px auto 16px;
            background: #ecfdf5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #a7f3d0;
            position: relative;
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .icon-wrapper i {
            font-size: 44px;
            color: #10b981;
        }
        .icon-wrapper .confetti {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid rgba(16, 185, 129, 0.15);
            animation: confettiRing 2s ease-out infinite;
        }
        @keyframes confettiRing {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        .dot {
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #34d399;
            animation: floatDot 2s ease-out infinite;
        }
        .dot:nth-child(2) { top: -5px; left: 20%; animation-delay: 0.2s; background: #f59e0b; }
        .dot:nth-child(3) { top: -5px; right: 20%; animation-delay: 0.4s; background: #8b5cf6; }
        .dot:nth-child(4) { bottom: -5px; left: 15%; animation-delay: 0.6s; background: #ec4899; }
        .dot:nth-child(5) { bottom: -5px; right: 15%; animation-delay: 0.8s; background: #3b82f6; }
        @keyframes floatDot {
            0% { transform: translateY(0) scale(1); opacity: 1; }
            100% { transform: translateY(-20px) scale(0); opacity: 0; }
        }

        .success-title {
            font-size: 24px;
            font-weight: 900;
            color: #1a1a2e;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }

        .success-sub {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 18px;
        }
        .success-sub i {
            color: #10b981;
            margin-right: 4px;
        }

        .amount-box {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            padding: 16px;
            border-radius: 14px;
            margin-bottom: 20px;
            border: 1px solid #a7f3d0;
        }
        .amount-label {
            font-size: 11px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .amount-val {
            font-size: 32px;
            font-weight: 900;
            color: #10b981;
            letter-spacing: -0.5px;
        }
        .amount-val i {
            font-size: 20px;
            margin-right: 4px;
        }

        .details-grid {
            width: 100%;
            background: #f8fafc;
            border-radius: 14px;
            padding: 16px 18px;
            border: 1px solid #eef2f6;
            margin-bottom: 24px;
            text-align: left;
        }
        .d-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eef2f6;
        }
        .d-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .d-label {
            font-size: 13px;
            font-weight: 600;
            color: #9ca3af;
        }
        .d-label i {
            width: 18px;
            margin-right: 6px;
            color: #9ca3af;
        }
        .d-val {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
        }
        .d-val .order-id {
            font-family: 'Courier New', monospace;
            color: #2563eb;
            font-size: 12px;
            background: #eff6ff;
            padding: 2px 10px;
            border-radius: 6px;
        }
        .status-badge {
            background: #ecfdf5;
            color: #10b981;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #a7f3d0;
        }
        .status-badge i {
            margin-right: 4px;
            font-size: 10px;
        }

        .btn-action {
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            border: none;
            background: #1a1a2e;
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.25s;
            box-shadow: 0 4px 16px rgba(26, 26, 46, 0.15);
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(26, 26, 46, 0.25);
        }
        .btn-action:active {
            transform: scale(0.97);
        }
        .btn-action i {
            font-size: 14px;
        }

        .wallet-msg {
            margin-top: 16px;
            padding: 12px;
            background: #f0fdf4;
            border-radius: 10px;
            border: 1px solid #bbf7d0;
            font-size: 13px;
            color: #15803d;
            font-weight: 600;
        }
        .wallet-msg i {
            margin-right: 6px;
        }

        @media (max-width: 480px) {
            .success-card {
                padding: 24px 16px 20px;
            }
            .icon-wrapper {
                width: 74px;
                height: 74px;
            }
            .icon-wrapper i {
                font-size: 36px;
            }
            .success-title {
                font-size: 20px;
            }
            .amount-val {
                font-size: 26px;
            }
            .d-label, .d-val {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

    <div class="success-card">

        <div class="icon-wrapper">
            <span class="confetti"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <i class="fa-regular fa-circle-check"></i>
        </div>

        <h1 class="success-title">Payment Success! 🎉</h1>
        <p class="success-sub">
            <i class="fa-regular fa-circle-check"></i> 
            Your payment has been received successfully.
        </p>

        <div class="amount-box">
            <div class="amount-label">Amount Added</div>
            <div class="amount-val"><i class="fa-solid fa-indian-rupee-sign"></i> <?= number_format($amount, 2) ?></div>
        </div>

        <div class="details-grid">
            <div class="d-row">
                <span class="d-label"><i class="fa-solid fa-id-card""></i> Transaction ID</span>
                <span class="d-val"><span class="order-id"><?= htmlspecialchars($orderId) ?></span></span>
            </div>
            <div class="d-row">
                <span class="d-label"><i class="fa-regular fa-clock"></i> Date & Time</span>
                <span class="d-val"><?= $currentDate ?></span>
            </div>
            <div class="d-row">
                <span class="d-label"><i class="fa-regular fa-circle"></i> Status</span>
                <span class="d-val"><span class="status-badge"><i class="fa-regular fa-circle-check"></i> COMPLETED</span></span>
            </div>
        </div>

        <div class="wallet-msg">
            <i class="fa-regular fa-circle-check"></i>
            ₹<?= number_format($amount, 2) ?> has been added to your wallet successfully!
        </div>

        <a href="index.html" class="btn-action" style="margin-top: 16px;">
            <i class="fa-solid fa-arrow-right"></i> GO TO DASHBOARD
        </a>

    </div>

</body>
</html>