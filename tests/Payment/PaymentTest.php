<?php

namespace Test\Playkot\PhpTestTask\Payment;

use Playkot\PhpTestTask\Payment\Currency;
use Playkot\PhpTestTask\Payment\IPayment;
use Playkot\PhpTestTask\Payment\State;
use Playkot\PhpTestTask\Payment\Payment as Payment;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    /**
     * @return array
     */
    public function validPaymentsProvider(): array
    {
        return [
            [
                'payment_1',
                new \DateTime('2017-01-02 03:04:05'),
                new \DateTime('2017-02-03 04:05:06'),
                true,
                Currency::get(Currency::USD),
                14.69,
                0.14,
                State::get(State::CREATED),
            ],
            [
                'payment_2',
                new \DateTime('2017-02-03 04:05:06'),
                new \DateTime('2017-03-04 05:06:07'),
                false,
                Currency::get(Currency::RUB),
                0.0,
                0.0,
                State::get(State::CHARGED),
            ],
            [
                'payment_3',
                new \DateTime('2017-02-03 04:05:06'),
                new \DateTime('2017-01-02 03:04:05'),
                true,
                Currency::get(Currency::AED),
                1.0,
                1.0,
                State::get(State::REFUNDED),
            ],
            [
                'payment_4',
                new \DateTime('2017-02-03 04:05:06'),
                new \DateTime('2017-01-02 03:04:05'),
                true,
                Currency::get(Currency::AED),
                145.456,
                10.34,
                State::get(State::DECLINED),
            ],
        ];
    }

    public function testPaymentExists()
    {
        self::assertTrue(class_exists('Playkot\PhpTestTask\Payment\Payment'), 'Payment class not found');
        self::assertTrue(in_array(IPayment::class, class_implements(Payment::class)), 'Payment not implements IPayment interface');
    }

    /**
     * @depends testPaymentExists
     * @dataProvider validPaymentsProvider
     *
     * @param string $paymentId
     * @param \DateTime $created
     * @param \DateTime $updated
     * @param bool $isTest
     * @param Currency $currency
     * @param float $amount
     * @param float $taxAmount
     * @param State $state
     */
    public function testValidPayment(string $paymentId, \DateTime $created, \DateTime $updated, bool $isTest, Currency $currency, float $amount, float $taxAmount, State $state)
    {
        $payment = Payment::instance(
            $paymentId,
            $created,
            $updated,
            false,
            $currency,
            $amount,
            $taxAmount,
            $state
        );

        self::assertInstanceOf(Payment::class, $payment);

        self::assertSame($paymentId, $payment->getId());

        self::assertInstanceOf(\DateTimeInterface::class, $payment->getCreated());
        self::assertEquals($created, $payment->getCreated());
        self::assertNotSame($created, $payment->getCreated());

        $createdDate = $payment->getCreated();
        if ($createdDate instanceof \DateTime) {
            $createdDate->modify('+5 seconds');
            self::assertEquals($payment->getCreated(), $created->modify('+5 seconds'));
        }

        self::assertInstanceOf(\DateTimeInterface::class, $payment->getUpdated());
        self::assertEquals($updated, $payment->getUpdated());
        self::assertNotSame($updated, $payment->getUpdated());

        $updatedDate = $payment->getUpdated();
        if ($updatedDate instanceof \DateTime) {
            $updatedDate->modify('+5 seconds');
            self::assertEquals($payment->getUpdated(), $updated->modify('+5 seconds'));
        }

        self::assertFalse($payment->isTest());
        self::assertSame($currency, $payment->getCurrency());
        self::assertSame($amount, $payment->getAmount());
        self::assertSame($taxAmount, $payment->getTaxAmount());
        self::assertSame($state, $payment->getState());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPaymentId()
    {
            Payment::instance(
                '',
                new \DateTime('2017-01-02 03:04:05'),
                new \DateTime('2017-02-03 04:05:06'),
                true,
                Currency::get(Currency::USD),
                14.69,
                0.14,
                State::get(State::CREATED)
            );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAmount()
    {
        Payment::instance(
            'payment_1',
            new \DateTime('2017-01-02 03:04:05'),
            new \DateTime('2017-02-03 04:05:06'),
            true,
            Currency::get(Currency::USD),
            -0.01,
            0.14,
            State::get(State::CREATED)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidTaxAmount()
    {
        Payment::instance(
            'payment_1',
            new \DateTime('2017-01-02 03:04:05'),
            new \DateTime('2017-02-03 04:05:06'),
            true,
            Currency::get(Currency::USD),
            14.69,
            -0.01,
            State::get(State::CREATED)
        );
    }
}
