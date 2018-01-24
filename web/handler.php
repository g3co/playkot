<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Playkot\PhpTestTask\Handler\Handler;
use Playkot\PhpTestTask\Storage\Storage;

$handler = new Handler(Storage::instance());

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    foreach ($data['events'] as $event) {
        try {
            if (empty($event['action'])) {
                throw new \Exception('Empty "action" parameter');
            }

            $stateName = strtoupper($event['action'] . 'd');

            if (defined('Playkot\PhpTestTask\Payment\State::' . $stateName)) {
                $event['action'] = constant('Playkot\PhpTestTask\Payment\State::' . $stateName);
            } else {
                throw new \Exception('Wrong "action" parameter');
            }

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