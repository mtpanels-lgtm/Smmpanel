<?php 
// failed.php
$orderId = $_GET['orderid'] ?? 'N/A';

// Set Indian Time Zone
date_default_timezone_set('Asia/Kolkata');
$currentDate = date('d M, Y h:i A');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Payment Failed - VC Gateway</title>
    
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

        .failed-card {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            border-radius: 24px;
            padding: 32px 24px 28px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.06);
            text-align: center;
            position: relative;
            border: 1px solid #f0f2f5;
            transition: all 0.3s;
        }

        .failed-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 4px;
            background: linear-gradient(90deg, #dc2626, #ef4444);
            border-radius: 0 0 10px 10px;
        }

        .icon-wrapper {
            width: 90px;
            height: 90px;
            margin: 8px auto 16px;
            background: #fef2f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fecaca;
            position: relative;
        }
        .icon-wrapper i {
            font-size: 44px;
            color: #dc2626;
        }
        .icon-wrapper .pulse {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid rgba(220, 38, 38, 0.2);
            animation: pulseRing 1.8s ease-out infinite;
        }
        @keyframes pulseRing {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.3); opacity: 0; }
        }

        .failed-title {
            font-size: 22px;
            font-weight: 900;
            color: #1a1a2e;
            letter-spacing: -0.3px;
            margin-bottom: 8px;
        }

        .failed-sub {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 22px;
            padding: 0 4px;
        }
        .failed-sub i {
            color: #dc2626;
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
            background: #fef2f2;
            color: #dc2626;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #fecaca;
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

        .btn-support {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 30px;
            border: 1px solid #eef2f6;
            transition: all 0.25s;
            background: #fafafa;
        }
        .btn-support:hover {
            color: #1a1a2e;
            border-color: #1a1a2e;
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }
        .btn-support i {
            font-size: 14px;
        }

        .help-section {
            margin-top: 20px;
            padding-top: 18px;
            border-top: 1px solid #f0f2f5;
        }
        .help-text {
            font-size: 12px;
            color: #9ca3af;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .help-text i {
            color: #6b7280;
        }

        @media (max-width: 480px) {
            .failed-card {
                padding: 24px 16px 20px;
            }
            .icon-wrapper {
                width: 74px;
                height: 74px;
            }
            .icon-wrapper i {
                font-size: 36px;
            }
            .failed-title {
                font-size: 19px;
            }
            .failed-sub {
                font-size: 13px;
            }
            .d-label, .d-val {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

    <div class="failed-card">

        <div class="icon-wrapper">
            <span class="pulse"></span>
            <i class="fa-solid fa-circle-xmark"></i>
        </div>

        <h1 class="failed-title">Payment Failed</h1>
        <p class="failed-sub">
            <i class="fa-solid fa-triangle-exclamation"></i> 
            Your payment was not received. Please complete the payment and try again.
        </p>

        <div class="details-grid">
            <div class="d-row">
                <span class="d-label"><i class="fa-regular fa-receipt"></i> Transaction ID</span>
                <span class="d-val"><span class="order-id"><?= htmlspecialchars($orderId) ?></span></span>
            </div>
            <div class="d-row">
                <span class="d-label"><i class="fa-regular fa-clock"></i> Date & Time</span>
                <span class="d-val"><?= $currentDate ?></span>
            </div>
            <div class="d-row">
                <span class="d-label"><i class="fa-regular fa-circle"></i> Status</span>
                <span class="d-val"><span class="status-badge"><i class="fa-solid fa-xmark"></i> FAILED</span></span>
            </div>
        </div>

        <a href="addfunds.html" class="btn-action">
            <i class="fa-solid fa-rotate-right"></i> TRY AGAIN
        </a>

        <a href="https://t.me/VishalCodeVerseOwner" target="_blank" class="btn-support">
            <i class="fa-regular fa-circle-question"></i> Need Help? Contact Support
        </a>

        <div class="help-section">
            <span class="help-text">
                <i class="fa-regular fa-lightbulb"></i>
                Still having issues? Our support team is here to help.
            </span>
        </div>

    </div>

</body>
</html>