<?php

namespace bulldozer\catalog\backend\services;

use bulldozer\App;
use bulldozer\catalog\backend\forms\CurrencyForm;
use bulldozer\catalog\common\ar\Currency;
use yii\base\Exception;

/**
 * Class CurrencyService
 * @package bulldozer\catalog\backend\services
 */
class CurrencyService
{
    /**
     * @param Currency|null $currency
     * @return CurrencyForm
     * @throws \yii\base\InvalidConfigException
     */
    public function getForm(?Currency $currency = null): CurrencyForm
    {
        /** @var CurrencyForm $currencyForm */
        $currencyForm = App::createObject([
            'class' => CurrencyForm::class,
        ]);

        if ($currency !== null) {
            $currencyForm->setAttributes($currency->getAttributes($currencyForm->getSavedAttributes()));
        }

        return $currencyForm;
    }

    /**
     * @param CurrencyForm $currencyForm
     * @param Currency|null $currency
     * @return Currency
     * @throws \yii\base\InvalidConfigException
     * @throws Exception
     */
    public function save(CurrencyForm $currencyForm, ?Currency $currency = null): Currency
    {
        if ($currency === null) {
            $currency = App::createObject([
                'class' => Currency::class,
            ]);
        }

        $currency->setAttributes($currencyForm->getAttributes($currencyForm->getSavedAttributes()));

        if ($currency->save()) {
            return $currency;
        }

        throw new Exception('Cant save currency. Errors: ' . json_encode($currency->getErrors()));
    }
}