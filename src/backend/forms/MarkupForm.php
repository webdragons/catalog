<?php

namespace bulldozer\catalog\backend\forms;

use bulldozer\base\Form;
use bulldozer\catalog\common\ar\Price;
use bulldozer\catalog\common\ar\Section;
use Yii;

/**
 * Class MarkupForm
 * @package bulldozer\catalog\backend\forms
 */
class MarkupForm extends Form
{
    /**
     * @var int
     */
    public $price_id;

    /**
     * @var array
     */
    public $sections = [];

    /**
     * @var int
     */
    public $type;

    /**
     * @var float
     */
    public $value;

    /**
     * @var int
     */
    public $round;

    /**
     * @var int
     */
    public $updateDiscounts;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['price_id', 'required'],
            ['price_id', 'in', 'range' => Price::find()->asArray()->select(['id'])->column()],

            ['sections', 'productSectionsValidator'],

            ['type', 'required'],
            ['type', 'in', 'range' => array_keys($this->getTypes())],

            ['value', 'required'],
            ['value', 'number'],

            ['round', 'boolean'],

            ['updateDiscounts', 'boolean'],
        ];
    }

    /**
     * @param string $attribute
     */
    public function productSectionsValidator(string $attribute)
    {
        $sections = Section::find()->andWhere(['id' => $this->$attribute])->asArray()->select(['id'])->column();

        if (count($sections) != count($this->$attribute)) {
            $this->addError($attribute, Yii::t('catalog', 'Somebody sections not found'));
        }
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return [
            1 => Yii::t('catalog', 'Percent'),
            2 => Yii::t('catalog', 'Value'),
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'price_id' => Yii::t('catalog', 'Price type'),
            'sections' => Yii::t('catalog', 'Sections'),
            'type' => Yii::t('catalog', 'Type'),
            'value' => Yii::t('catalog', 'Value'),
            'round' => Yii::t('catalog', 'Round'),
            'updateDiscounts' => Yii::t('catalog', 'Recalculate discount'),
        ];
    }
}