<?php

namespace bulldozer\catalog\frontend\ar;

use bulldozer\App;
use bulldozer\catalog\common\ar\Discount;
use bulldozer\catalog\common\ar\ProductPrice;
use bulldozer\catalog\frontend\entities\Price;
use bulldozer\catalog\frontend\services\PriceService;
use yii\db\ActiveQuery;
use yii\helpers\Url;

/**
 * Class Product
 * @package bulldozer\catalog\frontend\ar
 *
 * @property-read ProductPrice $arPrice
 */
class Product extends \bulldozer\catalog\common\ar\Product
{
    /**
     * @var Price
     */
    private $_price;

    /**
     * @var bool
     */
    private $priceInited = false;

    /**
     * @var PriceService
     */
    private $priceService;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->priceService = App::createObject([
            'class' => PriceService::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function find(): ActiveQuery
    {
        $query = parent::find();

        if (!App::$app->user->can('catalog_manage')) {
            $query->andWhere(['active' => 1]);
        }

        return $query;
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getViewUrl($full = false): string
    {
        return Url::to([
            '/catalog/products/view',
            'slug' => $this->slug,
            'section_slug' => $this->section->slug
        ], $full);
    }

    /**
     * @return string
     */
    public function getFullViewUrl(): string
    {
        return $this->getViewUrl(true);
    }

    /**
     * @return ActiveQuery
     */
    public function getArPrice(): ActiveQuery
    {
        $priceType = $this->priceService->getCurrentPriceType();

        if ($priceType) {
            return $this->hasOne(ProductPrice::class, ['product_id' => 'id'])->andOnCondition(['price_id' => $priceType->id]);
        } else {
            return $this->hasOne(ProductPrice::class, ['product_id' => 'id'])->andOnCondition(['price_id' => 0]);
        }
    }

    /**
     * @return ActiveQuery
     */
    public function getArDiscount(): ActiveQuery
    {
        $priceType = $this->priceService->getCurrentPriceType();

        if ($priceType) {
            return $this->hasOne(Discount::class, ['product_id' => 'id'])->andOnCondition(['price_id' => $priceType->id]);
        } else {
            return $this->hasOne(Discount::class, ['product_id' => 'id'])->andOnCondition(['price_id' => 0]);
        }
    }

    /**
     * @return Price
     * @throws \yii\base\Exception
     */
    public function getPrice(): ?Price
    {
        if (!$this->priceInited) {
            $priceType = $this->priceService->getCurrentPriceType();

            if ($priceType) {
                $productPrice = ProductPrice::find()->where([
                    'price_id' => $priceType->id, 'product_id' => $this->id
                ])->one();
                $productDiscount = Discount::find()->where([
                    'price_id' => $priceType->id, 'product_id' => $this->id
                ])->one();

                if ($productPrice) {
                    $this->_price = new Price($productPrice, $productDiscount);
                }
            }

            $this->priceInited = true;
        }

        return $this->_price;
    }

    /**
     * @return ActiveQuery
     */
    public function getSection(): ActiveQuery
    {
        return $this->hasOne(Section::class, ['id' => 'section_id']);
    }
}