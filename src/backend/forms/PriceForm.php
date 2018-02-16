<?php

namespace bulldozer\catalog\backend\forms;

use bulldozer\base\Form;
use bulldozer\catalog\common\ar\Price;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class PriceForm
 * @package bulldozer\catalog\backend\controllers
 */
class PriceForm extends Form
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $base;

    /**
     * @var int
     */
    public $currency_id;

    /**
     * @var int
     */
    public $priceForCopy;

    /**
     * @var int
     */
    public $extraCharge;

    /**
     * @var integer
     */
    private $id;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['base', 'boolean'],

            ['currency_id', 'required'],
            ['currency_id', 'integer'],

            ['priceForCopy', 'in', 'range' => ArrayHelper::getColumn(Price::find()->all(), 'id')],

            ['extraCharge', 'number'],

            ['name', 'required'],
            ['name', 'string', 'max' => 255],
            ['name', 'nameValidator'],
        ];
    }

    /**
     * @param string $attribute
     */
    public function nameValidator(string $attribute): void
    {
        $price = Price::findOne(['name' => $this->$attribute]);

        if ($price && ($this->id === null || $this->id != $price->id)) {
            $this->addError($attribute, 'Такое название уже занято');
        }
    }

    /**
     * @return array
     */
    public function getSavedAttributes(): array
    {
        return [
            'name',
            'base',
            'currency_id',
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('catalog', 'Name'),
            'base' => Yii::t('catalog', 'Base'),
            'currency_id' => Yii::t('catalog', 'Currency'),
            'priceForCopy' => Yii::t('catalog', 'Copy prices from'),
            'extraCharge' => Yii::t('catalog', 'Markup'),
        ];
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}