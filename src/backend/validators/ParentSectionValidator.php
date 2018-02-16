<?php

namespace bulldozer\catalog\backend\validators;

use bulldozer\catalog\common\ar\Section;
use Yii;
use yii\base\Model;
use yii\validators\Validator;

/**
 * Class ParentSectionValidator
 * @package bulldozer\catalog\validators
 */
class ParentSectionValidator extends Validator
{
    /**
     * @var boolean
     */
    public $skipOnEmpty = false;

    /**
     * @var integer
     */
    public $section_id;

    /**
     * @param Model $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute): void
    {
        if ($model->$attribute !== null) {
            if ($model->$attribute != 0 && !Section::findOne($model->$attribute)) {
                $model->addError($attribute, Yii::t('catalog', 'Section not found'));
            }

            $section = Section::findOne($this->section_id);

            if ($section !== null) {
                $child_section_ids = $section->children()->select('id')->asArray()->column();

                if (in_array($model->$attribute, $child_section_ids)) {
                    $model->addError($attribute, Yii::t('catalog', 'You can not move a partition to a child.'));
                }

                if ($model->$attribute == $this->section_id) {
                    $model->addError($attribute, Yii::t('catalog', 'You can not transfer the partition to itself.'));
                }
            }
        }
    }
}