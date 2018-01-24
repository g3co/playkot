<?php
/**
 * Created by IntelliJ IDEA.
 * User: VKabisov
 * Date: 22.01.2018
 * Time: 13:51
 */

namespace Playkot\PhpTestTask\Handler;


use Playkot\PhpTestTask\Payment\IPayment;
use Playkot\PhpTestTask\Payment\Payment;
use Playkot\PhpTestTask\Payment\Currency;
use Playkot\PhpTestTask\Payment\State;
use Playkot\PhpTestTask\Storage\IStorage;

class Handler implements IHandler
{
    protected $storage = null;

    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param array $event
     * @return IPayment
     * @throws \Exception
     */
    public function process(array $event): IPayment
    {
        if (empty($event['action']) || !State::exists($event['action'])) {
            throw new \Exception('Wrong data in "action" parameter');
        }

        if (empty($event['id'])) {
            throw new \Exception('Wrong data in "id" parameter');
        }

        if (empty($event['currency']) || !Currency::exists($event['currency'])) {
            throw new \Exception('Wrong data in "currency" parameter');
        }

        if (empty($event['value'])) {
            throw new \Exception('Wrong data in "value" parameter');
        }

        $payment = Payment::instance($event['id'],
            new \DateTime(),
            new \DateTime(),
            false,
            Currency::get($event['currency']),
            (float)str_replace(',','.',$event['value']),
            (!empty($event['tax'])) ? (float)str_replace(',','.',$event['tax']) : 0,
            State::get($event['action']));

        $this->storage->save($payment);
        return $payment;
    }
}