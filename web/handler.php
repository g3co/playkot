<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Playkot\PhpTestTask\Handler\Handler;
use Playkot\PhpTestTask\Storage\Storage;
use Playkot\PhpTestTask\Payment\State;

$handler = new Handler(Storage::instance(), [
    'Charge' => State::CHARGED,
    'Decline' => State::DECLINED,
    'Refund' => State::REFUNDED
]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    foreach ($data['events'] as $event) {
        try {
            $handler->process($event);
        } catch (Exception $e) {
            http_response_code(400);
        }
    }

    http_response_code(200);
    echo "OK";
} else {
    http_response_code(404);
}