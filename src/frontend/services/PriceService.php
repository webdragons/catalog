<?php

namespace bulldozer\catalog\frontend\services;

use bulldozer\catalog\common\ar\Price;

/**
 * Class PriceService
 * @package bulldozer\catalog\frontend\services
 */
class PriceService
{
    /**
     * @return Price
     */
    public function getCurrentPriceType(): Price
    {
        return Price::find()->one();
    }
}