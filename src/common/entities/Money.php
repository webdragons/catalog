<?php

namespace bulldozer\catalog\common\entities;

use bulldozer\catalog\common\ar\Currency;
use yii\base\Exception;

/**
 * Class Money
 * @package bulldozer\catalog\common\entities
 */
class Money
{
    const SCALE = 0;

    /**
     * @var string
     */
    private $value;

    /**
     * @var Currency|null
     */
    private $currency;

    /**
     * Money constructor.
     * @param $value
     * @param Currency|null $currency
     */
    public function __construct($value, Currency $currency = null)
    {
        $this->value = (string)$value;
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param $value
     */
    public function setValue($value): void
    {
        $this->value = (string)$value;
    }

    /**
     * @return Currency|null
     */
    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param string $dec_point
     * @param string $thousands_sep
     * @return string
     */
    public function getFormattedValue(string $dec_point = ".", string $thousands_sep = " "): string
    {
        $integer = '';
        $fractional = '0';
        $scale = self::SCALE;

        $parts = explode('.', $this->value);

        if (count($parts) == 2) {
            $integer = $parts[0];
            $fractional = $parts[1];
        } elseif (count($parts) == 1 || count($parts) == 0) {
            $integer = $parts[0];
        }

        $integer = strrev(implode($thousands_sep, str_split(strrev($integer), 3)));
        $fractional = rtrim($fractional, '0');

        if (strlen($fractional) > $scale) {
            $fractional = substr($fractional, 0, $scale);
        }

        return $integer . (strlen($fractional) > 0 ? $dec_point . $fractional : '') . ($this->currency ? ' ' .  $this->currency->short_name : '');
    }

    /**
     * @param string|Money $value
     * @return Money
     * @throws Exception
     */
    public function add($value): Money
    {
        return new Money($this->operation('add', $value), $this->currency);
    }

    /**
     * @param $value
     * @return Money
     * @throws Exception
     */
    public function div($value): Money
    {
        return new Money($this->operation('div', $value), $this->currency);
    }

    /**
     * @param string|Money $value
     * @return Money
     * @throws Exception
     */
    public function mul($value): Money
    {
        return new Money($this->operation('mul', $value), $this->currency);
    }

    /**
     * @param string|Money $value
     * @return Money
     * @throws Exception
     */
    public function sub($value): Money
    {
        return new Money($this->operation('sub', $value), $this->currency);
    }

    /**
     * @param string|Money $value
     * @return int
     */
    public function comp($value): int
    {
        if ($value instanceof Money) {
            $value = $value->getValue();
        }

        return bccomp($this->getValue(), $value);
    }

    /**
     * @param string|Money $value
     * @return bool
     */
    public function less($value): bool
    {
        return $this->comp($value) == -1;
    }

    /**
     * @param string|Money $value
     * @return bool
     */
    public function more($value): bool
    {
        return $this->comp($value) == 1;
    }

    /**
     * @param string|Money $value
     * @return bool
     */
    public function equals($value): bool
    {
        return $this->comp($value) == 0;
    }

    /**
     * @param string $operator
     * @param $value
     * @return string
     * @throws Exception
     */
    protected function operation(string $operator, $value): string
    {
        $newValue = null;

        if ($value instanceof Money) {
            $value = $value->getValue();
        }

        switch ($operator) {
            case 'add':
                $newValue = bcadd($this->value, $value, self::SCALE);
                break;
            case 'div':
                $newValue = bcdiv($this->value, $value, self::SCALE);
                break;
            case 'mul':
                $newValue = bcmul($this->value, $value, self::SCALE);
                break;
            case 'sub':
                $newValue = bcsub($this->value, $value, self::SCALE);
                break;
            default:
                throw new Exception('Unsupported operator ' . $operator);
        }

        return $newValue;
    }
}