<?php

namespace bulldozer\catalog\common\ar;

use bulldozer\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%catalog_properties_enum}}".
 *
 * @property integer $id
 * @property integer $property_id
 * @property string $value
 *
 * @property Property $property
 */
class PropertyEnum extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%catalog_properties_enum}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getProperty(): ActiveQuery
    {
        return $this->hasOne(Property::class, ['id' => 'property_id']);
    }
}
