<?php

namespace bulldozer\catalog\common\ar;

use bulldozer\db\ActiveRecord;

/**
 * This is the model class for table "{{%catalog_section_properties}}".
 *
 * @property integer $id
 * @property integer $section_id
 * @property integer $property_id
 */
class SectionProperty extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%catalog_section_properties}}';
    }
}
