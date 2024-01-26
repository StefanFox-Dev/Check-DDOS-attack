<?php
$ids = 0; $ids2 = 0; $idsc = 0; $startCheckAttackList = 'false'; $txt = '🟠 <b>Запуск бота для проверки DDos-атак...</b>';
$ipWebSite = 'https://name.com'; $statusWebSite = 'null'; $txtWebSite = "🟠 <b>Запуск бота для проверки сайта...</b>";


#Send message to Telegram, VK
function send($message = 'ошибка'): void {
    /** TODO
    $url = 'https://api.vk.com/method/messages.send';
    $params = [
    'access_token' => 'vk1.a.', #токен группы
    'v' => '5.85'
    ];
    $params['message'] = $message;
    $params['chat_id'] = 1; #айди чата
    $ch = curl_init($url . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    */

    $tokenTG = 'bot6329'; #токен бота
    $idTG = '-1'; #айди чата
    $urlTG = "https://api.telegram.org/{$tokenTG}/sendMessage?chat_id={$idTG}&parse_mode=html&disable_web_page_preview=true&text=" . urlencode($message);
    $curlTG = curl_init();
    curl_setopt($curlTG, CURLOPT_URL, $urlTG);
    curl_setopt($curlTG, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curlTG);
    $http_code = curl_getinfo($curlTG, CURLINFO_HTTP_CODE);
    if (in_array($http_code, ['200', '301']) && $result !== false) {
        curl_close($curlTG);
    } else {
        echo PHP_EOL . 'Ошибка Telegram - ' . $http_code;

        $urlTG = "https://api.telegram.org/{$tokenTG}/sendMessage?chat_id={$idTG}&parse_mode=html&text=" . urlencode($message);
        $curlTG = curl_init();
        curl_setopt($curlTG, CURLOPT_URL, $urlTG);
        curl_setopt($curlTG, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curlTG);
        $http_code = curl_getinfo($curlTG, CURLINFO_HTTP_CODE);
        curl_close($curlTG);
    }
}


#АККАУНТ (my.aeza.net)
$email = 'aeza@gmail.com'; #почта
$password = 'aeza123'; #пароль
$headers = array(
    'X-API-Key: 12345'
); #api_key


#АВТОРИЗАЦИЯ проверка
$auth_url = 'https://my.aeza.net/api/auth';
$data = array(
    'method' => 'credentials',
    'email' => $email,
    'password' => $password
);
header('Content-Type: text/html; charset=utf-8');


#УСЛУГИ
$service_id = 0; #номер услуги вдс
$service_id2 = 0; #номер услуги waf


#ЗАПУСК
echo $txt;
send($txt);
echo PHP_EOL . $txtWebSite;
send($txtWebSite);

