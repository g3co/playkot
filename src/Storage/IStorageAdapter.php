<?php

namespace Playkot\PhpTestTask\Storage;


use Playkot\PhpTestTask\Payment\IPayment;
use Playkot\PhpTestTask\Payment\IPaymentArray;
use Playkot\PhpTestTask\Storage\Exception;


interface IStorageAdapter
{
    /**
     * Сохранение полей
     *
     * @param string $id paymentId
     * @param array $payment fields array
     * @return bool
     */
    public function save(string $id, array $payment): bool;

    /**
     * Проверка на существование платежа
     *
     * @param string $paymentId
     * @return bool
     */
    public function has(string $paymentId): bool;

    /**
     * Получение платежа
     *
     * @param string $paymentId
     * @return array
     * @throws Exception\NotFound
     */
    public function get(string $paymentId): array;

    /**
     * Удаление платежа
     *
     * @param string $paymentId
     * @return bool
     */
    public function remove(string $paymentId): bool;
}