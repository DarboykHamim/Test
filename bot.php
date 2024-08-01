<?php
$botToken = "7466048504:AAF6FePrb-n1rSvklI8fgixoKgEmK0SpC5I";
$apiUrl = "https://api.telegram.org/bot" . $botToken;


$update = file_get_contents('php://input');
$updateArray = json_decode($update, TRUE);


$myEmoji = ["⚡"];
$doEmoji = $myEmoji[array_rand($myEmoji)];


function sendMessage($chatId, $message) {
    global $apiUrl;
    $url = $apiUrl . "/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode'=>'Markdown'
    ];
    sendPostRequest($url, $data);
}


function setMessageReaction($chatId, $messageId, $reaction) {
    global $apiUrl;
    $url = $apiUrl . "/setMessageReaction";
    $data = [
        'chat_id' => $chatId,
        'message_id' => $messageId,
        'parse_mode'=>'MarkdownV2',
        'reaction' => json_encode([
            [
                'type' => 'emoji',
                'emoji' => $reaction,
                'is_big' => true
            ]
        ])
    ];
    sendPostRequest($url, $data);
}


function sendPostRequest($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $response;
}


function sendGetRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $response;
}

if (isset($updateArray['message'])) {
    $chatId = $updateArray['message']['chat']['id'];
    $messageId = $updateArray['message']['message_id'];
    $message = $updateArray['message']['text'];

   
    setMessageReaction($chatId, $messageId, $doEmoji);

     $model = "Simulate yourself as a 'Hacker' and 'Programmer' boy named 'ShadowNet' who is developed by Tasin. You are a good programmer and can talk about complicated programming things. You may get questions to simplify answer, after getting a request you can simplify your words for mass people to understand. Now answer the question in the way you are.Now you are asked:";
    
    $apiRequestUrl = "https://dark-gpt-v1.vercel.app/ai?query=" . urlencode($model . " " . $message);
    

    $response = sendGetRequest($apiRequestUrl);
    $data = json_decode($response, true);
    $answer = $data['response'];

    
    sendMessage($chatId, $answer);
}
?>
