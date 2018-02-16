<?php

namespace bulldozer\catalog\common\ar;

use bulldozer\catalog\common\enums\PropertyTypesEnum;
use bulldozer\catalog\common\traits\UsersRelationsTrait;
use bulldozer\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%catalog_properties}}".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $creator_id
 * @property integer $updater_id
 * @property string $name
 * @property integer $type
 * @property integer $multiple
 * @property integer $sort
 * @property integer $group_id
 * @property integer $filtered
 *
 * @property PropertyEnum[] $enums
 * @property PropertyGroup $group
 * @property SectionProperty[] $sectionProperties
 * @property Section[] $sections
 * @property-read array $valueVariants
 */
class Property extends ActiveRecord
{
    use UsersRelationsTrait;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%catalog_properties}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'creator_id',
                'updatedByAttribute' => 'updater_id',
            ],
        ];
    }

    /**
     * @return string
     */
    public function getTypeName(): ?string
    {
        return PropertyTypesEnum::getLabel($this->type);
    }

    /**
     * @return ActiveQuery
     */
    public function getEnums(): ActiveQuery
    {
        return $this->hasMany(PropertyEnum::className(), ['property_id' => 'id'])->addOrderBy(['value' => SORT_ASC]);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(PropertyGroup::class, ['id' => 'group_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSectionProperties(): ActiveQuery
    {
        return $this->hasMany(SectionProperty::class, ['property_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSections(): ActiveQuery
    {
        return $this->hasMany(Section::class, ['id' => 'section_id'])->via('sectionProperties');
    }

    /**
     * @return array
     */
    public function getValueVariants(): array
    {
        $propertyValues = ProductPropertyValue::find()
            ->where(['property_id' => $this->id])
            ->groupBy(['value'])
            ->all();

        $values = [];

        foreach ($propertyValues as $propertyValue) {
            $values[$propertyValue->value] = $propertyValue->value;
        }

        asort($values);

        return $values;
    }
}
