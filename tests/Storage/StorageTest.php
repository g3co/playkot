<?php

namespace Test\Playkot\PhpTestTask\Storage;

use Playkot\PhpTestTask\Payment\Currency;
use Playkot\PhpTestTask\Payment\IPayment;
use Playkot\PhpTestTask\Payment\State;
use Playkot\PhpTestTask\Storage\IStorage;
use Playkot\PhpTestTask\Storage\Storage as Storage;
use Playkot\PhpTestTask\Payment\Payment as Payment;
use Playkot\PhpTestTask\Storage\Exception;
use PHPUnit\Framework\TestCase;

class StorageTest extends TestCase
{
    public function testCreateStorage()
    {
        self::assertTrue(class_exists('Playkot\PhpTestTask\Storage\Storage'), 'Storage class not found');
        self::assertTrue(in_array(IStorage::class, class_implements(Storage::class)), 'Storage not implements IStorage interface');

        $storage = Storage::instance();
        self::assertInstanceOf(Storage::class, $storage);

        for ($i = 1; $i < 10; $i++) {

            $paymentId = 'payment_' . $i;

            $payment = Payment::instance(
                $paymentId,
                new \DateTime('2017-01-02 03:04:05'),
                new \DateTime('2017-02-03 04:05:06'),
                $i % 2 ? true : false,
                Currency::get(Currency::USD),
                14.55 * $i,
                1.34 * $i,
                State::get((int)($i % 4))
            );

            self::assertFalse($storage->has($paymentId), 'Payment already exists');

            try {
                $storage->get($paymentId);
                $this->fail('Payment NotFound exception expected');
            } catch (\Throwable $e) {
                $this->assertInstanceOf(Exception\NotFound::class, $e, 'Expected NotFound exception');
            }

            // Create
            self::assertSame($storage, $storage->save($payment));
            self::assertTrue($storage->has($paymentId), 'Payment saved but not exists');

            // Read
            self::assertInstanceOf(IPayment::class, $storage->get($paymentId));
            self::assertNotSame($payment, $storage->get($paymentId), 'Payment instance same storage instance');
            self::assertEquals($payment, $storage->get($paymentId), 'Payment instance not equals storage instance');

            // Update
            self::assertSame($storage, $storage->save($payment));
            self::assertEquals($payment, $storage->get($paymentId), 'Payment not modified but changed');

            $changedPayment = Payment::instance(
                $paymentId,
                new \DateTime('2017-03-04 05:06:07'),
                new \DateTime('2017-02-03 04:05:06'),
                $i % 2 ? false :true,
                Currency::get(Currency::RUB),
                12.22 * $i,
                2.54 * $i,
                State::get((int)($i % 4))
            );

            self::assertSame($storage, $storage->save($changedPayment));
            self::assertEquals($changedPayment, $storage->get($paymentId), 'Payment was modified but not changed');

            // Delete
            self::assertSame($storage, $storage->remove($payment));
            self::assertFalse($storage->has($paymentId), 'Payment not removed');
            try {
                $storage->get($paymentId);
                $this->fail('Payment NotFound exception expected');
            } catch (\Throwable $e) {
                $this->assertInstanceOf(Exception\NotFound::class, $e, 'Expected NotFound exception');
            }
        }
    }
}
