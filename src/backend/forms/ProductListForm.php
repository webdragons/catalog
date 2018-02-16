<?php

namespace bulldozer\catalog\backend\forms;

use bulldozer\base\Form;
use bulldozer\catalog\common\ar\Product;
use Yii;

/**
 * Class ProductListForm
 * @package bulldozer\catalog\backend\forms
 */
class ProductListForm extends Form
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $active;

    /**
     * @var string
     */
    public $more_url;

    /**
     * @var array
     */
    public $products = [];

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['active', 'integer'],

            ['name', 'required'],
            ['name', 'string', 'max' => 255],

            ['more_url', 'string', 'max' => 600],

            ['products', 'productsValidator'],
        ];
    }

    /**
     * @param $attribute
     */
    public function productsValidator($attribute): void
    {
        $products = Product::find()->where(['id' => $this->$attribute])->all();

        if (count($products) != count($this->$attribute)) {
            $this->addError($attribute, Yii::t('catalog', 'Product(s) not found'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'active' => Yii::t('catalog', 'Active'),
            'name' => Yii::t('catalog', 'Name'),
            'more_url' => Yii::t('catalog', 'More link'),
            'products' => Yii::t('catalog', 'Products'),
        ];
    }

    /**
     * @return array
     */
    public function getSavedAttributes(): array
    {
        return [
            'name',
            'active',
            'more_url',
        ];
    }
}