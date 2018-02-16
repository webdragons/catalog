<?php

namespace bulldozer\catalog\backend\validators;

use bulldozer\catalog\common\ar\Price;
use Yii;
use yii\validators\Validator;

/**
 * Class PricesValidator
 * @package bulldozer\catalog\backend\validators
 */
class PricesValidator extends Validator
{
    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $prices_ids = Price::find()->asArray()->select(['id'])->column();
        $base_price = Price::find()->where(['base' => 1])->one();

        if (is_array($model->$attribute)) {
            foreach ($model->$attribute as $price_id => $value) {
                $form_attr = $attribute/* . '[' . $price_id . ']'*/;

                if (!in_array($price_id, $prices_ids)) {
                    $this->addError($model, $form_attr, Yii::t('catalog', 'This price does not exist'));
                }

                if (is_numeric($value)) {
                    if ($value <= 0) {
                        $this->addError($model, $form_attr, Yii::t('catalog', 'The price must be greater than zero'));
                    }
                } else if ($value != '' && $value != null) {
                    $this->addError($model, $form_attr, Yii::t('catalog', 'The price must be a number'));
                } else if ($price_id == $base_price->id) {
                    $this->addError($model, $form_attr, Yii::t('catalog', 'The base price is obligatory for filling'));
                } else {
                    unset($model->$attribute[$price_id]);
                }
            }
        } else {
            $this->addError($model, $attribute, Yii::t('catalog', 'Is not array'));
        }
    }
}