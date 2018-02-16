<?php

namespace bulldozer\catalog\backend\services;

use bulldozer\App;
use bulldozer\catalog\backend\forms\PropertyEnumForm;
use bulldozer\catalog\backend\forms\PropertyForm;
use bulldozer\catalog\common\ar\Property;
use bulldozer\catalog\common\ar\PropertyEnum;
use yii\base\Exception;

/**
 * Class PropertyService
 * @package bulldozer\catalog\backend\services
 */
class PropertyService
{
    /**
     * @param Property|null $property
     * @return PropertyForm
     * @throws \yii\base\InvalidConfigException
     */
    public function getForm(?Property $property = null): PropertyForm
    {
        /** @var PropertyForm $form */
        $form = App::createObject([
            'class' => PropertyForm::class,
        ]);

        if ($property) {
            $form->setAttributes($property->getAttributes($form->getSavedAttributes()));
        } else {
            $lastProperty = Property::find()->orderBy(['sort' => SORT_DESC])->one();

            if ($lastProperty) {
                $form->sort = $lastProperty->sort + 100;
            } else {
                $form->sort = 100;
            }
        }

        return $form;
    }

    /**
     * @param PropertyEnum|null $propertyEnum
     * @return PropertyEnumForm
     * @throws \yii\base\InvalidConfigException
     */
    public function getPropertyEnumForm(?PropertyEnum $propertyEnum = null): PropertyEnumForm
    {
        /** @var PropertyEnumForm $form */
        $form = App::createObject([
            'class' => PropertyEnumForm::class,
        ]);

        if ($propertyEnum) {
            $form->setAttributes($propertyEnum->getAttributes($form->getSavedAttributes()));
        }

        return $form;
    }

    /**
     * @param PropertyForm $form
     * @param Property|null $property
     * @return Property
     * @throws Exception
     */
    public function save(PropertyForm $form, ?Property $property = null): Property
    {
        if ($property === null) {
            $property = App::createObject([
                'class' => Property::class,
            ]);
        }

        $property->setAttributes($form->getAttributes($form->getSavedAttributes()));

        if ($property->save()) {
            return $property;
        }

        throw new Exception('Cant save property. Errors: ' . json_encode($property->getErrors()));
    }

    /**
     * @param PropertyEnumForm $form
     * @param int $propertyId
     * @param PropertyEnum|null $propertyEnum
     * @return PropertyEnum
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function savePropertyEnum(PropertyEnumForm $form, int $propertyId, ?PropertyEnum $propertyEnum = null): PropertyEnum
    {
        if ($propertyEnum === null) {
            $propertyEnum = App::createObject([
                'class' => PropertyEnum::class,
            ]);
        }

        $propertyEnum->setAttributes($form->getAttributes($form->getSavedAttributes()));
        $propertyEnum->property_id = $propertyId;

        if ($propertyEnum->save()) {
            return $propertyEnum;
        }

        throw new Exception('Cant save property. Errors: ' . json_encode($propertyEnum->getErrors()));
    }
}