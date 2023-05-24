<?php

/* Отправка сообщений в ВКонтакте и Телеграм */
function send(string $msg = ''): void {
  $idChatTG = 0;
  $idChatVK = 0;
  $keyBotTG = 'bot58:an';
  
    //TG
    $url = 'https://api.telegram.org/' . (string)$keyBotTG . '/sendMessage?chat_id=' . (int)$idChatTG . '&text=' . urlencode($msg);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    
    //VK
    $url = 'https://api.vk.com/method/messages.send';
    $params = [
        'access_token' => 'vk1.a.', //key group
        'v' => '5.85'
    ];
    $params['message'] = $msg;
    $params['chat_id'] = (int)$idChatVK;
    $ch = curl_init($url . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
}


$email = 'google@gmail.com'; //почта
$password = 'aeza517'; //пароль к аккаунту
$headers = array(
    'X-API-Key: 6262%!'
); //api_key профиля
$auth_url = 'https://core.aeza.net/api/auth';
$data = array(
    'method' => 'credentials',
    'email' => (string)$email,
    'password' => (string)$password
);
$service_id = 0; //номер услуги
$service_id2 = 0; //waf-aeza номер услуги
$ids = 0; //vds id начало атаки
$idsc = 0; //vds id конец атаки
$ids2 = 0; //waf-aeza id атаки
header('Content-Type: text/html; charset=utf-8');
$start = 'false';
echo '🟢 Перезагрузка скрипта проверки атак...';

  while (true) {
    $payload = json_encode($data);
    $ch = curl_init($auth_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json'
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    $json_response = json_decode($response, true);

    //vds атаки
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://core.aeza.net/api/services/' . (int)$service_id . '/ddosattacks' . http_build_query(array()));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $result = json_decode($response, true);

    if ($http_code === 200) {
        $c = 0;

        foreach ($result['data']['items'] as $item) {
            $c++;
            if ($c == 1) {
                $id = $item['id'] ?? 0;
                $idc = $item['id'] ?? 0;
                $level = $item['level'] ?? 'null';
                $protocol = $item['protocol'] ?? 'null';
                $bpsTotal = $item['bpsTotal'] ?? 0;
                $bpsAverage = $item['bpsAverage'] ?? 0;
                $ppsPeak = $item['ppsPeak'] ?? 0;
                $ppsAverage = $item['ppsAverage'] ?? 0;
                $type = $item['type'] ?? 'null';
                $startAt = date('d-m-Y H:i', (int)$item['startAt'] + 60 * 0);
                $endAt = date('d-m-Y H:i', (int)$item['endAt'] + 60 * 0);
                $endAt = str_replace('01-01-1970 03:00', 'null', $endAt);
            }
        }

        if ($endAt == 'null') {
            if ($id !== $ids) {
                $ids = $id;
                $text = '📡 На сервер началась DDoS-атака #' . $id . '

💡 Метод: ' . $protocol . ' (' . $level . ')
🖥️ Причина: ' . $type . '
💥 Начало: ' . $startAt . '
📨 Пакетов в секунду: ' . $ppsPeak . ' Пак/с
🚿 Мощность в секунду: ' . round($bpsTotal / 1000000, 1) . ' МБит/с

❔ Ничего делать не нужно, наша защита автоматически фильтрует все вредоносные запросы к серверу, поэтому работа будет продолжаться в штатном режиме.';

                if ($start === 'true') send($text);
                echo PHP_EOL . PHP_EOL . $text;
            }
        } else {
            if ($idc !== $idsc) {
                $idsc = $idc;
                $text = '📡 На сервер закончилась DDoS-атака #' . $id . '

💡 Метод: ' . $protocol . ' (' . $level . ')
🖥️ Причина: ' . $type . '
💥 Начало: ' . $startAt . '
🐇 Конец: ' . $endAt . '
📨 Пакетов в секунду: ' . $ppsAverage . ' Пак/с
🚿 Мощность в секунду: ' . round($bpsAverage / 1000000, 1) . ' МБит/с';
                if ($start === 'true') send($text);
                echo PHP_EOL . PHP_EOL . $text;
            }
        }
    } else echo PHP_EOL . PHP_EOL . date('d-m-Y H:i:s') . ': Сайт аезы умер, ' . $http_code;

    /**
    //waf-guard атаки
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, 'https://core.aeza.net/api/services/' . (int)$service_id2 . '/waf/attacks' . http_build_query(array()));
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
    $response2 = curl_exec($ch2);
    $http_code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);
    $result2 = json_decode($response2, true);

    if ($http_code2 === 200) {
    $c2 = 0;

    foreach ($result2['data']['items'] as $item){ $c2++;
    if($c2 == 1){
    $id2 = (int)$item['id'] ?? 0;
    $protocol = 'https';
    $ips = count($item['ips'] ?? 0);
    $createdAt = date('d-m-Y H:i', (int)$item['createdAt'] + 60 * 0);
    }
    }

    if($id2 !== $ids2){
    $ids2 = $id2;
    $text = '📡 На сайт была зафиксирована и отфильтрована DDoS-атака #' . $id2 . '

    💡 Метод: ' . $protocol . ' (L7)
    🗑 Число заблокированных адресов: ' . $ips . '
    📨 Отфильтровано: ' . $createdAt . '

    ❔ Ничего делать не нужно, наша защита автоматически фильтрует все вредоносные запросы к сайту, поэтому работа будет продолжаться в штатном режиме.';
    if($start === 'true') send($text);
    echo PHP_EOL . PHP_EOL . $text;
    }
    } else echo PHP_EOL . PHP_EOL . date('d-m-Y H:i:s') . ': Сайт аезы умер, ' . $http_code;
    */
    
    $start = 'true';
    sleep(300);
}
?>