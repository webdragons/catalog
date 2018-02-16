<?php

namespace bulldozer\catalog\backend\services;

use bulldozer\App;
use bulldozer\catalog\backend\forms\PropertyGroupForm;
use bulldozer\catalog\common\ar\PropertyGroup;
use yii\base\Exception;

/**
 * Class PropertyGroupService
 * @package bulldozer\catalog\backend\services
 */
class PropertyGroupService
{
    /**
     * @param PropertyGroup|null $propertyGroup
     * @return PropertyGroupForm
     * @throws \yii\base\InvalidConfigException
     */
    public function getForm(?PropertyGroup $propertyGroup = null): PropertyGroupForm
    {
        /** @var PropertyGroupForm $form */
        $form = App::createObject([
            'class' => PropertyGroupForm::class,
        ]);

        if ($propertyGroup) {
            $form->setAttributes($propertyGroup->getAttributes($form->getSavedAttributes()));
        } else {
            $lastPropertyGroup = PropertyGroup::find()->orderBy(['sort' => SORT_DESC])->one();

            if ($lastPropertyGroup) {
                $form->sort = $lastPropertyGroup->sort + 100;
            } else {
                $form->sort = 100;
            }
        }

        return $form;
    }

    /**
     * @param PropertyGroupForm $form
     * @param PropertyGroup|null $propertyGroup
     * @return PropertyGroup
     * @throws \yii\base\InvalidConfigException
     * @throws Exception
     */
    public function save(PropertyGroupForm $form, ?PropertyGroup $propertyGroup = null): PropertyGroup
    {
        if ($propertyGroup === null) {
            $propertyGroup = App::createObject([
                'class' => PropertyGroup::class,
            ]);
        }

        $propertyGroup->setAttributes($form->getAttributes($form->getSavedAttributes()));

        if ($propertyGroup->save()) {
            return $propertyGroup;
        }

        throw new Exception('Cant save property group. Errors: ' . json_encode($propertyGroup->getErrors()));
    }
}