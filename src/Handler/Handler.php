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
    protected $stateMapping = null;

    public function __construct(IStorage $storage, array $stateMapping = [])
    {
        $this->storage = $storage;
        $this->stateMapping = $stateMapping;
    }

    /**
     * @param array $event
     * @return IPayment
     * @throws \Exception
     */
    public function process(array $event): IPayment
    {
        if (empty($event['action']) ||
            !array_key_exists($event['action'], $this->stateMapping) ||
            !State::exists($this->stateMapping[$event['action']])
        ) {
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
            (float)$event['value'],
            (!empty($event['tax'])) ? (float)$event['tax'] : 0,
            State::get(State::CHARGED));

        return $this->storage->save($payment);
    }
}