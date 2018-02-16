<?php

namespace bulldozer\catalog\common\ar;

use bulldozer\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%catalog_product_prices}}".
 *
 * @property integer $id
 * @property integer $price_id
 * @property integer $product_id
 * @property string $value
 *
 * @property Price $priceType
 */
class ProductPrice extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%catalog_product_prices}}';
    }

    public function getPriceType(): ActiveQuery
    {
        return $this->hasOne(Price::class, ['id' => 'price_id']);
    }
}
