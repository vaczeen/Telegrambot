<?php
$config = include __DIR__ . '/config/config.php';
$conn = include __DIR__ . '/database/connection.php';
require_once __DIR__ . '/lib/telegram.php';
require_once __DIR__ . '/lib/battery_alert.php';

checkBatteryAndNotify($conn, $config['telegram_token'], $config['telegram_chat_id']);