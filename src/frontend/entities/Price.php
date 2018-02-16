<?php

namespace bulldozer\catalog\frontend\entities;

use bulldozer\catalog\common\ar\Currency;
use bulldozer\catalog\common\ar\Discount;
use bulldozer\catalog\common\ar\ProductPrice;
use bulldozer\catalog\common\entities\Money;

class Price
{
    /**
     * @var Money
     */
    private $price;

    /**
     * @var Money
     */
    private $old_price;

    /**
     * @var bool
     */
    private $with_discount;

    /**
     * @var int
     */
    private $percent;

    /**
     * @var Money
     */
    private $diff;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * Price constructor.
     * @param ProductPrice $price
     * @param Discount|null $discount
     * @throws \yii\base\Exception
     */
    public function __construct(ProductPrice $price, Discount $discount = null)
    {
        $currency = $price->priceType->currency;

        if ($discount !== null) {
            $this->price = new Money($discount->value, $currency);
            $this->old_price = new Money($price->value, $currency);
            $this->percent = (int)(100 - ($this->price->getValue() / $this->old_price->getValue() * 100));
            $this->diff = $this->old_price->sub($this->price);

            $this->with_discount = true;
        } else {
            $this->price = new Money($price->value, $currency);
            $this->old_price = new Money(0);
            $this->diff = new Money(0);
            $this->percent = 0;
            $this->with_discount = false;
        }

        $this->currency = $price->priceType->currency;
    }

    /**
     * @return Money
     */
    public function getPrice(): Money
    {
        return $this->price;
    }

    /**
     * @return Money
     */
    public function getOldPrice(): Money
    {
        return $this->old_price;
    }

    /**
     * @return bool
     */
    public function isWithDiscount(): bool
    {
        return $this->with_discount;
    }

    /**
     * @return int
     */
    public function getPercent(): int
    {
        return $this->percent;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getPrintPrice()
    {
        return $this->getPrice()->getFormattedValue();
    }

    /**
     * @return string
     * @deprecated
     */
    public function getPrintOldPrice()
    {
        return $this->getOldPrice()->getFormattedValue();
    }

    /**
     * @return Money
     */
    public function getDiff(): Money
    {
        return $this->diff;
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}