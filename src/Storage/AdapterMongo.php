<?php

namespace Playkot\PhpTestTask\Storage;


class AdapterMongo implements IStorageAdapter
{
    protected $connection = null;

    public function __construct(array $config)
    {
        if (empty($config['host'])) {
            $config['host'] = '127.0.0.1';
        }

        if (empty($config['port'])) {
            $config['port'] = '27017';
        }

        $this->connection = new \MongoClient('mongodb://' . $config['host'] . ':' . $config['port']);

        if (!empty($config['db'])) {
            if (!$this->connection->c((int)$config['db'])) {
                throw new \Exception('The failure connect to a Redis db');
            }
        }

    }

    public function save(string $id, array $payment): bool
    {

    }

    public function has(string $paymentId): bool
    {
        // TODO: Implement has() method.
    }

    public function get(string $paymentId): array
    {
        // TODO: Implement get() method.
    }

    public function remove(IPayment $payment): bool
    {
        // TODO: Implement remove() method.
    }
}