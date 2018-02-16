<?php

namespace bulldozer\catalog\common\ar;

use bulldozer\catalog\common\enums\PropertyTypesEnum;
use bulldozer\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%catalog_product_property_values}}".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $property_id
 * @property string $value
 *
 * @property Product $product
 * @property Property $property
 * @property string $enumValue
 */
class ProductPropertyValue extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%catalog_product_property_values}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getProperty(): ActiveQuery
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @return null|string
     */
    public function getEnumValue(): ?string
    {
        if ($this->property->type == PropertyTypesEnum::TYPE_ENUM) {
            foreach ($this->property->enums as $enum) {
                if ($enum->id == $this->value) {
                    return $enum->value;
                }
            }
        }

        return null;
    }
}
