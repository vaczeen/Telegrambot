<?php
function sendTelegramMessage($token, $chatId, $message) {
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $data = ['chat_id' => $chatId, 'text' => $message,'parse_mode' => 'HTML'];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type:application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data)
        ]
    ];
    $context  = stream_context_create($options);
    return file_get_contents($url, false, $context);
}