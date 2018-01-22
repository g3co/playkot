<?php

namespace Playkot\PhpTestTask\Storage;

use Playkot\PhpTestTask\Payment\IPayment;
use Playkot\PhpTestTask\Payment\IPaymentArray;
use Playkot\PhpTestTask\Payment\Payment;
use Playkot\PhpTestTask\Storage\Exception;

/**
 * Class Storage
 * @package Playkot\PhpTestTask\Storage
 */
class Storage implements IStorage
{
    const ADAPTER_REDIS = 0;
    const ADAPTER_MONGO = 1;

    /** @var IStorageAdapter $storage */
    protected $storage = null;

    /**
     * Фабричный метод для создания экземпляра хранилища
     *
     * @param array $config
     * @return IStorage
     */
    public static function instance(array $config = []): IStorage
    {
        if (!empty($config['adapter'])) {
            switch ($config['adapter']) {
                case self::ADAPTER_MONGO:
                    return new self(new AdapterMongo($config));
                    break;
            }
        }

        return new self(new AdapterRedis($config));
    }

    public function __construct(IStorageAdapter $adapter)
    {
        $this->storage = $adapter;
    }

    /**
     * Сохранение только изменившихся полей существующего платежа
     * или создание нового
     *
     * @param IPayment $payment
     * @throws \Exception
     * @return IStorage
     */
    public function save(IPayment $payment): IStorage
    {
        if (!$payment instanceof IPaymentArray) {
            throw new \InvalidArgumentException('Parameter must implement IPaymentArray interface');
        }

        $paymentArray = $payment->toArray();

        $isUpdate = false;

        if ($this->storage->has($payment->getId())) {
            $paymentArray = array_diff($paymentArray, $this->storage->get($payment->getId()));
            $isUpdate = true;
        }

        if (!$this->storage->save($payment->getId(), $paymentArray, $isUpdate)) {
            throw new \Exception('Did not save');
        }

        return $this;
    }

    /**
     * Проверка на существование платежа
     *
     * @param string $paymentId
     * @return bool
     */
    public function has(string $paymentId): bool
    {
        return $this->storage->has($paymentId);
    }

    /**
     * Получение платежа
     *
     * @param string $paymentId
     * @return IPayment
     * @throws Exception\NotFound
     */
    public function get(string $paymentId): IPayment
    {
        $result = $this->storage->get($paymentId);

        if (empty($result)) {
            throw new Exception\NotFound();
        }

        return Payment::fromArray($result);
    }

    /**
     * Удаление платежа
     *
     * @param IPayment $payment
     * @return IStorage
     */
    public function remove(IPayment $payment): IStorage
    {
        $this->storage->remove($payment->getId());
        return $this;
    }
}