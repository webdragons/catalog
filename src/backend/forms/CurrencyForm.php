<?php

namespace bulldozer\catalog\backend\forms;

use bulldozer\base\Form;
use Yii;

/**
 * Class CurrencyForm
 * @package bulldozer\catalog\backend\forms
 */
class CurrencyForm extends Form
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $short_name;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 255],

            ['short_name', 'required'],
            ['short_name', 'string', 'max' => 10],

            ['code', 'required'],
            ['code', 'string', 'max' => 3],
            ['code', 'match', 'pattern' => '/^[A-Z]+$/', 'message' => Yii::t('catalog', 'The code must be in ISO 4217 format (https://ru.wikipedia.org/wiki/ISO_4217)')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('catalog', 'Name'),
            'short_name' => Yii::t('catalog', 'Short name'),
            'code' => Yii::t('catalog', 'Code'),
        ];
    }

    /**
     * @return array
     */
    public function getSavedAttributes(): array
    {
        return [
            'name',
            'code',
            'short_name',
        ];
    }
}