<?php

namespace bulldozer\catalog\backend\services;

use bulldozer\App;
use bulldozer\catalog\backend\forms\MarkupForm;
use bulldozer\catalog\backend\forms\PriceForm;
use bulldozer\catalog\common\ar\Discount;
use bulldozer\catalog\common\ar\Price;
use bulldozer\catalog\common\ar\Product;
use bulldozer\catalog\common\ar\ProductPrice;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Class PriceService
 * @package bulldozer\catalog\backend\services
 */
class PriceService
{
    const TYPE_PERCENT = 1;
    const TYPE_FIXED = 2;

    /**
     * @param MarkupForm $markupForm
     * @return bool
     * @throws \yii\db\Exception
     */
    public function markup(MarkupForm $markupForm): bool
    {
        $transaction = App::$app->db->beginTransaction();

        $products = Product::find()->andWhere(['section_id' => $markupForm->sections])->all();

        if ($this->updateProductPrices($products, $markupForm->price_id, $markupForm->type, $markupForm->value,
                $markupForm->round == 1, $markupForm->updateDiscounts == 1)) {
            $transaction->commit();

            return true;
        }

        $transaction->rollBack();

        return false;
    }

    /**
     * @param Price|null $price
     * @return PriceForm
     * @throws \yii\base\InvalidConfigException
     */
    public function getPriceForm(?Price $price = null): PriceForm
    {
        /** @var PriceForm $form */
        $form = App::createObject([
            'class' => PriceForm::class,
        ]);

        if ($price !== null) {
            $form->setAttributes($price->getAttributes($form->getSavedAttributes()));
            $form->setId($price->id);
        }

        return $form;
    }

    /**
     * @param PriceForm $priceForm
     * @param Price|null $price
     * @return Price|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function save(PriceForm $priceForm, Price $price = null): Price
    {
        $transaction = App::$app->db->beginTransaction();

        if ($price === null) {
            $price = App::createObject([
                'class' => Price::class,
            ]);
        }

        $price->setAttributes($priceForm->getAttributes($priceForm->getSavedAttributes()));

        if ($price->save()) {
            $this->saveProductPrices($priceForm, $price);
            $this->saveDiscounts($priceForm, $price);

            $transaction->commit();

            return $price;
        } else {
            throw new Exception('Cant save price. Errors: ' . json_encode($price->getErrors()));
        }
    }

    /**
     * @param PriceForm $priceForm
     * @param Price $price
     * @throws Exception
     * @throws InvalidConfigException
     */
    protected function saveProductPrices(PriceForm $priceForm, Price $price): void
    {
        if ($priceForm->priceForCopy) {
            /** @var ProductPrice[] $productPrices */
            $productPrices = ProductPrice::find()->where(['price_id' => $priceForm->priceForCopy])->all();

            foreach ($productPrices as $productPrice) {
                /** @var ProductPrice $newProductPrice */
                $newProductPrice = App::createObject([
                    'class' => ProductPrice::class,
                    'product_id' => $productPrice->product_id,
                    'price_id' => $price->id,
                ]);

                if ($priceForm->extraCharge != 0) {
                    $newValue = (float)$productPrice->value
                        + (float)$productPrice->value * $priceForm->extraCharge / 100;
                    $newProductPrice->value = ceil($newValue / 100) * 100 - 10;

                    if ($newProductPrice->value < 0) {
                        $newProductPrice->value = 0;
                    }
                } else {
                    $newProductPrice->value = $productPrice->value;
                }

                if (!$newProductPrice->save()) {
                    throw new Exception('Cant save product price. Errors: ' . json_encode($newProductPrice->getErrors()));
                }
            }
        }
    }

    /**
     * @param PriceForm $priceForm
     * @param Price $price
     * @throws Exception
     * @throws InvalidConfigException
     */
    protected function saveDiscounts(PriceForm $priceForm, Price $price): void
    {
        if ($priceForm->priceForCopy) {
            /** @var Discount[] $discounts */
            $discounts = Discount::find()->where(['price_id' => $priceForm->priceForCopy])->all();

            foreach ($discounts as $discount) {
                /** @var Discount $newDiscount */
                $newDiscount = App::createObject([
                    'class' => Discount::class,
                    'price_id' => $price->id,
                    'product_id' => $discount->object_id,
                ]);

                if ($priceForm->extraChargeProducts != 0) {
                    $newValue = (float)$discount->value
                        + (float)$discount->value * $priceForm->extraCharge / 100;
                    $newDiscount->value = ceil($newValue / 100) * 100 - 10;

                    if ($newDiscount->value < 0) {
                        $newDiscount->value = 0;
                    }
                } else {
                    $newDiscount->value = $discount->value;
                }

                if (!$newDiscount->save()) {
                    throw new Exception('Cant save discount. Errors: ' . json_encode($newDiscount->getErrors()));
                }
            }
        }
    }

    /**
     * @param Product[] $products
     * @param int $priceId
     * @param int $type
     * @param float $value
     * @param bool $round
     * @param bool $updateDiscounts
     * @return bool
     */
    protected function updateProductPrices(
        array $products,
        int $priceId,
        int $type,
        float $value,
        bool $round,
        bool $updateDiscounts
    ): bool {
        $ids = ArrayHelper::map($products, 'id', 'id');

        if (count($ids) > 0) {
            $prices = ProductPrice::find()->andWhere([
                'price_id' => $priceId,
                'product_id' => $ids,
            ])->all();

            foreach ($prices as $price) {
                $diff = 0;

                if ($type == self::TYPE_PERCENT) {
                    $diff = (float)$price->value * $value / 100;
                } elseif ($type == self::TYPE_FIXED) {
                    $diff = $value;
                }

                $price->value += $diff;

                if ($round) {
                    $price->value = ceil($price->value / 100) * 100 - 10;
                }

                if ($price->value < 0) {
                    $price->value = 0;
                }

                if (!$price->save()) {
                    return false;
                }
            }

            /** @var Discount[] $discounts */
            $discounts = Discount::find()->where([
                'price_id' => $priceId,
                'product_id' => $ids,
            ])->all();

            if ($updateDiscounts) {
                foreach ($discounts as $discount) {
                    $diff = 0;

                    if ($type == self::TYPE_PERCENT) {
                        $diff = (float)$discount->value * $value / 100;
                    } elseif ($type == self::TYPE_FIXED) {
                        $diff = $value;
                    }

                    $discount->value += $diff;

                    if ($round) {
                        $discount->value = ceil($discount->value / 100) * 100 - 10;
                    }

                    if ($discount->value < 0) {
                        $discount->value = 0;
                    }

                    if (!$discount->save()) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}