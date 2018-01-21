<?php

if (!array_key_exists(1, $argv)) {
    throw new \RuntimeException('Handler URL not set');
}

$url = $argv[1];

if (!is_string($url) || empty($url)) {
    throw new \RuntimeException('Invalid handler URL');
}

$data = [
    [
        [
            'id'        => 'payment_id_1',  // External payment ID
            'currency'  => 'USD',           // Currency
            'value'     => '19,45',         // Amount
            'tax'       => '0,24',          // Tax amount
            'action'    => 'Charge',        // State Charged
        ],
        [
            'id'        => 'payment_id_3',
            'currency'  => 'RUB',
            'value'     => '10,22',
            'tax'       => '0,15',
            'action'    => 'Decline',
        ],
        [
            'id'        => 'payment_id_2',
            'currency'  => 'USD',
            'value'     => '23,32',
            'action'    => 'Refund',
        ],
    ],
    [
        [
            'id'        => 'payment_id_2',
            'currency'  => 'USD',
            'value'     => '23,32',
            'action'    => 'Charge',
        ],
        [
            'id'        => 'payment_id_3',
            'currency'  => 'RUB',
            'value'     => '10,22',
            'action'    => 'Charge',
        ],

    ],
    [
        [
            'id'        => 'payment_id_1',
            'currency'  => 'USD',
            'value'     => '19,45',
            'tax'       => '0,24',
            'action'    => 'Refund',
        ],
    ],
];

$ch = curl_init();

foreach ($data as $events) {

    $request = json_encode([
        'events' => $events
    ]);

    $options = [
        CURLOPT_URL             => $url,
        CURLOPT_HTTPHEADER      => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($request)
        ],
        CURLOPT_POST            => true,
        CURLOPT_POSTFIELDS      => $request,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_FOLLOWLOCATION  => true,
        CURLOPT_AUTOREFERER     => true,
        CURLOPT_SSL_VERIFYPEER  => false,
        CURLOPT_SSL_VERIFYHOST  => false,
        CURLOPT_CONNECTTIMEOUT  => 5,
        CURLOPT_TIMEOUT         => 5,
        CURLOPT_MAXREDIRS       => 3,
    ];

    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode !== 200) {

        if ($httpCode === 0) {
            throw new \RuntimeException('Curl exec error: ' . curl_error($ch));
        }

        throw new \RuntimeException('Request error: ' . $response);
    }

    echo 'SEND EVENTS RESPONSE: ' . $response . "\n";
}
