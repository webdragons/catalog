<?php

namespace bulldozer\catalog\backend\forms;

use bulldozer\base\Form;
use bulldozer\catalog\common\ar\PropertyGroup;
use bulldozer\catalog\common\enums\PropertyTypesEnum;
use Yii;

/**
 * Class PropertyForm
 * @package bulldozer\catalog\backend\forms
 */
class PropertyForm extends Form
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $type;

    /**
     * @var int
     */
    public $multiple;

    /**
     * @var int
     */
    public $sort;

    /**
     * @var int
     */
    public $group_id;

    /**
     * @var int
     */
    public $filtered;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string'],

            ['type', 'required'],
            ['type', 'in', 'range' => array_keys(PropertyTypesEnum::listData())],

            ['multiple', 'boolean'],

            ['sort', 'required'],
            ['sort', 'integer'],

            ['group_id', 'in', 'range' => PropertyGroup::find()->asArray()->select(['id'])->column()],

            ['filtered', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('catalog', 'Name'),
            'type' => Yii::t('catalog', 'Type'),
            'multiple' => Yii::t('catalog', 'Multiple'),
            'sort' => Yii::t('catalog', 'Display order'),
            'group_id' => Yii::t('catalog', 'Group'),
            'filtered' => Yii::t('catalog', 'Available in filter'),
        ];
    }

    /**
     * @return array
     */
    public function getSavedAttributes(): array
    {
        return [
            'name',
            'type',
            'multiple',
            'sort',
            'group_id',
            'filtered',
        ];
    }
}