while (true) {

    #СПИСОК АТАК $service_id
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

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://my.aeza.net/api/services/' . $service_id . '/ddosattacks' . http_build_query(array()));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $result = json_decode($response, true);

    if (in_array($http_code, ['200', '301']) && $response !== false) {
        curl_close($ch);
        $c = 0;

        foreach ($result['data']['items'] as $item) {
            $c++;
            if ($c === 1) {
                $id = $item['id'] ?? 'null';
                $idc = $item['id'] ?? 'null';
                $level = $item['level'] ?? 'null';
                $protocol = $item['protocol'] ?? 'null';
                $reason = $item['reason'] ?? 'null';
                $targetIp = $item['targetIp'] ?? 'null';
                $bpsAverage = $item['bpsAverage'] ?? 'null';
                $ppsAverage = $item['ppsAverage'] ?? 'null';
                $ppsPeak = $item['ppsPeak'] ?? 'null';
                $bpsTotal = $item['bpsTotal'] ?? 'null';
                $type = $item['type'] ?? 'null';
                $startAt = date('d-m-Y H:i:s', (int)$item['startAt'] + 60 * 0) . ' Europe/Moscow';
                $endAt = date('d-m-Y H:i:s', (int)$item['endAt'] + 60 * 0) . ' Europe/Moscow';
                $endAt = str_replace(['01-01-1970 00:00:00 Europe/Moscow', '01-01-1970 03:00:00 Europe/Moscow'], 'null', $endAt);
                $mbits = round($bpsTotal / 1000000, 2); $power = 'null';

                if ((int)$mbits < 1024) $power = $mbits . ' Мбит/с';
                if ((int)$mbits >= 1024) $power = round($mbits / 1000, 2) . ' Гбит/с';
                if ((int)$mbits >= 1000000) $power = round($mbits / (1000 * 1000), 2) . ' Тбит/с';
            }
        }

        if ($endAt == 'null') {
            if ($id !== $ids) {
                $ids = $id;
                $text = '📡 <code>#' . $id . '</code> ' . $protocol . '
<pre>' . $power . ' | ' . $ppsPeak . ' пак/с</pre>

🛡 <a href="https://aeza.net/?ref=342273">Aéza AntiDDos 3.5tbps</a>';

                if ($startCheckAttackList === 'true') send($text);
                echo PHP_EOL . PHP_EOL . $text;
            }
        } else {
            if ($idc !== $idsc) {
                $idsc = $idc;
                $text = '🐇 <b>DDoS-атака</b> закончилась на сервере <code>#' . $id . '</code>

 Айпи: ' . $targetIp . '
 Уровень: <b>' . $level . '</b>
 Метод: <b>' . $protocol . '</b>
 Тип: <b>' . $type . '</b>
 Причина: <i>' . $reason . '</i>
 Начало: <b>' . $startAt . '</b>
 Конец: <b>' . $endAt . '</b>
 PPS: <code>' . $ppsPeak . '</code>
 BPS: <code>' . $bpsTotal . '</code>
 <pre>Мощность: ' . $power . '</pre>
 
🛡 <a href="https://aeza.net/?ref=342273">Aéza AntiDDos 3.5tbps</a>';
                
                if ($startCheckAttackList === 'true') send($text);
                echo PHP_EOL . PHP_EOL . $text;
            }
        }
    } else {
        $txt = date('d-m-Y H:i:s:s') . ': API AEZA ERROR CODE: ' . $http_code;
        echo PHP_EOL . PHP_EOL . $txt;
    }


    #СПИСОК АТАК $service_id2
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, 'https://my.aeza.net/api/services/' . $service_id2 . '/waf-notifications' . http_build_query(array()));
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
    $response2 = curl_exec($ch2);
    $http_code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);
    $result2 = json_decode($response2, true);

    if (in_array($http_code2, ['200', '301']) && $response2 !== false) {
        $c2 = 0;

        foreach ($result2['data']['items'] as $item) {
            $c2++;
            if ($c2 == 1) {
                $id2 = $item['id'] ?? 0;
                $ips = count($item['ips'] ?? 0);
                $createdAt = date('d-m-Y H:i:s', $item['createdAt'] + 60 * 0) . ' Europe/Moscow';
            }
        }

        if ($id2 !== $ids2) {
            $ids2 = $id2;
            $text = '📡 <b>DDoS-атака была обнаружена и отфильтрована на сайте WAF #' . $id2 . '</b>

Количество заблокированных адресов: <code>' . $ips . '</code>
Начало: ' . $createdAt . '

❔ <b>Ничего делать не нужно, наша защита автоматически фильтрует все вредоносные запросы к Вашему сайту, поэтому работа будет продолжаться в штатном режиме.</b>';
            if ($startCheckAttackList === 'true') send($text);
            echo PHP_EOL . PHP_EOL . $text;
        }
    } else {
        $txt = date('d-m-Y H:i:s:s') . ': API WAF AEZA ERROR CODE: ' . $http_code;
        echo PHP_EOL . PHP_EOL . $txt;
    }


    #CHECK STATUS MY WEBSITE
    $curlWebSite = curl_init();
    curl_setopt($curlWebSite, CURLOPT_URL, $ipWebSite);
    curl_setopt($curlWebSite, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curlWebSite);
    $http_code = curl_getinfo($curlWebSite, CURLINFO_HTTP_CODE);
    if (in_array($http_code, ['200', '301']) && $result !== false) {
        curl_close($curlWebSite);

        if ($statusWebSite === 'false') {
            $statusWebSite = 'true';
            $txtWebSite = "🟢 Сайт $ipWebSite вернул код: <b>$http_codeb>";
            if ($startCheckAttackList === 'true') send($txtWebSite);
            echo PHP_EOL . $txtWebSite;
        }
    } else {
        if ($statusWebSite === 'true') {
            $statusWebSite = 'false';
            $txtWebSite = "🔴 Сайт $ipWebSite вернул код: <b>$http_code</b>";
            if ($startCheckAttackList === 'true') send($txtWebSite);
            echo PHP_EOL . $txtWebSite;
        }
    }

    $startCheckAttackList = 'true';
    sleep(10);
}
?>
