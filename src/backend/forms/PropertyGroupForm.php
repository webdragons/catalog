<?php

namespace bulldozer\catalog\backend\forms;

use bulldozer\base\Form;
use Yii;

/**
 * Class PropertyGroupForm
 * @package bulldozer\catalog\backend\forms
 */
class PropertyGroupForm extends Form
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var integer
     */
    public $sort;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string'],

            ['sort', 'required'],
            ['sort', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('catalog', 'Name'),
            'sort' => Yii::t('catalog', 'Display order'),
        ];
    }

    /**
     * @return array
     */
    public function getSavedAttributes(): array
    {
        return [
            'name',
            'sort',
        ];
    }
}