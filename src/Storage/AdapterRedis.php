<?php

namespace Playkot\PhpTestTask\Storage;


use Playkot\PhpTestTask\Payment\IPayment;
use Playkot\PhpTestTask\Payment\IPaymentArray;

class AdapterRedis implements IStorageAdapter
{
    protected $redis = null;

    public function __construct(array $config)
    {
        if (empty($config['host'])) {
            $config['host'] = '127.0.0.1';
        }

        if (empty($config['port'])) {
            $config['port'] = '6379';
        }

        $this->redis = new \Redis();
        if (!$this->redis->connect($config['host'], $config['port'])) {
            throw new \Exception('The failure connect to a Redis instance');
        }

        if (!empty($config['db'])) {
            if (!$this->redis->select((int)$config['db'])) {
                throw new \Exception('The failure connect to a Redis db');
            }
        }
    }

    /**
     * Сохранение полей
     *
     * @param string $id paymentId
     * @param array $payment fields array
     * @return bool
     */
    public function save(string $id, array $payment): bool
    {
        foreach ($payment as $key => $value) {
            $this->redis->hSet($id, $key, $value);
        }
        return true;
    }

    /**
     * Проверка на существование платежа
     *
     * @param string $paymentId
     * @return bool
     */
    public function has(string $paymentId): bool
    {
        return $this->redis->exists($paymentId);
    }

    /**
     * Получение платежа
     *
     * @param string $paymentId
     * @return array
     * @throws Exception\NotFound
     */
    public function get(string $paymentId): array
    {
        $result = $this->redis->hGetAll($paymentId);

        if(!$result) {
            throw new Exception\NotFound();
        }

        return $result;
    }

    /**
     * Удаление платежа
     *
     * @param string $paymentId
     * @return bool
     */
    public function remove(string $paymentId): bool
    {
        return $this->redis->del($paymentId);
    }


}