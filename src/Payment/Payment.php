<?php

namespace Playkot\PhpTestTask\Payment;


class Payment implements IPayment, IPaymentArray
{
    protected $paymentId = null;
    protected $created = null;
    protected $updated = null;
    protected $isTest = null;
    protected $currency = null;
    protected $amount = null;
    protected $taxAmount = null;
    protected $state = null;

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
        string $paymentId,
        \DateTimeInterface $created,
        \DateTimeInterface $updated,
        bool $isTest,
        Currency $currency,
        float $amount,
        float $taxAmount,
        State $state
    ): IPayment
    {
        return new self($paymentId, $created, $updated, $isTest, $currency, $amount, $taxAmount, $state);
    }

    public function __construct(
        string $paymentId,
        \DateTimeInterface $created,
        \DateTimeInterface $updated,
        bool $isTest,
        Currency $currency,
        float $amount,
        float $taxAmount,
        State $state
    )
    {
        if (strlen($paymentId) == 0) {
            throw new \InvalidArgumentException('PaymentId must not be empty');
        } else {
            $this->paymentId = $paymentId;
        }

        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount must be more than 0. Current amount is incorrect :' . $amount);
        } else {
            $this->amount = $amount;
        }

        if ($taxAmount < 0) {
            throw new \InvalidArgumentException('Tax amount must be more than 0. Current tax amount is incorrect :' . $taxAmount);
        } else {
            $this->taxAmount = $taxAmount;
        }

        $this->created = clone $created;
        $this->updated = clone $updated;
        $this->isTest = $isTest;
        $this->currency = $currency;
        $this->state = $state;
    }

    /**
     * Идентификатор платежа
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->paymentId;
    }

    /**
     * Дата создания платежа
     *
     * @return \DateTimeInterface
     */
    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    /**
     * Дата последнего обновления платежа
     *
     * @return \DateTimeInterface
     */
    public function getUpdated(): \DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * Признак тестового платежа
     *
     * @return bool
     */
    public function isTest(): bool
    {
        return $this->isTest;
    }

    /**
     * Валюта платежа
     *
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * Сумма платежа включая сумму налога
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Сумма налога от платежа
     *
     * @return float
     */
    public function getTaxAmount(): float
    {
        return $this->taxAmount;
    }

    /**
     * Идентификатор состояния платежа
     *
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }

    public function toArray(): array
    {
        return [
            'paymentId' => (string)$this->getId(),
            'created' => serialize($this->getCreated()),
            'updated' => serialize($this->getUpdated()),
            'isTest' => (bool)$this->isTest(),
            'currency' => $this->getCurrency()->getCode(),
            'amount' => (float)$this->getAmount(),
            'taxAmount' => (float)$this->getTaxAmount(),
            'state' => $this->getState()->getCode()
        ];
    }

    public static function fromArray($paymentArray): IPayment
    {
        if (count(array_diff_key(get_class_vars(self::class), $paymentArray))) {
            throw new \UnexpectedValueException('Input array has incorrect structure');
        }

        return new self(
            (string)$paymentArray['paymentId'],
            unserialize($paymentArray['created']),
            unserialize($paymentArray['updated']),
            (bool)$paymentArray['isTest'],
            Currency::get($paymentArray['currency']),
            (float)$paymentArray['amount'],
            (float)$paymentArray['taxAmount'],
            State::get($paymentArray['state'])
        );
    }
}