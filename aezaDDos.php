<?php
$ids = 0; $ids2 = 0; $idsc = 0; $start = 'false'; $txt = '🟠 Проверка атак...';


#Отправка сообщений в Телеграм / Send message to Telegram
function send(string $message = ''): void {
    $token = 'bot1234:TEST-test'; #токен бота / token bot
    $id = '-10071'; #айди чата / chat id

    $url = 'https://api.telegram.org/' . $token . '/sendMessage?chat_id=' . $id . '&text=' . urlencode($message);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    $url = 'https://api.vk.com/method/messages.send';
    $params = [
        'access_token' => 'vk1.a.', #токен / token
        'v' => '5.85'
    ];
    $params['message'] = $message;
    $params['chat_id'] = 1; #айди чата / id chat
    $ch = curl_init($url . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
}


#АККАУНТ / ACCOUNT [my.aeza.net]
$email = 'test@gmail.com'; #почта / email
$password = '1234pwd'; #пароль / password
$headers = array(
    'X-API-Key: 51ga2'
); #api_key


#АВТОРИЗАЦИЯ / AUTH
$auth_url = 'https://my.aeza.net/api/auth';
$data = array(
    'method' => 'credentials',
    'email' => $email,
    'password' => $password
);
header('Content-Type: text/html; charset=utf-8');


#УСЛУГИ / SERVICES
$service_id = 99999; #номер услуги / number service
$service_id2 = 999999; #id waf-guard


#СТАРТ / START
send($txt); echo $txt;
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


    #service id список атак / attacks list
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://my.aeza.net/api/services/' . $service_id . '/ddosattacks' . http_build_query(array()));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $result = json_decode($response, true);

    if ($http_code === 200 || $http_code === 301) {
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
                $startAt = date('d-m-Y H:i:s', (int)$item['startAt'] + 60 * 0);
                $endAt = date('d-m-Y H:i:s', (int)$item['endAt'] + 60 * 0);
                $endAt = str_replace('01-01-1970 03:00:00', 'null', $endAt);
                $mbit = round($bpsAverage / 1000000, 1) . ' Мбит/с';
            }
        }

        if ($endAt == 'null') {
            if ($id !== $ids) {
                $ids = $id;
                $text = '📡 На #' . $service_id . ' началась DDoS-атака #' . $id . '

💡 Метод: ' . $protocol . ' (' . $level . ')
🖥️ Причина: ' . $type . '
💥 Начало: ' . $startAt . '
📨 Пакетов в секунду: ' . $ppsPeak . ' Пак/с
🚿 Мощность в секунду: ' . $mbit . '

❔ Ничего делать не нужно, наша защита автоматически фильтрует все вредоносные запросы к серверу, поэтому работа будет продолжаться в штатном режиме.';

                if ($start === 'true') send($text);
                echo PHP_EOL . PHP_EOL . $text;
            }
        } else {
            if ($idc !== $idsc) {
            $idsc = $idc;
            $text = '📡 На #' . $service_id . ' закончилась DDoS-атака #' . $id . '

            💡 Метод: ' . $protocol . ' (' . $level . ')
            🖥️ Причина: ' . $type . '
            💥 Начало: ' . $startAt . '
            🐇 Конец: ' . $endAt . '
            📨 Пакетов в секунду: ' . $ppsAverage . ' Пак/с
            🚿 Мощность в секунду: ' . $mbit;
            if ($start === 'true') send($text);
            echo PHP_EOL . PHP_EOL . $text;
            }
        }
    } else {
        $txt = date('d-m-Y H:i:s:s') . ': Сайт аезы умер, код ошибки: ' . $http_code;
        send($txt);
        echo PHP_EOL . PHP_EOL . $txt;
    }

    /**
    #waf-guard список атак / attacks list
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, 'https://core.aeza.net/api/services/' . (int)$service_id2 . '/waf/attacks' . http_build_query(array()));
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
    $response2 = curl_exec($ch2);
    $http_code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);
    $result2 = json_decode($response2, true);

    if ($http_code2 === 200 || $http_code === 301) {
    $c2 = 0;

    foreach ($result2['data']['items'] as $item){ $c2++;
    if($c2 == 1){
    $id2 = (int)$item['id'] ?? 0;
    $protocol = 'https';
    $ips = count($item['ips'] ?? 0);
    $createdAt = date('d-m-Y H:i:s', (int)$item['createdAt'] + 60 * 0);
    }
    }

    if($id2 !== $ids2){
    $ids2 = $id2;
    $text = '📡 На #' . $service_id2 . ' была зафиксирована и отфильтрована DDoS-атака #' . $id2 . '

    💡 Метод: ' . $protocol . ' (L7)
    🗑 Число заблокированных адресов: ' . $ips . '
    📨 Отфильтровано: ' . $createdAt . '

    ❔ Ничего делать не нужно, наша защита автоматически фильтрует все вредоносные запросы к сайту, поэтому работа будет продолжаться в штатном режиме.';
    if($start === 'true') send($text);
    echo PHP_EOL . PHP_EOL . $text;
    }
    } else {
        $txt = date('d-m-Y H:i:s:s') . ': Сайт аезы умер, код ошибки: ' . $http_code;
        send($txt);
        echo PHP_EOL . PHP_EOL . $txt;
    }
    */

    $start = 'true';
    sleep(1);
}
?>
