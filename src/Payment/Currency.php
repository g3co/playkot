<?php

namespace Playkot\PhpTestTask\Payment;

final class Currency
{
    const USD = 'USD';
    const ZAR = 'ZAR';
    const KZT = 'KZT';
    const AED = 'AED';
    const INR = 'INR';
    const GBP = 'GBP';
    const CAD = 'CAD';
    const AUD = 'AUD';
    const NZD = 'NZD';
    const EUR = 'EUR';
    const SGD = 'SGD';
    const QAR = 'QAR';
    const HKD = 'HKD';
    const PHP = 'PHP';
    const ILS = 'ILS';
    const TZS = 'TZS';
    const SAR = 'SAR';
    const EGP = 'EGP';
    const NOK = 'NOK';
    const SEK = 'SEK';
    const RUB = 'RUB';
    const NGN = 'NGN';
    const MYR = 'MYR';
    const JPY = 'JPY';
    const MXN = 'MXN';
    const CNY = 'CNY';
    const IDR = 'IDR';
    const CHF = 'CHF';
    const TWD = 'TWD';
    const TRY = 'TRY';
    const PKR = 'PKR';
    const VND = 'VND';
    const THB = 'THB';
    const DKK = 'DKK';

    private static $currency = [
        self::USD => null,
        self::ZAR => null,
        self::KZT => null,
        self::AED => null,
        self::INR => null,
        self::GBP => null,
        self::CAD => null,
        self::AUD => null,
        self::NZD => null,
        self::EUR => null,
        self::SGD => null,
        self::QAR => null,
        self::HKD => null,
        self::PHP => null,
        self::ILS => null,
        self::TZS => null,
        self::SAR => null,
        self::EGP => null,
        self::NOK => null,
        self::SEK => null,
        self::RUB => null,
        self::NGN => null,
        self::MYR => null,
        self::JPY => null,
        self::MXN => null,
        self::CNY => null,
        self::IDR => null,
        self::CHF => null,
        self::TWD => null,
        self::TRY => null,
        self::PKR => null,
        self::VND => null,
        self::THB => null,
        self::DKK => null,
    ];

    /** @var string */
    private $code;

    /**
     * @param string $code
     */
    public function __construct(string $code)
    {
        if (!self::exists($code)) {
            throw new \InvalidArgumentException('Undefined currency ' . $code);
        }

        $this->code = $code;
    }

    /**
     * @param string $code
     * @return bool
     */
    public static function exists(string $code): bool
    {
        return array_key_exists($code, self::$currency);
    }

    /**
     * @param string $code
     * @return Currency
     */
    public static function get(string $code): Currency
    {
        if (self::$currency[$code] === null) {
            self::$currency[$code] = new self($code);
        }

        return self::$currency[$code];
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}