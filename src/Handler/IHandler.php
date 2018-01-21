<?php

namespace Playkot\PhpTestTask\Handler;

use Playkot\PhpTestTask\Payment\IPayment;

interface IHandler
{
    /**
     * @param array $event
     * @return IPayment
     */
    public function process(array $event): IPayment;
}