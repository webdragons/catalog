<?php

namespace bulldozer\catalog\frontend\widgets\forms;

use bulldozer\base\Form;
use Yii;

/**
 * Class ItemsPerPageForm
 * @package bulldozer\catalog\frontend\widgets\forms
 */
class ItemsPerPageForm extends Form
{
    /**
     * @var integer
     */
    public $itemsPerPage;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['itemsPerPage', 'required'],
            ['itemsPerPage', 'integer'],
            ['itemsPerPage', 'in', 'range' => self::getCounts()],
        ];
    }

    /**
     * @return array
     */
    public static function getCounts(): array
    {
        return [
            24 => 24,
            32 => 32,
            48 => 48
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'itemsPerPage' => Yii::t('catalog', 'Products per page'),
        ];
    }
}