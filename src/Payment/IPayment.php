<?php

namespace Playkot\PhpTestTask\Payment;

interface IPayment
{
    /**
     * @param string $paymentId
     * @param \DateTimeInterface $created
     * @param \DateTimeInterface $updated
     * @param bool $isTest
     * @param Currency $currency
     * @param float $amount
     * @param float $taxAmount
     * @param State $state
     * @return IPayment
     */
    public static function instance(
        string                  $paymentId,
        \DateTimeInterface      $created,
        \DateTimeInterface      $updated,
        bool                    $isTest,
        Currency                $currency,
        float                   $amount,
        float                   $taxAmount,
        State                   $state
    ): IPayment;

    /**
     * Идентификатор платежа
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Дата создания платежа
     *
     * @return \DateTimeInterface
     */
    public function getCreated(): \DateTimeInterface;

    /**
     * Дата последнего обновления платежа
     *
     * @return \DateTimeInterface
     */
    public function getUpdated(): \DateTimeInterface;

    /**
     * Признак тестового платежа
     *
     * @return bool
     */
    public function isTest(): bool;

    /**
     * Валюта платежа
     *
     * @return Currency
     */
    public function getCurrency(): Currency;

    /**
     * Сумма платежа включая сумму налога
     *
     * @return float
     */
    public function getAmount(): float;

    /**
     * Сумма налога от платежа
     *
     * @return float
     */
    public function getTaxAmount(): float;

    /**
     * Идентификатор состояния платежа
     *
     * @return State
     */
    public function getState(): State;
}