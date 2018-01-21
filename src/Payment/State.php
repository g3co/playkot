<?php

namespace Playkot\PhpTestTask\Payment;

final class State
{
    const CREATED   = 0; // Платёж создан но не проведён
    const CHARGED   = 1; // Платёж успешно проведён
    const DECLINED  = 2; // Платёж отменён
    const REFUNDED  = 3; // Платёж возвращён

    private static $states = [
        self::CREATED   => null,
        self::CHARGED   => null,
        self::DECLINED  => null,
        self::REFUNDED  => null,
    ];

    /** @var int */
    private $code;

    /**
     * @param int $code
     */
    public function __construct(int $code)
    {
        if (!self::exists($code)) {
            throw new \InvalidArgumentException('Undefined payment state ' . $code);
        }

        $this->code = $code;
    }

    /**
     * @param int $code
     * @return bool
     */
    public static function exists(int $code): bool
    {
        return array_key_exists($code, self::$states);
    }

    /**
     * @param string $code
     * @return State
     */
    public static function get(string $code): State
    {
        if (self::$states[$code] === null) {
            self::$states[$code] = new self($code);
        }

        return self::$states[$code];
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
}