<?php

namespace bulldozer\catalog\backend\validators;

use bulldozer\catalog\common\ar\Price;
use Yii;
use yii\validators\Validator;

/**
 * Class DiscountsValidator
 * @package bulldozer\catalog\backend\validators
 */
class DiscountsValidator extends Validator
{
    /**
     * @param \yii\base\Model $model
     * @param string $attribute.
     */
    public function validateAttribute($model, $attribute)
    {
        $prices_ids = Price::find()->asArray()->select(['id'])->column();

        foreach ($model->$attribute as $price_id => $value) {
            if (!in_array($price_id, $prices_ids)) {
                $this->addError($model, $attribute, Yii::t('catalog', 'This price does not exist'));
            }

            if (is_numeric($value)) {
                if ($value <= 0) {
                    $this->addError($model, $attribute, Yii::t('catalog', 'Discount must be greater than zero'));
                }

                if (!isset($model->prices[$price_id]) || $model->prices[$price_id] <= $value || $model->prices[$price_id] < 1) {
                    $this->addError($model, $attribute, Yii::t('catalog', 'Discount can not be more than the basic price'));
                }
            } else if ($value != '' && $value != null) {
                $this->addError($model, $attribute, Yii::t('catalog', 'Discount must be a number'));
            } else {
                unset($model->$attribute[$price_id]);
            }
        }
    }
}