<?php

namespace bulldozer\catalog\common\ar;

use bulldozer\db\ActiveRecord;

/**
 * This is the model class for table "{{%catalog_discounts}}".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $price_id
 * @property string $value
 */
class Discount extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%catalog_discounts}}';
    }
}
