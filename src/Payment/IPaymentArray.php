<?php

namespace Playkot\PhpTestTask\Payment;

interface IPaymentArray extends IPayment
{
    /**
     * Преобразование объекта в ассоциативный массив
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Восстановление объекта из массива
     *
     * @param array $paymentArray массив с данными платежа
     * @return IPayment
     */
    public static function fromArray($paymentArray): IPayment;
}